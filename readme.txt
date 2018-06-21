=== Ultimate LinkedIn Integration ===
Contributors: arbet01
Tags: linkedin, linkedin-api, social-login
Donate link: https://paypal.me/SamerBechara
Requires at least: 3.0.1
Tested up to: 4.9.6
Requires PHP: 5.x
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

One click Ultimate LinkedIn Integration/Registration, Buddy Press Integration, profile syncing, and more...

== Description ==
Ultimate LinkedIn Integration (Previously Ultimate LinkedIn Integration) is the most advanced integration between LinkedIn and WordPress. This plugin gives your WordPress website the ability to integrate with LinkedIn\'s API in a way that isn\'t possible with other plugins. 

List of major features:
- Map BuddyPress custom fields to LinkedIn profile fields
- Allow users to register/login with their LinkedIn account using one click
- Lock content and make it available to users who have logged in using LinkedIn only. Users who have logged in via other methods won\'t be able to see it. 
- Custom redirect URLs upon Sign-up, Login and Authorization Cancellation
- Ability to request custom scopes upon authentication (Basic profile and email address are asked for by default, Sharing and Company Admin scopes are optional)
- Ability to sync user data the first time user signs up, or everytime they login.
- Option to override the user\'s profile picture with LinkedIn\'s profile picture
- Ability to keep the user logged in, or require them to login every time.
- Docmentation and ShortCodes: [WordPress LinkedIn Documentation](http://thoughtengineer.com/docs/ultimate-linkedin-login/)
- Request features via our [Github Issue Tracker](https://github.com/arbet/wordpress-linkedin-login/issues)
- For Custom Development Services, get in touch through our [website](http://thoughtengineer.com/)


== Installation ==
Install like any other plugin, and go to plugin settings page for configuration

== Changelog ==

= 1.1.1 =
 * Fixed BuddyPress Integration

= 1.1 =
 * 

= 1.0.1 =
 * Removed old files and folders

= 1.0 =
 * Added BuddyPress Integration
 * Added Custom Scopes
 * Added Content Locking Feature

= 0.9.0 = 
 * Removed Freemius Integration
 * Removed Piklist dependency to make install process easier

= 0.8.8 = 
 * Fixed a bug in freemius integration which showed an error for users who did not have Piklist installed

= 0.8.7 = 
 * Added Freemius Integration

= 0.8.6 =

* Removed WP Session Manager as it caused lots of load on wp_options table for heavy websites
* Reverted to regular PHP Sessions

= 0.8.4 = 

 * Feature: Added headline, specialties and positions fields to pkli_linkedin_profile meta fields
 * Improvement: Updated Piklist checker to latest version

= 0.8.3 = 

* Feature: Added user location and industry as meta fields

= 0.8 = 
* Feature: Users now can be redirected to different page if they decide not to authenticate via LinkedIn
* Feature: User avatars are now fetched from LinkedIn and can override Gravatars via options page
* Feature: A custom message can be now shown to logged in users
* Fixed: Login button no longer shown for loggedin users
* Fixed: Shortcode redirect attributes now working again properly
* Fixed: Error logging added for additional troubleshooting

= 0.7.1 = 
* User LinkedIn profile data now being stored as a user meta under field pkli_linkedin_profile to help developers use the data in their themes/plugins

= 0.7 = 
* Added option to disable data updating every time
* Changed Sign-in With LinkedIn button on WordPress default login page
* Refactored code to make more sense

= 0.6.1 = 
* Added wp_set_current_user function call so that authentication takes immediate effect without waiting for cookie

= 0.6 = 
* Fixed a bug when user changed his email address on LinkedIn or inside Wordpress, he would be registered as a new user
* Refactored code for easier readability and modifications

= 0.5.2 = 
* Feature: Users can now specify image via shortcode

= 0.5.1 =
* FIXED: Shortcode with default URL was not working

= 0.5 = 
* Added an option to allow for a one-time redirect URL upon registration

= 0.4.3 =
* Bug was not properly fixed in previous versions, fixed now

= 0.4.2 =
* FIXED: Custom hook was improperly being triggered on any page load, not just upon login

= 0.4.1 =
* FIXED: LinkedIn\'s latest access token wasn\'t being stored in the db

= 0.4 = 
* Added action pkli_linkedin_authenticated to allow developers to immediately hook after plugin has authenticated.
* Plugin now stores user\'s profile summary on profile description
* LinkedIn profile URL now retrieved as user\'s URL

= 0.3.2 = 

* Fixed PHP notice showing on plugin activation
* Updated Piklist Checker to 0.6.0

= 0.3.1 =
* Updated readme file description

= 0.3 = 
* First Name and Last Name automatically retrieved from LinkedIn and updated in wordpress database upon every login

= 0.21 =
* Fixed a bug where two shortcodes on the same page was causing an error
* Added option to specify CSS class via shortcode

= 0.2 =
* Added option to allow redirect URL via shortcode

= 0.11 =
* Fixed minor aesthetic issues.

= 0.1 =
* Initial release.
