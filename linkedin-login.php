<?php
/**
 * Plugin Name: WP LinkedIn Login
 * Plugin URI: http://thoughtengineer.com/wordpress-linkedin-login-plugin
 * Description: Enables login with LinkedIn functionality for your website
 * Version: 0.8.8
 * Author: Samer Bechara
 * Author URI: http://thoughtengineer.com/
 * Text Domain: linkedin-login
 * Domain Path: /languages 
 * Plugin Type: Piklist
 * License: GPL2
 */

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

// Do not allow direct file access
defined('ABSPATH') or die("No script kiddies please!");

// Define plugin path
define( 'PKLI_PATH', plugin_dir_path( __FILE__ ) );
define( 'PKLI_URL', plugin_dir_url(__FILE__));

// Require PkliLogin class
require_once (PKLI_PATH.'/includes/lib/PkliLogin.php');

// Require Pkli_Mods class
require_once (PKLI_PATH.'/includes/lib/class-pkli-mods.php');

// Crete new objects to register actions
$linkedin = new PkliLogin();
$linkedin_mods = new Pkli_Mods();

// Initialize piklist framework
add_action('init', 'pkli_init');

// Check if piklist is installed and active
function pkli_init()
{
  if(is_admin())
  {
   include_once(PKLI_PATH.'/includes/lib/class-piklist-checker.php');
 
   if (!piklist_checker::check(__FILE__))
   {

     return;
   }
   
  }
  
}

    // Create settings page
  add_filter('piklist_admin_pages', 'piklist_theme_setting_pages');
  function piklist_theme_setting_pages($pages)
  {
     $pages[] = array(
      'page_title' => __('LinkedIn Login Settings', 'linkedin-login')
      ,'menu_title' => __('LinkedIn Login', 'linkedin-login')
      ,'sub_menu' => 'options-general.php' //Under Appearance menu
      ,'capability' => 'manage_options'
      ,'menu_slug' => 'linkedin_login'
      ,'setting' => 'pkli_basic_options'
      ,'menu_icon' => PKLI_URL.'includes/assets/img/linkedin.png'
      ,'page_icon' => PKLI_URL.'includes/assets/img/linkedin.png'
      ,'single_line' => false
      ,'default_tab' => 'Plugin Settings'
      ,'save_text' => __('Save LinkedIn Settings','linkedin-login')
    );
 
    return $pages;
  }
    
    
/*
  * this function loads our translation files
  */
 function pkli_login_load_translation_files() {
  load_plugin_textdomain('linkedin-login', false, 'linkedin-login/languages');
 }    

//add action to load language files
 add_action('plugins_loaded', 'pkli_login_load_translation_files');
