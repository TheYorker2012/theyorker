<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/// Time format information.
class Time_format
{
	/// Time format to use.
	public $TimeFormat = 'g:i a';
	/// Date format to use.
	public $DateFormat = 'l, jS F Y';
	
	/// Default constructor.
	function __construct()
	{
		$CI = & get_instance();
		$CI->load->model('user_auth');
		if ($CI->user_auth->timeFormat == 24) {
			$this->TimeFormat = 'H:i';
		}
	}
	
	/// Format a date.
	/**
	 * @param $format Format string with extra detectables:
	 *	- '%T' is replaced with the time in the user preferences format.
	 *	- '%D' is replaced with teh date in the user preferences format.
	 */
	function date($format, $timestamp = NULL)
	{
		if (NULL == $timestamp) {
			$timestamp = time();
		}
		$format = str_replace('%T', $this->TimeFormat, $format);
		$format = str_replace('%D', $this->DateFormat, $format);
		return date($format, $timestamp);
	}
};

?>