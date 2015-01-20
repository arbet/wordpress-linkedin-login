=== LinkedIn Login ===
Contributors: arbet01
Tags: linkedin, linkedin-api, social-login
Requires at least: 3.0.1
Tested up to: 4.1
Stable tag: 0.3.2
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

= 0.4 = 
* Added action pkli_linkedin_authenticated to allow developers to immediately hook after plugin has authenticated.
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