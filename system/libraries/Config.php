<?php
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
 * Code Igniter Config Class
 *
 * This class contains functions that enable config files to be managed
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/libraries/config.html
 */
class CI_Config {
	private static $instance = null;

	private function __construct() {
		$this->load('config');
	}

	private function __clone() {}

	public static function &get_instance() {
		if (self::$instance === null)
			self::$instance = new self();

		return self::$instance;
	}

	var $config = array();
	var $is_loaded = array();
  	
	// --------------------------------------------------------------------

	/**
	 * Load Config File
	 *
	 * @access	public
	 * @param	string	the config file name
	 * @return	boolean	if the file was loaded correctly
	 */	
	function load($file = '')
	{
		if (in_array($file, $this->is_loaded, TRUE))
			return TRUE;

		include(APPPATH.'config/'.$file.EXT);
		$this->config = array_merge($this->config, $config);
		unset($config);

		$this->is_loaded[] = $file;

		return TRUE;
	}
  	
	// --------------------------------------------------------------------

	/**
	 * Fetch a config file item
	 *
	 *
	 * @access	public
	 * @param	string	the config item name
	 * @return	string
	 */		
	function item($item)
	{			
		return $this->config[$item];
	}
  	
  	// --------------------------------------------------------------------

	/**
	 * Fetch a config file item - adds slash after item
	 *
	 * The second parameter allows a slash to be added to the end of
	 * the item, in the case of a path.
	 *
	 * @access	public
	 * @param	string	the config item name
	 * @return	string
	 */		
	function slash_item($item)
	{
		if ( ! isset($this->config[$item]))
		{
			return FALSE;
		}
		
		$pref = $this->config[$item];
		
		if ($pref != '')
		{			
			if (ereg("/$", $pref) === FALSE)
			{
				$pref .= '/';
			}
		}

		return $pref;
	}

	// --------------------------------------------------------------------
	 
	/**
	 * Site URL
	 *
	 * @access      public
	 * @param       string  the URI string
	 * @return      string
	 */
	function site_url($uri = '')
	{
	        if (is_array($uri))
	        {
	                $uri = implode('/', $uri);
	        }
	
	        if ($uri == '')
	        {
	                return $this->slash_item('base_url').$this->item('index_page');
	        }
	        else
	        {
	                $suffix = ($this->item('url_suffix') == FALSE) ? '' : $this->item('url_suffix');
	                return $this->slash_item('base_url').$this->slash_item('index_page').preg_replace("|^/*(.+?)/*$|", "\\1", $uri).$suffix;
	        }
	}
}

// END CI_Config class
?>
