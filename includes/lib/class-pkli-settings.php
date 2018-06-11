<?php
/*
 * This classes handles the plugin's options page
 * @author The Thought Engineer <sam@thoughtengineer.com>
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
        $this->options_buddypress = get_option('pkli_buddypress_options');
        $this->options_locked_content = get_option('pkli_locked_content_options');
    }

    /*
     * Adds an admin menu
     */
    public function add_admin_menu() {
        add_options_page('Ultimate LinkedIn Integration Options Page', 'Ultimate LinkedIn Integration', 'manage_options', 'linkedin_login_settings', array($this, 'pkli_options_page_display'));

    }

    /*
     * Displays the options page
     */
    public function pkli_options_page_display() {
        echo '<h2>'. __( 'Ultimate LinkedIn Integration Plugin Settings', 'linkedin-login' ) .'</h2>';

        $this->pkli_basic_options_section_callback();
  
        $active_tab = "general-options";
        if(isset($_GET["tab"])){
            if($_GET["tab"] == "buddypress-options" && class_exists('BuddyPress')){
                $active_tab = "buddypress-options";
            }elseif($_GET["tab"] == "locked-content-options"){
                $active_tab = "locked-content-options";
            }
        }
        ?>
        <h2 class="nav-tab-wrapper">
                <a href="?page=linkedin_login_settings&tab=general-options" class="nav-tab <?php if($active_tab == 'general-options'){echo 'nav-tab-active';} ?>"><?php _e('General', 'linkedin-login'); ?></a>
                <?php if (class_exists('BuddyPress')) { ?>
                    <a href="?page=linkedin_login_settings&tab=buddypress-options" class="nav-tab <?php if($active_tab == 'buddypress-options'){echo 'nav-tab-active';} ?>"><?php _e('BuddyPress', 'linkedin-login'); ?></a>
                <?php } ?>
                <a href="?page=linkedin_login_settings&tab=locked-content-options" class="nav-tab <?php if($active_tab == 'locked-content-options'){echo 'nav-tab-active';} ?>"><?php _e('Locked Content', 'linkedin-login'); ?></a>                
        </h2>
        <form action='options.php' method='post'>

            <?php           
            if ($active_tab == 'general-options') {
                settings_fields('pkli_options_page');
                do_settings_sections('pkli_options_page');
                
            }elseif($active_tab == 'buddypress-options') {
                
                echo "<div class='tab-buddypress-options'>";
                settings_fields('pkli_buddypress_options');
                do_settings_sections('pkli_options_buddypress_page');
                echo "</div>";
                
            }elseif($active_tab = "locked-content-options"){
                
                echo "<div class='tab-locked-content-options'>";
                settings_fields('pkli_locked_content_options');
                do_settings_sections('pkli_options_locked_content_page');
                echo "</div>";
            }
            
            submit_button();
            ?>

        </form>
        <?php
    }

    /*
     * Initializes our settings
     */
    public function init_settings() {
        //For BuddyPress
        register_setting( 'pkli_buddypress_options', 'pkli_buddypress_options' );
        add_settings_section('pkli_buddypress_options_section', '', '', 'pkli_options_buddypress_page' );

        global $wpdb;

        $table_prof_fields = $wpdb->prefix.'bp_xprofile_fields';
        $sql = "SELECT id, name FROM `{$table_prof_fields}` WHERE 1";
        $arr_names = $wpdb->get_results( $sql );
        add_settings_field(
                'pkli_buddypress_options_section', 
                __( '', 'linkedin-login' ), 
                array($this, 'buddypress_fields_match'),  
                'pkli_options_buddypress_page', 
                'pkli_buddypress_options_section' ,
                array('field_name' => 'li_buddypress_fields', 'args' => $arr_names)
        );
        
        //For General options
        register_setting( 'pkli_options_page', 'pkli_basic_options' );
        add_settings_section( 'pkli_general_options_section', '', '', 'pkli_options_page' );

        add_settings_field( 
                'li_api_key', 
                __( 'Your LinkedIn API Key', 'linkedin-login' ), 
                array($this, 'text_field_display'), 
                'pkli_options_page', 
                'pkli_general_options_section',
                array('field_name' => 'li_api_key',
                        'field_description' => 'Retrieved from <a href="https://www.linkedin.com/secure/developer" target="_blank">LinkedIn Developer Portal</a>. Follow the previous link, create an application and paste the key here',
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

        require_once (PKLI_PATH.'/includes/lib/class-pkli-scopes.php');

        add_settings_field( 
                'li_list_scopes', 
                __( 'Scopes', 'linkedin-login' ), 
                array($this, 'checkbox_field_display'),  
                'pkli_options_page', 
                'pkli_general_options_section',
                array('field_name' => 'li_list_scopes',
                    'field_description' => 'Select the additional LinkedIn Scopes you need. This option should be only used by developers who need to extend the plugin.',
                    'args' => array(
                        'values' => array(
                            Pkli_Scopes::READ_BASIC_PROFILE => 'Basic profile',
                            Pkli_Scopes::READ_EMAIL_ADDRESS => 'Email address',
                            Pkli_Scopes::MANAGE_COMPANY => 'Company admin',
                            Pkli_Scopes::SHARING => 'Share',
                        ),
                        'other' => array('disabled' => array(Pkli_Scopes::READ_BASIC_PROFILE, Pkli_Scopes::READ_EMAIL_ADDRESS))
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
                    'field_description' => 'Enter a message you would like to show for logged in users in place of the login button. If left blank, the button is hidden and no message is shown.',
                    'args' => array(
                        'parent' => 'pkli_basic_options'
                    )
                )
        );

        add_settings_field( 
                'li_keep_user_logged_in', 
                __( "Keep user logged in?", 'linkedin-login' ), 
                array($this, 'select_field_display'),  
                'pkli_options_page', 
                'pkli_general_options_section' ,
                array('field_name' => 'li_keep_user_logged_in',
                    'field_description' => 'Should the user login every time, or should we remember their details after they login via LinkedIn?')
        );
        //For Locked Content
        register_setting( 'pkli_locked_content_options', 'pkli_locked_content_options' );
        add_settings_section('pkli_locked_content_options_section', '', '', 'pkli_options_locked_content_page' );
        
        add_settings_field( 
                'pkli_locked_content_options_section', 
                __( 'Custom Message', 'linkedin-login' ), 
                array($this, 'text_area_display'), 
                'pkli_options_locked_content_page', 
                'pkli_locked_content_options_section',
                array('field_name' => 'li_locked_content_message',
                    'field_description' => '',
                    'args' => array(
                        'parent' => 'pkli_locked_content_options'
                    )
                )
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
        $arg_parent = !empty($field_options['args']['parent']) ? $field_options['args']['parent'] : 'pkli_basic_options';
        $stored_value = $arg_parent == 'pkli_basic_options' ? $this->get_field_value($field_name) : (isset($this->options_locked_content[$field_name]) ? $this->options_locked_content[$field_name] : '');
        ?>
        <textarea cols='40' rows='5' name='<?php echo $arg_parent; ?>[<?php echo $field_name; ?>]'><?php echo $stored_value; ?></textarea>
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
     * Displays a checkbox field
     */
    function checkbox_field_display($field_options){
        $field_name = $field_options['field_name'];
        $field_value = $this->get_field_value($field_name);
        $field_value = is_array($field_value) ? $field_value : array();

        $arg_values = $field_options['args']['values'];
        $other = $field_options['args']['other'];
        $disabled = !empty($other['disabled']) ? $other['disabled'] : array();
        $field_value = array_unique(array_merge($field_value, $disabled));
        if( !empty($arg_values) ){
            foreach ($arg_values as $key => $value) { ?>
                        <p><input type="checkbox" name="pkli_basic_options[<?php echo $field_name;?>][]" value="<?php echo $key; ?>" <?php echo in_array($key, $field_value) ? 'checked' : ''; ?> <?php echo in_array($key, $disabled) ? 'disabled' : ''; ?>>
                        <label><?php echo $value; ?></label></p>
            <?php }
        }
        ?>
        <p class="description"><?php echo isset($field_options['field_description']) ? $field_options['field_description'] : ''; ?></p>
        <?php
    }
    
    /*
     * Fields for BuddyPress
     */
    function buddypress_fields_match($field_options){
        $field_name = $field_options['field_name'];
        $args = $field_options['args'];

        $stored_values = isset($this->options_buddypress[$field_name]) ? $this->options_buddypress[$field_name] : '';
        $stored_values = is_array($stored_values) ? $stored_values : array();

        $ln_fields = array(
            'first-name'    =>'First Name',
            'last-name'     =>'Last Name',
            'headline'      =>'Headline',
            'positions'     =>'Positions',
            'picture-url'   =>'Picture URL'
        );
        ?>
        <h2><?php _e( 'Map LinkedIn to BuddyPress fields', 'linkedin-login' );?></h2>
        <h3><?php _e( 'Choose which LinkedIn fields correspond to your BuddyPress custom fields', 'linkedin-login' );?></h3>
        <table class='buddypress_table'>
            <tr><th><?php _e( 'LinkedIn', 'linkedin-login' );?></th><th><?php _e( 'Buddypress', 'linkedin-login' );?></th></tr>
            <?php
                foreach ($ln_fields as $ln_key => $ln_value) {
                    echo '<tr><td>'.$ln_value.'</td><td>'. $this->buddypress_list_fields($args, $field_name, $ln_key, $stored_values) .'</td></tr>';
                }
            ?>
        </table>
    <?php 
    }
    
    //Get list of buddypress fields
    private function buddypress_list_fields($args, $field_name, $field_key, $stored_values){
        if( empty($args) ){
            return '';
        }
        $html = '<select name="pkli_buddypress_options['. $field_name .']['. $field_key .']">';
        if( !empty($args) ){
            $selected = (isset($stored_values[$field_key]) && $stored_values[$field_key] == 'not_map') ? 'selected' : '';
            $html .= '<option value="not_map" '. $selected .'>'. __( 'Do Not Map', 'linkedin-login' ) .'</option>';
            foreach ($args as $key => $value) {
                $selected = (isset($stored_values[$field_key]) && $stored_values[$field_key] == $value->id) ? 'selected' : '';
                $html .= '<option value="'. $value->id .'" '. $selected .'>'. $value->name .'</option>';
            }
        }
        $html .= '</select>';
        return $html;
    }
    
    /*
     * Rendered at the start of the options section
     */
    function pkli_basic_options_section_callback() {

        echo __('For installation instructions, please visit <a href="http://thoughtengineer.com/wordpress-linkedin-login-plugin/" target="_blank">Installation Instructions Page</a>', 'linkedin-login');

    }

}