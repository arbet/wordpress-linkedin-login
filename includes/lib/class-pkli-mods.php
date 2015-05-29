<?php

/* 
 * Copyright (C) 2015 Samer Bechara <sam@thoughtengineer.com>
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
 * This class contains all mods that modify WordPress but aren't part of the login process
 */
Class Pkli_Mods {
    
    public function __construct() {
	
	// Add filter to override avatar
	add_filter( 'get_avatar' , array('Pkli_Mods', 'override_user_photo') , 1 , 5 );
    }
    
    /*
     * This function overrides the user photo with the LinkedIn profile photo
     */
    public static function override_user_photo ($avatar, $id_or_email, $size, $default, $alt) {
	
	// Get plugin option
	$li_options = get_option('pkli_basic_options');
	
	// Do nothing if the option is not enabled
	if($li_options['li_override_profile_photo'] !== 'yes'){
	    return;
	}

	// Assume that no user is logged in
	$user = false;

	// If the ID passed is numeric, get user by ID
	 if ( is_numeric( $id_or_email ) ) {

	     $id = (int) $id_or_email;
	     $user = get_user_by( 'id' , $id );

	// Object passed, Get user by ID part of that object
	 } elseif ( is_object( $id_or_email ) ) {

	     if ( ! empty( $id_or_email->user_id ) ) {
		 $id = (int) $id_or_email->user_id;
		 $user = get_user_by( 'id' , $id );
	     }
	// Get user by email
	 } else {
	     $user = get_user_by( 'email', $id_or_email );	
	 }

	 // User has been successfully returned
	 if ( $user && is_object( $user ) ) {

		$li_profile = get_user_meta($user->ID, 'pkli_linkedin_profile', true);
		$li_profile_pic = $li_profile['profile_picture'];
		
		
		 $avatar = "<img alt='{$alt}' src='{$li_profile_pic}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";

	 }

	 return $avatar;	    
	    
	
    }
}