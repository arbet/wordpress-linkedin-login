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

    // LinkedIn Application Key
    public $li_api_key;

    // LinkedIn Application Secret
    public $li_secret_key;

    // Stores Access Token
    public $access_token;

    // Stores OAuth Object
    public $oauth;

    // Stores the user redirect after login
    public $user_redirect = false;

    // Stores our LinkedIn options 
    public $li_options;

    public function __construct() {

        // This action displays the LinkedIn Login button on the default WordPress Login Page
        add_action('login_form', array($this, 'display_login_button'));

        // This action processes any LinkedIn Login requests
        add_action('init', array($this, 'process_login'));
        
        add_action( 'admin_enqueue_scripts', array($this, 'admin_enqueue') );
        
        // Get plugin options
        $this->li_options = get_option('pkli_basic_options');

        // Set LinkedIn keys class variables - These will be used throughout the class
        $this->li_api_key = !empty($this->li_options['li_api_key']) ? $this->li_options['li_api_key'] : '';
        $this->li_secret_key = !empty($this->li_options['li_secret_key']) ? $this->li_options['li_secret_key'] : '';

        // Require OAuth2 client to process authentications
        require_once(PKLI_PATH . '/includes/lib/Pkli_OAuth2Client.php');

        // Create new Oauth client
        $this->oauth = new Pkli_OAuth2Client($this->li_api_key, $this->li_secret_key);

        // Set Oauth URLs
        $this->oauth->redirect_uri = wp_login_url() . '?action=pkli_login';
        $this->oauth->authorize_url = self::_AUTHORIZE_URL;
        $this->oauth->token_url = self::_TOKEN_URL;
        $this->oauth->api_base_url = self::_BASE_URL;

        // Set user token if user is logged in
        if (get_current_user_id()) {
            $this->oauth->access_token = get_user_meta(get_current_user_id(), 'pkli_access_token', true);
        }
        // Add shortcode for getting LinkedIn Login URL 
        add_shortcode('wpli_locked_content', array($this, 'get_login_link'));

        // Start session
        if (!session_id()) {
            session_start();
        }

    }

    // Returns LinkedIn authorization URL
    public function get_auth_url($redirect = false) {
        
        $state = wp_generate_password(12, false);
        
        //'r_basicprofile' and 'r_emailaddress' are default values
        require_once (PKLI_PATH.'/includes/lib/class-pkli-scopes.php');
        $def_scopes = array(Pkli_Scopes::READ_BASIC_PROFILE, Pkli_Scopes::READ_EMAIL_ADDRESS);
        $this->li_options['li_list_scopes'] = (isset($this->li_options['li_list_scopes']) && is_array($this->li_options['li_list_scopes'])) ? implode(' ', array_unique(array_merge($this->li_options['li_list_scopes'], $def_scopes))) : implode(' ', $def_scopes);

        $authorize_url = $this->oauth->authorizeUrl(array('scope' => $this->li_options['li_list_scopes'],
            'state' => $state));

        // Store state in database in temporarily till checked back
        if (!isset($_SESSION['li_api_state'])) {
            $_SESSION['li_api_state'] = $state;
        }

        // Store redirect URL in session
        $_SESSION['li_api_redirect'] = $redirect;

        return $authorize_url;

    }

    // This function displays the login button on the default WP login page
    public function display_login_button() {

        // User is not logged in, display login button
        echo "<p><a rel='nofollow' href='" . $this->get_auth_url() . "'>
                                            <img alt='LinkedIn' src='" . PKLI_URL . "includes/assets/img/linkedin-button.png' />
        </a></p>";

    }
    
    //Enqueue styles and scripts
    function admin_enqueue(){
        wp_enqueue_style( 'li-admin-css', PKLI_URL . 'includes/assets/css/admin.css' );
    }

    // Logs in a user after he has authorized his LinkedIn account
    function process_login() {

        // If this is not a linkedin sign-in request, do nothing
        if (!$this->is_linkedin_signin()) {
            return;
        }

        // If this is a user sign-in request, but the user denied granting access, redirect to login URL
        if (isset($_REQUEST['error']) && $_REQUEST['error'] == 'access_denied') {

            // Get our cancel redirect URL
            $cancel_redirect_url = $this->li_options['li_cancel_redirect_url'];

            // Redirect to login URL if left blank
            if (empty($cancel_redirect_url)) {
                wp_redirect(wp_login_url());
            }

            // Redirect to our given URL
            wp_safe_redirect($cancel_redirect_url);
        }

        // Another error occurred, create an error log entry
        if (isset($_REQUEST['error'])) {
            $error = $_REQUEST['error'];
            $error_description = $_REQUEST['error_description'];
            error_log("WP_LinkedIn Login Error\nError: $error\nDescription: $error_description");
        }


        // Get profile XML response
        $xml = $this->get_linkedin_profile();

        // Returns the user's WordPress ID after setting proper redirect URL
        $user_id = $this->authenticate_user($xml);

        $remember = $this->li_options['li_keep_user_logged_in'] == 'yes' ? true : false;
        // Signon user by ID
        wp_set_auth_cookie($user_id, $remember);

        // Set current WP user so that authentication takes immediate effect without waiting for cookie
        wp_set_current_user($user_id);

        // Store the user's access token as a meta object
        update_user_meta($user_id, 'pkli_access_token', $this->access_token, true);

        // Do action hook that user has authenticated his LinkedIN account for developers to hook into
        do_action('pkli_linkedin_authenticated', $user_id);

        // Validate URL as absolute
        if (filter_var($this->user_redirect, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)) {
            wp_redirect($this->user_redirect); exit;
        }

        // Invalid redirect URL, we'll redirect to admin URL
        else {
            wp_redirect(admin_url());
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
        $xml = simplexml_load_string($this->oauth->get('https://api.linkedin.com/v1/people/~:(id,first-name,last-name,email-address,headline,specialties,positions:(id,title,summary,start-date,end-date,is-current,company),summary,site-standard-profile-request,picture-url,location:(name,country:(code)),industry,public-profile-url)'));

        return $xml;

    }

    /*
     * Checks if this is a LinkedIn sign-in request for our plugin
     */

    private function is_linkedin_signin() {

        // If no action is requested or the action is not ours
        if (!isset($_REQUEST['action']) || ($_REQUEST['action'] != "pkli_login")) {
            return false;
        }

        // If a code is not returned, and no error as well, then OAuth did not proceed properly
        if (!isset($_REQUEST['code']) && !isset($_REQUEST['error'])) {
            return false;
        }
        /*
         * Temporarily disabled this because we're getting two different states at random times

          // If state is not set, or it is different than what we expect there might be a request forgery
          if ( ! isset($_SESSION['li_api_state'] ) || $_REQUEST['state'] != $_SESSION['li_api_state']) {
          return false;
          }
         */

        // This is a LinkedIn signing-request - unset state and return true
        unset($_SESSION['li_api_state']);

        return true;

    }

    /*
     * Authenticate a user by his LinkedIn ID first, and his email address then. IF he doesn't exist, the function creates him based on his LinkedIn email address
     * 
     * @param	string	$xml	The XML response by LinkedIN which contains profile data
     */

    private function authenticate_user($xml) {

        // Logout any logged in user before we start to avoid any issues arising
        wp_logout();

        // Set default redirect URL to the URL provided by shortcode and stored in session
        $this->user_redirect = $_SESSION['li_api_redirect'];

        // Get the user's email address
        $email = (string) $xml->{'email-address'};

        // Get the user's application-specific LinkedIn ID
        $linkedin_id = (string) $xml->{'id'};

        // See if a user with the above LinkedIn ID exists in our database
        $user_by_id = get_users(array('meta_key' => 'pkli_linkedin_id',
            'meta_value' => $linkedin_id));

        // If he exists, return his ID
        if (count($user_by_id) == 1) {

            $user_id = $user_by_id[0]->ID;

            // No custom redirect URL has been specified
            if ($_SESSION['li_api_redirect'] === false) {

                // User already exists in our database, redirect him to Login Redirect URL
                $this->user_redirect = $this->li_options['li_redirect_url'];
            }

            // Update the user's data upon login if the option is enabled
            if ($this->li_options['li_auto_profile_update'] == 'yes') {
                $this->update_user_data($xml, $user_id);
            }

            // Do action saying that user logged in via linkedin
            do_action('pkli_login', $user_id);
            
            return $user_id;
        }

        // ID does not exist, sign in the user if the email already exists
        elseif (email_exists($email)) {

            // Get the user ID by email
            $user = get_user_by('email', $email);

            // No custom redirect URL has been specified
            if ($_SESSION['li_api_redirect'] === false) {

                // User signs up with his LinkedIn ID for the first time, redirect him to reg URL
                $this->user_redirect = $this->li_options['li_registration_redirect_url'];
            }
            // Update the user's data upon login if the option is enabled
            if ($this->li_options['li_auto_profile_update'] == 'yes') {
                $this->update_user_data($xml, $user->ID);
            }
            
            // Run action that the user has logged in first time via LinkedIn
            do_action('pkli_first_login', $user->ID);
            
            // Return the user's ID
            return $user->ID;
        }

        // User is signing in for the first time, and has a valid email address 
        elseif (is_email($email)) {

            // Create user
            $user_id = wp_create_user($email, wp_generate_password(16), $email);

            // Set the user redirect URL
            $this->user_redirect = $this->li_options['li_registration_redirect_url'];

            // Update the user's data, since this is his first sign-in
            $this->update_user_data($xml, $user_id);
            
            // The action tells us that the user has registered via LinkedIn
            do_action('pkli_registration', $user_id);

            return $user_id;
        }

        // Does not exist, return false
        return false;

    }

    // Used by shortcode in order to get the login link
    public function get_login_link($attributes = false, $content = '') {
        // extract data from array
        extract(shortcode_atts(array('text' => 'Login With LinkedIn', 'img' => PKLI_URL . 'includes/assets/img/linkedin-button.png', 'redirect' => false, 'autoredirect' => false, 'class' => ''), $attributes));

        if( $redirect != false){
            // Validate URL as absolute
            if( ! filter_var($redirect, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) ){
                $redirect = home_url($redirect);
            }
        }
     
        // Display the logged in message if user is already logged in
        if (is_user_logged_in()) {
            if( $autoredirect != false && $redirect != false ){
                //Use js because 'wp_redirect' doesn't work
                ?>
                <script>
                document.location.href = '<?php echo $redirect; ?>';
                </script>
                <?php                
            }
            //Show content only if user is already logged in via LinkedIn 
            if(get_user_meta(wp_get_current_user()->ID, 'pkli_linkedin_id') != false){
                $html = $this->li_options['li_logged_in_message'].'<br/>';
                $html .= $content != false ? $content : '';

                return $html;    
            }else{
                return;
            }
        }

        $auth_url = $this->get_auth_url($redirect);

        // User has specified an image
        if (isset($attributes['img'])) {
            return "<a href='" . $auth_url . "' class='$class'><img src='" . $img . "' /></a>";
        }

        // User has specified text
        if (isset($attributes['text'])) {
            return "<a href='" . $auth_url . "' class='$class'>" . $text . "</a>";
        }

        // Default fields
        return "<a href='" . $auth_url . "' class='$class'><img src='" . $img . "' /></a>";

    }

    // Updates the user's wordpress data based on his LinkedIn data
    private function update_user_data($xml, $user_id = false) {
        $first_name = (string) $xml->{'first-name'};
        $last_name = (string) $xml->{'last-name'};
        $description = (string) $xml->{'summary'};
        $linkedin_url = (string) $xml->{'site-standard-profile-request'}->url;
        $linkedin_id = (string) $xml->{'id'};
        $picture_url = (string) $xml->{'picture-url'};
        $location = array('name' => (string) $xml->{'location'}->{'name'}, 'country_code' => (string) $xml->{'location'}->{'country'}->{'code'});
        $industry = (string) $xml->{'industry'};
        $headline = (string) $xml->{'headline'};
        $specialties = (string) $xml->{'specialties'};
        $public_profile_url = (string) $xml->{'public-profile-url'};

        // Get total positions
        $total_positions = (int) $xml->positions->attributes()->total;

        // Depending on the total number of positions, LinkedIn returns data in a different format
        switch ($total_positions) {
            case 1:
                $user_positions[] = array('title' => (string) $xml->positions->position->{'title'}, 'summary' => (string) $xml->positions->position->{'summary'});
                break;
            case $total_positions > 1:
                foreach ($xml->positions->position as $position) {
                    $user_positions[] = array('title' => (string) $position->{'title'}, 'summary' => (string) $position->{'summary'});
                }
                break;
            default:
                $user_positions = array();
                break;
        }

        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        // Update user data in database
        $result = wp_update_user(array('ID' => $user_id, 'first_name' => $first_name, 'last_name' => $last_name, 'description' => $description, 'user_url' => $linkedin_url));

        // Store LinkedIn ID in database
        update_user_meta($user_id, 'pkli_linkedin_id', $linkedin_id);

        // Store all profile fields as metadata values
        update_user_meta($user_id, 'pkli_linkedin_profile', array('first' => $first_name, 'last' => $last_name, 'description' => $description,
            'linkedin_url' => $linkedin_url, 'linkedin_id' => $linkedin_id, 'profile_picture' => $picture_url,
            'location' => $location, 'industry' => $industry, 'headline' => $headline, 
            'specialties' => $specialties, 'positions' => $user_positions, 'public_profile_url' => $public_profile_url));
                
        //Is BuddyPress active?
        if (class_exists('BuddyPress')) {
            /*
            * Buddypress Profile Custom Fields (Linikedin Fields | Buddypress Fields):        
            * first-name -> First Name (Because the field 'Name' is user's nickname)
            * last-name -> Last Name        
            * headline -> Description        
            * positions -> Position (! This field is a textarea)    
            * picture-url -> The Image for User (This type field is textbox)
            */
            $buddypress_options = get_option('pkli_buddypress_options');
            
            $arr_buddypress_fields = isset($buddypress_options['li_buddypress_fields']) && is_array($buddypress_options['li_buddypress_fields']) ? $buddypress_options['li_buddypress_fields'] : array();
            $arr_fields = array();
            if( $first_name != false && isset($arr_buddypress_fields['first-name'])){
                $arr_fields[$arr_buddypress_fields['first-name']] = $first_name;
            }
            if( $last_name != false && isset($arr_buddypress_fields['last-name'])){
                $arr_fields[$arr_buddypress_fields['last-name']] = $last_name;
            }
            if( $headline != false && isset($arr_buddypress_fields['headline']) ){
                $arr_fields[$arr_buddypress_fields['headline']] = $headline;
            }
            if( !empty($user_positions) && isset($arr_buddypress_fields['positions']) ){
                $arr_fields[$arr_buddypress_fields['positions']] = '';
                foreach ($user_positions as $key => $value) {
                    $arr_fields[$arr_buddypress_fields['positions']] .= $value['title'].'<br/>';
                }            
            }
            if( $picture_url != false && isset($arr_buddypress_fields['picture-url']) ){
                $arr_fields[$arr_buddypress_fields['picture-url']] = $picture_url;
            }
           
            if(empty($arr_fields)){
                return $result;
            }
            
            global $wpdb;
            
            $table_prof_fields = $wpdb->prefix.'bp_xprofile_fields';
            $table_prof_data = $wpdb->prefix.'bp_xprofile_data';

            foreach ($arr_fields as $field_type => $field_value) {
                    $sql = "SELECT prof_data.id FROM `{$table_prof_fields}` AS prof_fields INNER JOIN `{$table_prof_data}` AS prof_data ON prof_fields.id = prof_data.field_id WHERE prof_fields.id = {$field_type} AND prof_data.user_id = ".$user_id;
                    $id = $wpdb->get_var( $sql );

                    if( is_null($id) ){
                            $sql = "SELECT id FROM `{$table_prof_fields}` WHERE id = ".$field_type;
                            $field_id = $wpdb->get_var( $sql );
                            if( !is_null($field_id) ){
                                    $wpdb->insert(
                                            $table_prof_data,
                                            array( 'user_id' => $user_id, 'field_id' => $field_id, 'value' => $field_value, 'last_updated' => bp_core_current_time() ),
                                            array( '%d', '%d', '%s', '%s' )
                                    );		
                            }
                    }else{
                            $wpdb->update( $table_prof_data,
                                    array( 'value' => $field_value ),
                                    array( 'id' => $id )
                            );
                    }
            }
        }

        return $result;

    }

}