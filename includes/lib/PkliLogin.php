<?php

/*  Copyright 2014  Samer Bechara  (email : sam@thoughtengineer.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

Class PkliLogin {

    const _AUTHORIZE_URL = 'https://www.linkedin.com/uas/oauth2/authorization';
    const _TOKEN_URL = 'https://www.linkedin.com/uas/oauth2/accessToken';
    const _BASE_URL = 'https://api.linkedin.com/v1';
    
    // The URI to redirect to after OAuth success
    public $redirect_uri;
    
    // LinkedIn Application Key
    public $li_api_key;
    
    // LinkedIn Application Secret
    public $li_secret_key;
    
    // Stores Access Token
    public $access_token;
    
    // Stores OAuth Object
    public $oauth;
    
    // Stores the user redirect after login
    public $user_redirect;

    public function __construct() {

        // Setup redirect uri 
        $this->redirect_uri = wp_login_url() . '?action=pkli_login';

        // Add actions
        add_action('login_form', array($this, 'display_login_button'));
        add_action('init', array($this, 'process_login'));
        
        // Set LinkedIn keys
        $li_keys = get_option('pkli_basic_options');
        $this->li_api_key = $li_keys['li_api_key'];
        $this->li_secret_key = $li_keys['li_secret_key'];
        
        // Require OAuth2 client
        require_once(PKLI_PATH . '/includes/lib/Pkli_OAuth2Client.php');

        // Create new Oauth client
        $this->oauth = new Pkli_OAuth2Client($this->li_api_key, $this->li_secret_key);
        
        // Set Oauth URLs
        $this->oauth->redirect_uri = $this->redirect_uri;
        $this->oauth->authorize_url = self::_AUTHORIZE_URL;
        $this->oauth->token_url = self::_TOKEN_URL;
	$this->oauth->api_base_url = self::_BASE_URL;
        
	// Set user token if it exists
	if (get_current_user_id()){
	    $this->oauth->access_token = get_user_meta(get_current_user_id(), 'pkli_access_token', true);
	}
        // Add shortcode for getting LinkedIn Login URL
        add_shortcode( 'wpli_login_link', array($this, 'get_login_link') );        

    }

    // Returns LinkedIn authorization URL
    public function get_auth_url($redirect = false) {

        $state = wp_generate_password(12, false);
        $authorize_url = $this->oauth->authorizeUrl(array('scope' => 'r_basicprofile r_emailaddress',
            'state' => $state));

        // Store state in database in temporarily till checked back
        $_SESSION['li_api_state'][$state] = $redirect;

        return $authorize_url;
    }

    // Returns the code for the login button
    public function get_login_button() {

        return "<a rel='nofollow' href='" . $this->get_auth_url() . "' title='".__('Connect with LinkedIn','linkedin-login')."'>
                                            <img alt='LinkedIn' title='".__('Sign-in Using LinkedIn','linkedin-login')."' src='" . plugins_url() . "/linkedin-login/includes/assets/img/linkedin.png' />
        </a>";
    }

    // Logs in a user after he has authorized his LinkedIn account
    function process_login() {
	
        // Check if this is a LinkedIn Sign-in Request
        if ( $this->is_linkedin_signin()) {            

		    // Set redirect URL
		    $this->set_redirect_url();

		    // Get profile XML response
		    $xml = $this->get_linkedin_profile();
		    
		    // Authenticate the user based on his LinkedIn profile data
		    $user_id = $this->authenticate_user($xml);		   
		    
		    // Signon user by ID
		    wp_set_auth_cookie($user_id);

		    // Store the user's access token as a meta object
		    update_user_meta($user_id,'pkli_access_token',$this->access_token,true);

		    // Update the user's data from LinkedIn
		    $this->update_user_data($xml, $user_id);

		    // Do action hook that user has authenticated his LinkedIN account for developers to hook into
		    do_action('pkli_linkedin_authenticated', $user_id);		    
		    
		    // Validate URL as absolute
		    if(filter_var($this->user_redirect, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED))
			wp_safe_redirect($this->user_redirect);
		    // Invalid redirect URL, we'll redirect to admin URL
		    else
			wp_redirect( admin_url() );		    
                
            
        }
        // Getting an error, redirect to login page
        elseif (isset($_REQUEST['error'])) {
	    wp_redirect(wp_login_url());
        }
    }

    /*
     * Get the user LinkedIN profile and return it as XML
     */
    
    private function get_linkedin_profile() {
	
	// Use GET method since POST isn't working
	$this->oauth->curl_authenticate_method = 'GET';

	// Request access token
	$response = $this->oauth->authenticate($_REQUEST['code']);
	$this->access_token = $response->{'access_token'};

	// Get first name, last name and email address, and load 
	// response into XML object
	$xml = simplexml_load_string($this->oauth->get('https://api.linkedin.com/v1/people/~:(id,first-name,last-name,email-address,summary,site-standard-profile-request)'));  
	
	return $xml;
    }
    /*
     * Checks if this is a LinkedIn sign-in request for our plugin
     */
    private function is_linkedin_signin() {
	
	// If no action is requested or the action is not ours
	if(!isset($_REQUEST['action']) || ($_REQUEST['action'] != "pkli_login") ) {
	    return false;
	}
	
	// If a code is not returned, then OAuth did not proceed properly
	if (! isset($_REQUEST['code']) )	{
	    return false;
	}
	
	// If state is not set, there might be a request forgery
	// TODO: Check if the state received is the same we sent for further security
	if ( ! isset($_SESSION['li_api_state'][$_REQUEST['state']] )) {
	    return false;
	}

	// This is a LinkedIn signing-request
	return true;
    }
    /*
     * Sets the redirect URL for the user after logging in
     */
    private function set_redirect_url() {

	// Get redirect URL
	$this->user_redirect = $_SESSION['li_api_state'][$_REQUEST['state']];

	// State should be deleted as it is no longer needed
	unset($_SESSION['li_api_state']);
	
	// Get plugin option
	$li_options = get_option('pkli_basic_options');

	// Use default redirect in case no redirect has been specified
	if( ($this->user_redirect == false)  || ($this->user_redirect == '') ){
	    $this->user_redirect = $li_options['li_redirect_url'];
	}		    

    }
    /*
     * Authenticate a user by his LinkedIn ID first, and his email address then. IF he doesn't exist, the function creates him based on his LinkedIn email address
     * 
     * @param	string	$linkedin_id	The XML response by LinkedIN
     */
    private function authenticate_user($xml) {
	
	// Logout any logged in user before we start to avoid any issues arising
	wp_logout();	
	
	// Get the user's email address
	$email = (string) $xml->{'email-address'};

	// Get the user's application-specific LinkedIn ID
	$linkedin_id = (string) $xml->{'id'};	
	
	// See if a user with the above LinkedIn ID exists in our database
	$user_by_id = get_users(array('meta_key' => 'pkli_linkedin_id',
					'meta_value' => $linkedin_id) );

	// If he exists, return his ID
	if(count($user_by_id) == 1) {

	    $user_id = $user_by_id[0]->ID;						
	    
	    return $user_id;

	}
	
	// Sign in the user if the email already exists
	elseif(email_exists($email)){

	    // Get the user ID by email
	    $user = get_user_by('email',$email);

	    $user_id = $user->ID;
	    
	    return $user_id;


	}

	// User is signing in for the first time 
	elseif(is_email($email)) {

	    // Create user
	    $user_id = wp_create_user( $email, wp_generate_password(16), $email );
	    
		
	    // Get plugin option
	    $li_options = get_option('pkli_basic_options');

	    // Set the user redirect URL
	    $this->user_redirect = $li_options['li_registration_redirect_url'];


	    return $user_id;
	    	   

	}		  	
	
	// Does not exist, return false
	return false;
    }
    
    public function display_login_button() {
        echo $this->get_login_button();
    }
    
    // Display login error
    private function get_login_error($email){
        
        // Data has been marked as private inside LinkedIn user's account
        if($email== 'private')
            $error = __('We are unable to sign you in, since you have not allowed API'
                . ' access to your email address. Please register manually','linkedin-login');
        else
            $error = __('It seems that your application does not have proper scope permissions.
                Please visit your application page on linkedin, and make sure r_emailaddress is checked under Scope','linkedin-login');
        return '<div id="login_error">	<strong>ERROR</strong>: '.$error.'<br />
</div>';
    }
    
    // Used by shortcode in order to get the login link
    public function get_login_link($attributes = false){
        
	    // extract data from array
	    extract( shortcode_atts( array('text' => 'Login With LinkedIn', 'img' => PKLI_URL.'includes/assets/img/linkedin-button.png', 'redirect' => '' , 'class' => ''), $attributes ) );

	    $auth_url = $this->get_auth_url($redirect);

	    // User has specified an image
	    if(isset($attributes['img']) ){
		return "<a href='".$auth_url."' class='$class'><img src='".$img."' /></a>";
	    }
	    
	    // User has specified text
	    if(isset($attributes['text']) ){
		return "<a href='".$auth_url."' class='$class'>".$text."</a>";
	    }
	    
	    // Default fields
	    return "<a href='".$auth_url."' class='$class'><img src='".$img."' /></a>";
	    
	        
    }
    
    // Updates the user's wordpress data based on his LinkedIn data
    private function update_user_data($xml,$user_id=false){
	$first_name = (string) $xml->{'first-name'};
	$last_name = (string) $xml->{'last-name'};
	$description = (string) $xml->{'summary'};
	$linkedin_url = (string) $xml->{'site-standard-profile-request'}->url;
	$linkedin_id = (string) $xml->{'id'};
	
	if(!$user_id){
	    $user_id = get_current_user_id();
	}
	// Update user data in database
	$result = wp_update_user(array('ID' => $user_id, 'first_name' => $first_name, 'last_name' => $last_name, 'description' => $description, 'user_url' => $linkedin_url));
	
	// Store LinkedIn ID in database
	update_user_meta($user_id, 'pkli_linkedin_id', $linkedin_id);
	
	// Store all profile fields as metadata values
	return $result;
    }

}
