=== LinkedIn Login ===
Contributors: arbet01
Tags: linkedin, linkedin-api, social-login
Requires at least: 3.0.1
Tested up to: 4.3
Stable tag: 0.8.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows your WordPress website visitors to login using their LinkedIn account.  

== Description ==
This plugin gives your WordPress website the ability to allow users to login/register via their LinkedIn account. 

List of features:

* Users can register with their LinkedIn account using one click
* For people who are already registered, the LinkedIn plugin automatically logs them in, by matching the email address associated with their LinkedIn account with the user\'s email address inside WordPress. 
* Just enter your LinkedIn API Key and Secret Key, under the settings page and you\'re ready to go!
* User's First Name and Last Name are automatically updated from LinkedIn
* Use the shortcode [wpli_login_link] to display the sign in link anywhere on your site.
* You can use [wpli_login_link text='Your Custom Link Text'] to generate a sign-in link with your own text.
* [wpli_login_link redirect = 'http://example.com/your-redirect-page'] will redirect the user to a certain URL after login
* [wpli_login_link class = 'class1 class2'] will add the corresponding CSS classes to the generated link
You can find more information here: [LinkedIn Login Plugin](http://thoughtengineer.com/wordpress-linkedin-login-plugin/ "Your WordPress LinkedIn Login Solution").

* If you want to contribute to development, please visit our [Github Repository](https://github.com/arbet/wordpress-linkedin-login/ "Github Repository")

* For Custom LinkedIn-API Development Services, please check my [LinkedIn API developer](http://thoughtengineer.com/linkedin-api-developer/ "LinkedIn API Developer") profile

== Installation ==
1. Upload \"linkedin-login\" to the \"/wp-content/plugins/\" directory.
1. Activate the plugin through the \"Plugins\" menu in WordPress.
1. Enter your API Keys under Settings->LinkedIn Login

== Frequently Asked Questions ==
= Where do I find my LinkedIn API Key and Secret Key? =
1. Go to https://www.linkedin.com/secure/developer and create a new application.
1. Fill out any required fields such as the application name and description.
1. Put your website domain in the Integration URL field. This should match with the current hostname localhost.
1. Set \"OAuth 2.0 Redirect URLs:\" to http://example.com/wp-login.php (Replace this with your login page address)
1. After creating the application, you will be able to see your API key and secret. Copy and paste them into your plugin. 

== Changelog ==

= 0.8.5 =

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
* FIXED: LinkedIn's latest access token wasn't being stored in the db

= 0.4 = 
* Added action pkli_linkedin_authenticated to allow developers to immediately hook after plugin has authenticated.
* Plugin now stores user's profile summary on profile description
* LinkedIn profile URL now retrieved as user's URL

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