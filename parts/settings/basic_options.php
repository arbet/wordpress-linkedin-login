<?php 
/*
 Title: LinkedIn API Keys
 Setting: pkli_basic_options
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
  ,'help' => __('Write the full URL to redirect to after login, e.g. http://example.com/page.php','linkedin-login'),
  'value' => __('','linkedin-login')
  ,'attributes' => array(
  'class' => 'text'
  )
 ));
 
