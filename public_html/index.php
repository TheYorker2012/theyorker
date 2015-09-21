<?php
error_reporting(0);

$start_time = microtime(TRUE);

define('BASEPATH', '../system/');
define('APPPATH', '../system/application/');
define('EXT', '.php');
define('FCPATH', __FILE__);
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

require_once BASEPATH.'codeigniter/CodeIgniter'.EXT;

/* If we are sending html, add the render time */
$headers = headers_list();
foreach ($headers as $header) {
	if (strpos($header, 'Content-type: text/html') !== FALSE) {
		echo('<!-- Rendered in '.(microtime(TRUE) - $start_time).' seconds  -->');
		break;
	}
}
?>