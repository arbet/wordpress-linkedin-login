<?php

/* 
 * Copyright (C) 2016 Samer Bechara <sam@thoughtengineer.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/*
 * This class stores all of our Freemius integration code
 */
class PKLI_Freemius {
    
    public function __construct() {
        
        // Add filters to modify opt-in messages
        $this->get_instance()->add_filter('connect_message_on_update', array($this, 'update_existing_users_message') , 10, 6);
        
        // Update new users message
        $this->get_instance()->add_filter('connect_message', array($this, 'update_new_users_message') , 10, 6);

    }
    
    // Freemius Integration Code
    function get_instance() {
        
        global $freemius_object;

        if ( ! isset( $freemius_object ) ) {
            // Include Freemius SDK.
            require_once PKLI_PATH . '/freemius/start.php';

            $freemius_object = fs_dynamic_init( array(
                'id'                => '260',
                'slug'              => 'linkedin-login',
                'public_key'        => 'pk_68b2b8023d692aa4c98ef41bf3ce8',
                'is_premium'        => false,
                'has_addons'        => false,
                'has_paid_plans'    => false,
                'menu'              => array(
                    'slug'       => 'linkedin_login',
                    'account'    => false,
                    'support'    => false,
                    'parent'     => array(
                        'slug' => 'options-general.php',
                    ),
                ),
            ) );
        }

        return $freemius_object;
    }    
    
    /*
     * Modifies the opt-in messages for our existing plugin users
     */
    
    public function update_existing_users_message($message, $user_first_name, 
                    $plugin_title, $user_login, $site_link, $freemius_link)  {
        
        
        
        return sprintf(
                    __fs( 'hey-x' ) . '<br><br/>' .
                    __( 'Please help us improve %2$s! If you opt-in, some data about your usage of %2$s will be sent to %5$s. If you skip this, that\'s okay! %2$s will still work just fine.', 'linkedin-login' ),
                    $user_first_name,
                    '<b>' . $plugin_title . '</b>',
                    '<b>' . $user_login . '</b>',
                    $site_link,
                    $freemius_link
                );        
    }
    
    /*
     * Modifies the opt-in messages for our new plugin users
     */
    
    public function update_new_users_message($message, $user_first_name, 
                    $plugin_title, $user_login, $site_link, $freemius_link)  {
        
        
        
        return sprintf(
                    __fs( 'hey-x' ) . ' ' .
                    __( 'in order to enjoy all our features and functionality, %2$s needs to connect your user, %3$s at %4$s to %5$s', 'linkedin-login' ),
                    $user_first_name,
                    '<b>' . $plugin_title . '</b>',
                    '<b>' . $user_login . '</b>',
                    $site_link,
                    $freemius_link
                );        
    }    
    
}

new PKLI_Freemius();