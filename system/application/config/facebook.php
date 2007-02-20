<?php
/**
 * This file contains stuff that may be different for each platform developer.
 * Copy the file into example_client.conf.php and then edit the variables as needed.
 */

$config['api_server_base_url'] = 'http://api.facebook.com';
$config['login_server_base_url'] = 'http://www.facebook.com';

$config['rest_server_addr'] = $config['api_server_base_url']."/restserver.php";

// SAMPLE API keys
// These should be generated from your trip to
// http://developers.facebook.com/account.php

// GMG Hosting
$config['api_key'] = '0cf1a44ba0268cdb731e26e0741c779d';
$config['secret'] = 'f215e962f9cd2538cdad0ac1eb2a22e9';

// This assumes your registered callback URL is, say, http://www.myapplication.com/
// The page which would serve as an after-login entrypoint into your app would 
// be the "next" parameter passed to login (below, "login_url"), appended to
// your callback URL.  So the full address would be http://www.myapplication.com/facebook/sample_client.php
$config['next'] = urlencode('facebook/sample_client.php');

$config['login_url'] = $config['login_server_base_url'].'/login.php?v=1.0' .
                       '&next=' . $config['next'] . '&api_key=' . $config['api_key'];

$config['debug'] = 1; // TURN this on for XML input spew

?>
