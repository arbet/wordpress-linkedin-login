<?php 
/*
 Title: LinkedIn Login Options
 Setting: pkli_basic_options
 Tab Order: 10
 */
piklist('field', array(
  'type' => 'text'
  ,'field' => 'li_api_key'
  ,'label' => __('LinkedIn API Key', 'linkedin-login')
  ,'description' => __('Retrieved from <a href="https://www.linkedin.com/secure/developer">LinkedIn\'s Developer Portal</a>. Create an application and you will be able to see the keys.','linkedin-login')
  ,'help' => __('Go to the link next to the field, then create a new application. After that, find the corresponding key and paste it here.','linkedin-login')
  ,'value' => __('Enter your API Key','linkedin-login')
  ,'attributes' => array(
  'class' => 'text'
  )
 ));

piklist('field', array(
  'type' => 'text'
  ,'field' => 'li_secret_key'
  ,'label' => __('LinkedIn Secret Key','linkedin-login')
  ,'description' => __('Retrieved from <a href="https://www.linkedin.com/secure/developer">LinkedIn\'s Developer Portal</a>. Create an application and you will be able to see the keys.','linkedin-login')
  ,'help' => __('Go to the link next to the field, then create a new application. After that, find the corresponding key and paste it here.','linkedin-login'),
  'value' => __('Enter your secret key','linkedin-login')
  ,'attributes' => array(
  'class' => 'text'
  )
 ));

piklist('field', array(
  'type' => 'text'
  ,'field' => 'li_redirect_url'
  ,'label' => __('Redirect URL','linkedin-login')
  ,'description' => __('The absolute URL to redirect users to after login. If left blank or points to external host, will redirect to the dashboard page.','linkedin-login')
  ,'help' => __('Write the full URL to redirect to after login, e.g. http://example.com/redirect/','linkedin-login'),
  'value' => __('','linkedin-login')
  ,'attributes' => array(
  'class' => 'text'
  )
 ));
 
piklist('field', array(
  'type' => 'text'
  ,'field' => 'li_registration_redirect_url'
  ,'label' => __('Sign-Up Redirect URL','linkedin-login')
  ,'description' => __('Users are redirected to this URL when they register via their LinkedIn account. This is useful if you want to show them a one-time welcome message after registration. If left blank or points to external host, will redirect to the dashboard page.','linkedin-login')
  ,'help' => __('Write the full URL to redirect to after registration, e.g. http://example.com/signup-thank-you/','linkedin-login'),
  'value' => __('','linkedin-login')
  ,'attributes' => array(
  'class' => 'text'
  )
 ));

  piklist('field', array(
    'type' => 'select'
    ,'field' => 'li_auto_profile_update'
    ,'label' => 'Retrieve LinkedIn profile data everytime?'
    ,'description' => 'Should we update user profile data everytime they sign in?'
    ,'help' => 'This option allows you to pull in the users data the first time, upon registration but not overwrite all of their information every time they login with the linkedin button. This is useful if users spend time creating a custom profile and then they later use the login with linkedin button. Disable this if you do not wnat their information to be overwritten'
    ,'attributes' => array(
      'class' => 'text'
    )
    ,'choices' => array(
      'yes' => 'Yes'
      ,'no' => 'No'
    )
  ));
  
    piklist('field', array(
    'type' => 'select'
    ,'field' => 'li_override_profile_photo'
    ,'label' => 'Override the user\'s profile picture?'
    ,'description' => 'When enabled, this option fetches the user\'s profile picture from LinkedIn and overrides the default gravatar.com user profile picture used by WordPress'
    ,'attributes' => array(
      'class' => 'text'
    )
    ,'choices' => array(
	'no' => 'No',
	'yes' => 'Yes'
    )
  ));