<?php
/**
 * Code Igniter Application Controller Class
 *
 * This class object is the super class the every library in
 * Code Igniter will be assigned to.
 */
class Controller {
	private static $instance;

	function Controller()
	{	
		self::$instance =& $this;
		$this->_ci_initialize();
	}

	function _ci_initialize()
	{
		// Assign all the class objects that were instantiated by the
		// front controller to local class variables so that CI can be
		// run as one big super object.
		$classes = array(
			'config'	=> 'Config',
			'uri'		=> 'URI',
			'lang'		=> 'Language',
			'input'		=> 'Input'
		);
		
		foreach ($classes as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader');
		$this->load->_ci_autoloader();
	}

	public static function &get_instance()
	{
		return self::$instance;
	}
}

function &get_instance()
{
	return Controller::get_instance();
}
?>
