<?php
/**
 * Plugin Name: Ultimate LinkedIn Integration
 * Plugin URI: http://thoughtengineer.com/wordpress-linkedin-login-plugin
 * Description: Enables login with LinkedIn functionality for your website
 * Version: 1.0
 * Author: The Thought Engineer
 * Author URI: http://thoughtengineer.com/
 * Text Domain: linkedin-login
 * Domain Path: /languages 
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

// Require New Settings Page
require_once (PKLI_PATH.'/includes/lib/class-pkli-settings.php');

// Require PkliLogin class
require_once (PKLI_PATH.'/includes/lib/PkliLogin.php');

// Require Pkli_Mods class
require_once (PKLI_PATH.'/includes/lib/class-pkli-mods.php');

// Create new objects to register actions
$linkedin = new PkliLogin();
$linkedin_mods = new Pkli_Mods();
new PKLI_Settings();
    
    
/*
  * this function loads our translation files
  */
 function pkli_login_load_translation_files() {
  load_plugin_textdomain('linkedin-login', false, 'linkedin-login/languages');
 }    

//add action to load language files
 add_action('plugins_loaded', 'pkli_login_load_translation_files');
