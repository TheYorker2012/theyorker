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
 * @version		0.9
 * @filesource
 */
 
// ------------------------------------------------------------------------


$obj =& get_instance();
define('RAPYD_PATH', APPPATH."rapyd/");
define('RAPYD_DIR',  $obj->config->system_url()."application/rapyd/");
define('RAPYD_IMAGES',  $obj->config->system_url()."application/rapyd/images/");



/**
 * helpers inclusion
 */
require(RAPYD_PATH.'helpers/datehelper'.EXT);	
require(RAPYD_PATH.'helpers/html'.EXT);	
require(RAPYD_PATH.'helpers/highlight'.EXT);	
require(RAPYD_PATH.'classes/rapyd_session'.EXT);
require(RAPYD_PATH.'classes/rapyd_lang'.EXT);


/**
 * common inclusion
 */
require(RAPYD_PATH.'common'.EXT);



/**
 * rapyd library main class
 *
 * @package    rapyd.components
 * @access     public
 */
	class Rapyd {
	
		var $config = array();
		var $load;
		
    var $js = array();
    var $css = array();
    var $script = array();
    var $style = array();
		
		function Rapyd()
		{
			$this->ci =& get_instance();
      
      $rpd = array();
      if (file_exists(APPPATH.'config/rapyd'.EXT))
      {
        include(APPPATH.'config/rapyd'.EXT);
      }
      
			$this->config = new rapyd_config($rpd);
			$this->session = new rapyd_session();
			$this->language = new rapyd_lang($this);

		}
    
		function load()
		{
			$components = func_get_args();
			foreach($components as $component)
			{
			  $this->language->load_language();
				include_once(RAPYD_PATH.'classes/'.$component.EXT);
				
			}
		}

		function set_view_path()
		{
			$this->ci->load->_ci_view_path = RAPYD_PATH.'views/'.$this->config->item("theme").'/';
		}
		
		function reset_view_path()
		{
			$this->ci->load->_ci_view_path = APPPATH.'views/';
		}


		function get_head(){
			
			$buffer = "";
			
			//css links
			foreach ($this->css as $css){
				$buffer .= HTML::cssLinkTag($css);
			}
			
			//javascript links
			foreach ($this->js as $js){
				$buffer .= HTML::javascriptLinkTag($js);
			}

			//javascript in page
			$script = join("\n\n",$this->script)."\n";
			$buffer .= HTML::javascriptTag($script);

			//style in page
			$this->set_view_path();
			$this->style[] = $this->ci->load->view("css",null,true);
			$this->reset_view_path();
			$style = join("\n\n",$this->style)."\n";
			$buffer .= HTML::cssTag($style); 

			return $buffer;
		}

	}

/**
 * rapyd config class
 *
 * @package    rapyd.components
 * @access     private
 */
	class rapyd_config{
	
		var $config = array();
		
		function rapyd_config(&$config)
		{
			$this->config = &$config;
      
		}

		function item($item)
		{

			return $this->config[$item];
		}

		function set_item($item, $value)
		{
			$this->config[$item] = $value;
		}
		
	}




?>