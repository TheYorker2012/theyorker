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
 * Output Class
 *
 * Responsible for sending final output to browser, adapted to support multiple users
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Output
 * @author		Rick Ellis
 * @author		Mark Goodall <mark.goodall@gmail.com>
 * @link		http://www.codeigniter.com/user_guide/libraries/output.html
 */
class CI_Output {

	var $final_output;
	var $cache_expiration_all	= 0;
	var $cache_expiration_id = 0;
	var $headers 			= array();
	var $enable_profiler 	= FALSE;
	var $entityId = 0;
	var $key = 'default';
	


	function CI_Output()
	{
		log_message('debug', "Output Class Initialized");
		
		session_start();
		if (isset($_SESSION['ua_loggedin']) && $_SESSION['ua_loggedin'])
			$this->entityId = $_SESSION['ua_entityId'];
	}

	function get_output()
	{
		return $this->final_output;
	}

	function set_output($output)
	{
		$this->final_output = $output;
	}

	function set_header($header)
	{
		$this->headers[] = $header;
	}

	function enable_profiler($val = TRUE)
	{
		$this->enable_profiler = (is_bool($val)) ? $val : TRUE;
	}

	function cache($timeAll = 10, $timeId = 0.5, $everyone = false) {
		$this->cache_expiration_all = ( ! is_numeric($timeAll)) ? 0 : $timeAll;
		$this->cache_expiration_id = ( ! is_numeric($timeId)) ? 0 : $timeId;
		$this->entityId = ($everyone) ? 0 : $this->entityId;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Display Output
	 *
	 * All "view" data is automatically put into this variable by the controller class:
	 *
	 * $this->final_output
	 *
	 * This function sends the finalized output data to the browser along
	 * with any server headers and profile data.  It also stops the
	 * benchmark timer so the page rendering speed and memory usage can be shown.
	 *
	 * @access	public
	 * @return	mixed
	 */		
	function _display($output = '')
	{	
		// Note:  We use globals because we can't use $CI =& get_instance()
		// since this function is sometimes called by the caching mechanism,
		// which happens before the CI super object is available.
		global $BM, $CFG;
		
		if (isset($_SESSION['ua_loggedin']) && $_SESSION['ua_loggedin'])
			$this->entityId = $_SESSION['ua_entityId'];
		
		// --------------------------------------------------------------------

		// Set the output data
		if ($output == '')
		{
			$output =& $this->final_output;
		}

		// --------------------------------------------------------------------

		// Do we need to write a cache file?
		if (($this->cache_expiration_id != 0 and $this->entityId != 0) or $this->cache_expiration_all !=0)
			$this->_write_cache($output);
		

		// --------------------------------------------------------------------

		// Parse out the elapsed time and memory usage,
		// then swap the pseudo-variables with the data
				
		$elapsed = $BM->elapsed_time('total_execution_time_start', 'total_execution_time_end');
		$output = str_replace('{elapsed_time}', $elapsed, $output);
		
//		$memory	 = ( ! function_exists('memory_get_usage')) ? '0' : round(memory_get_usage()/1024/1024, 2).'MB';
//		$output = str_replace('{memory_usage}', $memory, $output);		

		// --------------------------------------------------------------------
		
		// Is compression requested?
		if ($CFG->item('compress_output') === TRUE && extension_loaded('zlib')) {
			if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
			{
				ob_start('ob_gzhandler');
			}
		}

		// Profiling with FirePHP (replaces CI code)
		if ( !function_exists('get_instance') && $this->enable_profiler == TRUE) {
			$CI =& get_instance();
			// this is a pear library and needs to be available in the include path
			require_once('FirePHP_Build/Init.inc.php');
			FirePHP::SetAccessKey(md5($CI->uri->uri_string()));
			FirePHP::Init();
		}

		// --------------------------------------------------------------------

		// Are there any server headers to send?
		if (count($this->headers) > 0)
			foreach ($this->headers as $header)
				@header($header);


		// --------------------------------------------------------------------
		
		// Does the get_instance() function exist?
		// If not we know we are dealing with a cache file so we'll
		// simply echo out the data and exit.
		if ( ! function_exists('get_instance')) {
			echo $output;
			log_message('debug', "Final output sent to browser");
			log_message('debug', "Total execution time: ".$elapsed);
			return true;
		}

		// --------------------------------------------------------------------

		if (!isset($CI))
			$CI =& get_instance();

		// Does the controller contain a function named _output()?
		// If so send the output there.  Otherwise, echo it.
		if (method_exists($CI, '_output'))
		{
			$CI->_output($output);
		}
		else
		{
			echo $output;  // Send it to the browser!
		}
		
		log_message('debug', "Final output sent to browser");
		log_message('debug', "Total execution time: ".$elapsed);		
	}

	function _write_cache($output) {
		$CI =& get_instance();
		
		if (isset($_SESSION['ua_loggedin']) && $_SESSION['ua_loggedin'])
			$this->entityId = $_SESSION['ua_entityId'];
		
		$path = $CI->config->item('cache_path');

		$cache_path = ($path == '') ? BASEPATH.'cache/' : $path;
		
		if ( ! is_dir($cache_path) OR ! is_writable($cache_path)) return;

		$uri = md5($CI->config->item('base_url').$CI->config->item('index_page').$CI->uri->uri_string());
		$cache_path.= $this->entityId.'/';
		
		if (!is_dir($cache_path)) {
			if(!@mkdir($cache_path)) return;
		}

		$cache_path.= $uri;
		if (is_array($_POST) && count($_POST) != 0) {
			$cache_path.= '_'.md5(serialize($_POST));
		}

		if ( ! $fp = @fopen($cache_path, 'wb'))
		{
			log_message('error', "Unable to write cache file: ".$cache_path);
			return;
		}

		if ($this->entityId == 0) {
			$expire = time() + ($this->cache_expiration_all * 60);
		} else {
			$expire = time() + ($this->cache_expiration_id * 60);
		}

		if (!@flock($fp, LOCK_EX)) {
			log_message('error', "Unable to lock cache file (possible concurrent update?): ".$cache_path);
			return;
		}
		fwrite($fp, '<---'.$expire.'TS--->'.$output);
		flock($fp, LOCK_UN);
		fclose($fp);
		@chmod($cache_path, 0770);

		log_message('debug', "Cache file written: ".$cache_path);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Update/serve a cached file
	 *
	 * @access	public
	 * @return	void
	 */	
	function _display_cache(&$CFG, &$RTR) {
		$CFG =& load_class('Config');
		$RTR =& load_class('Router');
		$IN =& load_class('Input');

		$cache_path = ($CFG->item('cache_path') == '') ? BASEPATH.'cache/' : $CFG->item('cache_path');
			
		if ( ! is_dir($cache_path) OR ! is_writable($cache_path)) {
			return FALSE;
		}

		$uri = md5($CFG->item('base_url').$CFG->item('index_page').$RTR->uri_string);

		if (is_array($_POST) && count($_POST) != 0) {
			$uri.= '_'.md5(serialize($_POST));
		}

		if (@file_exists($cache_path.$this->entityId.'/'.$uri)) {
			$filepath = $cache_path.$this->entityId.'/'.$uri;
		} elseif(@file_exists($cache_path.'0/'.$uri)) {
			$filepath = $cache_path.'0/'.$uri;
		} else return false;

		if (filesize($filepath) > 0 && !$cache = @file_get_contents($filepath))
			return false;

		// Strip out the embedded timestamp
		if ( ! preg_match("/(<---\d+TS--->)/", $cache, $match)) {
			return FALSE;
		}
		
		// Has the file expired? If so we'll delete it.
		if (time() >= trim(str_replace(array('<---', 'TS--->'), '', $match['1']))) {
			@unlink($filepath) or log_message('error', "Cache file cannot be deleted! Panic!");
			log_message('debug', "Cache file has expired. File deleted");
			return FALSE;
		}

		$this->_display(str_replace($match['0'], '', $cache));
		log_message('debug', "Cache file is current. Sending it to browser.");
		return TRUE;
	}


}
// END Output Class
?>