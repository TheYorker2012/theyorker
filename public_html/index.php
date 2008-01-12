<?php
error_reporting(E_ALL);

define('BASEPATH', '../system/');
define('APPPATH', '../system/application/');
define('EXT', '.php');
define('FCPATH', __FILE__);
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

require_once BASEPATH.'codeigniter/CodeIgniter'.EXT;
?>
