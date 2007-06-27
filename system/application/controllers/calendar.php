<?php

/**
 * @file calendar.php
 * @brief Calendar controller.
 */

include_once(BASEPATH.'application/helpers/calendar_control.php');

/// Controller for event manager.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @version 20/03/2007 James Hogan (jh559)
 *	- Doxygen tidy up.
 */
class Calendar extends CalendarController
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct('public');
	}
	
	function actions()
	{
		if (!CheckPermissions('student')) return;
		
		// do the magic, use calendar_actions as a controller
		$this->load->model('calendar/calendar_actions');
		$args = func_get_args();
		$func = array_shift($args);
		if ('_' !== substr($func,0,1) && method_exists($this->calendar_actions, $func)) {
			call_user_func_array(array(&$this->calendar_actions, $func), $args);
		} else {
			show_404();
		}
	}
}
?>
