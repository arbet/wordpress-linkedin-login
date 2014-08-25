<?php 
/*
 Title: LinkedIn API Keys
 Setting: pkli_basic_options
 */
piklist('field', array(
  'type' => 'text'
  ,'field' => 'li_api_key'
  ,'label' => __('LinkedIn API Key', 'pkli-login')
  ,'description' => __('Retrieved from <a href="https://www.linkedin.com/secure/developer">LinkedIn\'s Developer Portal</a>. Create an application and you will be able to see the keys.','pkli-login')
  ,'help' => __('Go to the link next to the field, then create a new application. After that, find the corresponding key and paste it here.','pkli-login')
  ,'value' => __('Enter your API Key','pkli-login')
  ,'attributes' => array(
  'class' => 'text'
  )
 ));

piklist('field', array(
  'type' => 'text'
  ,'field' => 'li_secret_key'
  ,'label' => __('LinkedIn Secret Key','pkli-login')
  ,'description' => __('Retrieved from <a href="https://www.linkedin.com/secure/developer">LinkedIn\'s Developer Portal</a>. Create an application and you will be able to see the keys.','pkli-login')
  ,'help' => __('Go to the link next to the field, then create a new application. After that, find the corresponding key and paste it here.','pkli-login'),
  'value' => __('Enter your secret key','pkli-login')
  ,'attributes' => array(
  'class' => 'text'
  )
 ));

piklist('field', array(
  'type' => 'text'
  ,'field' => 'li_redirect_url'
  ,'label' => __('Redirect URL','pkli-login')
  ,'description' => __('The absolute URL to redirect users to after login. If left blank or points to external host, will redirect to the dashboard page.','pkli-login')
  ,'help' => __('Write the full URL to redirect to after login, e.g. http://example.com/page.php','pkli-login'),
  'value' => __('','pkli-login')
  ,'attributes' => array(
  'class' => 'text'
  )
 ));
 
