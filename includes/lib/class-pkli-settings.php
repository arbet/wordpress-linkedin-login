<?php
/*
 * This classes handles the plugin's options page
 * @author Samer Bechara <sam@thoughtengineer.com>
 */

class PKLI_Settings {

    // This stores our plugin options
    private $options;

    /*
     * Class constructor, initializes menu and settings page
     */

    public function __construct() {

        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'init_settings'));

        $this->options = get_option('pkli_basic_options');

    }

    /*
     * Adds an admin menu
     */

    public function add_admin_menu() {
        add_options_page('WP LinkedIn Login Options Page', 'LinkedIn Login', 'manage_options', 'linkedin_login_settings', array($this, 'pkli_options_page_display'));

    }

    /*
     * Displays the options page
     */

    public function pkli_options_page_display() {
        ?>
        <form action='options.php' method='post'>

            <?php
            settings_fields('pkli_options_page');
            do_settings_sections('pkli_options_page');
            submit_button();
            ?>

        </form>
        <?php

    }

    /*
     * Initializes our settings
     */

    public function init_settings() {
        require_once (PKLI_PATH.'/includes/lib/class-pkli-scopes.php');

	register_setting( 'pkli_options_page', 'pkli_basic_options' );

	add_settings_section(
		'pkli_general_options_section', 
		__( 'LinkedIn Login Plugin Settings', 'linkedin-login' ), 
		array($this, 'pkli_basic_options_section_callback'), 
		'pkli_options_page'
	);

	add_settings_field( 
		'li_api_key', 
		__( 'Your LinkedIn API Key', 'linkedin-login' ), 
		array($this, 'text_field_display'), 
		'pkli_options_page', 
		'pkli_general_options_section',
                array('field_name' => 'li_api_key',
                        'field_description' => 'Retrieved from <a href="https://www.linkedin.com/secure/developer">LinkedIn Developer Portal</a>. Follow the previous link, create an application and paste the key here',
                    'field_help' => 'help text goes here')
	);

	add_settings_field( 
		'li_secret_key', 
		__( 'Your LinkedIn API Secret', 'linkedin-login' ), 
		array($this, 'text_field_display'), 
		'pkli_options_page', 
		'pkli_general_options_section' ,
                 array('field_name' => 'li_secret_key',
                     'field_description' => 'This is another key that can be found when you create the application following the previous link as well. Paste it here.')
	);

	add_settings_field( 
		'li_redirect_url', 
		__( 'Login Redirect URL', 'linkedin-login' ), 
		array($this, 'text_field_display'),
		'pkli_options_page', 
		'pkli_general_options_section' ,
                array('field_name' => 'li_redirect_url',
                    'field_description' => 'The absolute URL to redirect users to after login. If left blank or points to external host, will redirect to the dashboard page.')

	);

	add_settings_field( 
		'li_registration_redirect_url', 
		__( 'Sign-Up Redirect URL', 'linkedin-login' ), 
		array($this, 'text_field_display'), 
		'pkli_options_page', 
		'pkli_general_options_section',
                array('field_name' => 'li_registration_redirect_url',
                    'field_description' => 'Users are redirected to this URL when they register via their LinkedIn account. This is useful if you want to show them a one-time welcome message after registration. If left blank or points to external host, will redirect to the dashboard page.')
	);

	add_settings_field( 
		'li_cancel_redirect_url', 
		__( 'Cancel Redirect URL', 'linkedin-login' ), 
		array($this, 'text_field_display'),  
		'pkli_options_page', 
		'pkli_general_options_section',
                array('field_name' => 'li_cancel_redirect_url',
                    'field_description' => 'Users are redirected to this URL when they click Cancel on the LinkedIn Authentication page. This is useful if you want to show them a different option if for some reason they do not want to login with their LinkedIn account. If left blank or points to external host, will redirect back to default WordPress login page.')
	);
        
        add_settings_field( 
		'li_list_scopes', 
		__( 'Scopes', 'linkedin-login' ), 
		array($this, 'multiselect_field_display'),  
		'pkli_options_page', 
		'pkli_general_options_section',
                array('field_name' => 'li_list_scopes',
                    'field_description' => 'The list of LinkedIn scopes.',
                    'args' => array(
                        Pkli_Scopes::READ_BASIC_PROFILE => 'Basic profile',
                        Pkli_Scopes::READ_EMAIL_ADDRESS => 'Email address',
                        Pkli_Scopes::MANAGE_COMPANY => 'Company admin',
                        Pkli_Scopes::SHARING => 'Share',
                    )
                )
	);

	add_settings_field( 
		'li_auto_profile_update', 
		__( 'Retrieve LinkedIn profile data everytime?', 'linkedin-login' ), 
		array($this, 'select_field_display'),  
		'pkli_options_page', 
		'pkli_general_options_section' ,
                array('field_name' => 'li_auto_profile_update',
                    'field_description' => 'This option allows you to pull in the users data the first time, upon registration but not overwrite all of their information every time they login with the linkedin button. This is useful if users spend time creating a custom profile and then they later use the login with linkedin button. Disable this if you do not want their information to be overwritten')                
	);

	add_settings_field( 
		'li_override_profile_photo', 
		__( "Override the user's profile picture?", 'linkedin-login' ), 
		array($this, 'select_field_display'),  
		'pkli_options_page', 
		'pkli_general_options_section' ,
                array('field_name' => 'li_override_profile_photo',
                    'field_description' => 'When enabled, this option fetches the user\'s profile picture from LinkedIn and overrides the default gravatar.com user profile picture used by WordPress. If the plugin is setup to retrive new profile data on every login, the profile picture will be retrieved as well.')
	);

	add_settings_field( 
		'li_logged_in_message', 
		__( 'Logged In Message', 'linkedin-login' ), 
		array($this, 'text_area_display'), 
		'pkli_options_page', 
		'pkli_general_options_section',
                array('field_name' => 'li_logged_in_message',
                    'field_description' => 'Enter a message you would like to show for logged in users in place of the login button. If left blank, the button is hidden and no message is shown.')
	);
        
        add_settings_field( 
		'li_keep_user_logged_in', 
		__( "Keep user logged in?", 'linkedin-login' ), 
		array($this, 'select_field_display'),  
		'pkli_options_page', 
		'pkli_general_options_section' ,
                array('field_name' => 'li_keep_user_logged_in')
	);

    }

    /*
     * Displays a text field setting, called back by the add_settings_field function
     * @param   array   $field_options  Passed by the add_settings_field callback function
     */

    public function text_field_display($field_options) {

        // Get the text field name
        $field_name = $field_options['field_name'];
        ?>
        <input type='text' name='pkli_basic_options[<?php echo $field_name; ?>]' value='<?php echo $this->get_field_value($field_name) ?>'>
        <p class="description"><?php echo isset($field_options['field_description'])?$field_options['field_description']:''; ?></p>
        <?php

    }

    /*
     * Displays a text area setting, called back by the add_settings_field function
     * @param   array   $field_options  Passed by the add_settings_field callback function
     */
 
    public function text_area_display($field_options) {

        $field_name = $field_options['field_name'];
        ?>
        <textarea cols='40' rows='5' name='pkli_basic_options[<?php echo $field_name; ?>]'><?php echo $this->get_field_value($field_name) ?></textarea>
        <p class="description"><?php echo isset($field_options['field_description'])?$field_options['field_description']:''; ?></p>
        <?php

    }

    /*
     * Returns the field's value
     */

    private function get_field_value($field_name) {

        return isset($this->options[$field_name]) ? $this->options[$field_name] : '';

    }
    
    /*
     * Displays a select field
     */
    function select_field_display($field_options) {

        $field_name = $field_options['field_name'];
        $field_value = $this->get_field_value($field_name);
        ?>
        <select name='pkli_basic_options[<?php echo $field_name;?>]'>
            <option value='yes' <?php selected($field_value, 'yes'); ?>>Yes</option>
            <option value='no' <?php selected($field_value, 'no'); ?>>No</option>
        </select>
        <p class="description"><?php echo isset($field_options['field_description']) ? $field_options['field_description'] : ''; ?></p>
        <?php
    }
    
    /*
     * Displays a multi select field
     */
    function multiselect_field_display($field_options){
        $field_name = $field_options['field_name'];
        $field_value = $this->get_field_value($field_name);
        $field_value = is_array($field_value) ? $field_value : array();
        $args = $field_options['args'];
        ?>
        <select name='pkli_basic_options[<?php echo $field_name;?>][]' multiple size="4">
            <?php
                if( !empty($args) ){
                    foreach ($args as $key => $value) { ?>
                        <option value='<?php echo $key?>' <?php echo in_array($key, $field_value) ? 'selected' : ''; ?>><?php echo $value; ?></option>
                    <?php }
                }
            ?>
        </select>
        <p class="description"><?php echo isset($field_options['field_description']) ? $field_options['field_description'] : ''; ?></p>
        <?php
    }
    /*
     * Rendered at the start of the options section
     */
    function pkli_basic_options_section_callback() {

        echo __('For installation instructions, please visit <a href="http://thoughtengineer.com/wordpress-linkedin-login-plugin/" target="_blank">Installation Instructions Page</a>', 'linkedin-login');

    }

}