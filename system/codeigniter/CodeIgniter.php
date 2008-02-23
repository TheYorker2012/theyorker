<?php
require(BASEPATH.'codeigniter/Common'.EXT);

set_error_handler('_exception_handler');
set_magic_quotes_runtime(0); // Kill magic quotes

$RTR =& load_class('Router');

load_class('Controller', FALSE);

// Load the local application controller
include(APPPATH.'controllers/'.$RTR->fetch_directory().$RTR->fetch_class().EXT);

/*
 * ------------------------------------------------------
 *  Security check
 * ------------------------------------------------------
 *
 *  None of the functions in the app controller or the
 *  loader class can be called via the URI, nor can
 *  controller functions that begin with an underscore
 */
$class  = $RTR->fetch_class();
$method = $RTR->fetch_method();

if ( ! class_exists($class)
	OR $method == 'controller'
	OR substr($method, 0, 1) == '_'
	OR in_array($method, get_class_methods('Controller'), TRUE)
	)
{
	show_404();
}

/*
 * ------------------------------------------------------
 *  Instantiate the controller and call requested method
 * ------------------------------------------------------
 */

// Instantiate the Controller
$CI = new $class();

// Is there a "remap" function?
if (method_exists($CI, '_remap'))
{
	call_user_func_array(array(&$CI, '_remap'), array_slice($RTR->rsegments, 1));
}
else
{
	if ( ! method_exists($CI, $method))
	{
		show_404();
	}

	// Call the requested method.
	// Any URI segments present (besides the class/function) will be passed to the method for convenience		
	call_user_func_array(array(&$CI, $method), array_slice($RTR->rsegments, 2));
}

?>
