<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// For theyorker2.gmghosting.com
// Registered to James Hogan (jh559@york.ac.uk)
$config['facebook']['api_key'] = "57a06a579047e30407c34d33e0a81d69";
$config['facebook']['secret'] = "9deef724c66f943eb151387a0766ac85";

$config['facebook']['debug'] = 0; // switch to 0 to disable debug output

$config['facebook']['login_server_base_url'] = 'http://www.facebook.com';
$config['facebook']['api_server_base_url'] = 'http://api.facebook.com';
$config['facebook']['rest_server_addr'] = $config['facebook']['api_server_base_url'].'/restserver.php';

?>
