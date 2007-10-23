<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// Facebook keys for The Yorker - News Ticker
// Registered to Chris Travis (cdt502 - ctravis@gmail.com)
$config['facebook']['ticker']['api_key'] = '4f3edfdf722eb9c17ec3f748b9b7878d';
$config['facebook']['ticker']['secret'] = 'b0bb19f1988b57a8a20884e15ecb6262';
$config['facebook']['ticker']['user_id'] = '222301303';
$config['facebook']['ticker']['session_key'] = 'c314d44c88a2da443b6ceebb-ihf8ZCOoXnv_m9jx2u3CCkg..';

// For theyorker2.gmghosting.com
// Registered to James Hogan (jh559@york.ac.uk)
$config['facebook']['api_key'] = "57a06a579047e30407c34d33e0a81d69";
$config['facebook']['secret'] = "9deef724c66f943eb151387a0766ac85";

$config['facebook']['debug'] = 0; // switch to 0 to disable debug output

$config['facebook']['login_server_base_url'] = 'http://www.facebook.com';
$config['facebook']['api_server_base_url'] = 'http://api.facebook.com';
$config['facebook']['rest_server_addr'] = $config['facebook']['api_server_base_url'].'/restserver.php';

?>
