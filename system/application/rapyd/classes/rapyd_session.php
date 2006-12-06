<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Rapyd Components
 *
 * An open source library for CodeIgniter application development framework for PHP 4.3.2 or newer
 *
 * @package		rapyd.components
 * @author		Felice Ostuni <felix@rapyd.com>
 * @license		http://www.fsf.org/licensing/licenses/lgpl.txt LGPL
 * @copyright	Copyright (c) 2006 Felice Ostuni - http://www.rapyd.com
 * @version		0.7
 * @filesource
 */
 
 
	class rapyd_session {

		// constructor
		function rapyd_session()
		{
				if (session_id() == "") session_start();
				  //header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
		}

		function save($var, $val, $namespace = 'default') {
			if ($var == null)
			{
				$_SESSION[$namespace] = $val;
			} else {
				$_SESSION[$namespace][$var] = $val;
			}
		}
		
		function get($var = null, $namespace = 'default')
		{
			if(isset($var))
			{
				return isset($_SESSION[$namespace][$var]) ? $_SESSION[$namespace][$var] : null;
			} else {
				return isset($_SESSION[$namespace]) ? $_SESSION[$namespace] : null;
			}
		}
		
		function clear($var = null, $namespace = 'default')
		{
			if (isset($_SESSION[$namespace])){
				if(isset($var) && ($var !== null))
					unset($_SESSION[$namespace][$var]);
				else
					unset($_SESSION[$namespace]);
			}
		}
		


	}
?>