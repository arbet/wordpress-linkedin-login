=== LinkedIn Login ===
Contributors: arbet
Tags: linkedin, linkedin-api, social-login
Requires at least: 3.0.1
Tested up to: 3.9.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows your WordPress website visitors to login using their LinkedIn account.  

== Description ==
This plugin gives your WordPress website the ability to allow users to login/register via their LinkedIn account. 

List of features:

* Users can register with their LinkedIn account using one click
* For people who are already registered, the LinkedIn plugin automatically logs them in, by matching the email address associated with their LinkedIn account with the user\'s email address inside WordPress. 
* Just enter your LinkedIn API Key and Secret Key, under the settings page and you\'re ready to go!
* Use the shortcode [wpli_login_link] to display the sign in link anywhere on your site.
* You can use [wpli_login_link text='Your Custom Link Text'] to generate a sign-in link with your own text.

You can find more information here: [LinkedIn Login Plugin](http://thoughtengineer.com/wordpress-linkedin-login-plugin/ \"Your wordpress LinkedIn Login Solution\").

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
= 0.1 =
* Initial release.