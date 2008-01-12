<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, pMachine, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Code Igniter Application Controller Class
 *
 * This class object is the super class the every library in
 * Code Igniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/general/controllers.html
 */
class Controller extends CI_Base {

	var $_ci_scaffolding	= FALSE;
	var $_ci_scaff_table	= FALSE;
	
	/**
	 * Constructor
	 *
	 * Calls the initialize() function
	 */
	function Controller()
	{	
		parent::CI_Base();
		$this->_ci_initialize();
		log_message('debug', "Controller Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize
	 *
	 * Assigns all the bases classes loaded by the front controller to
	 * variables in this class.  Also calls the autoload routine.
	 *
	 * @access	private
	 * @return	void
	 */
	function _ci_initialize()
	{
		// Assign all the class objects that were instantiated by the
		// front controller to local class variables so that CI can be
		// run as one big super object.
		$classes = array(
							'config'	=> 'Config',
							'uri'		=> 'URI',
							'lang'		=> 'Language'
							);
		
		foreach ($classes as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader');
		$this->load->_ci_autoloader();
	}

}
// END _Controller class
?>
