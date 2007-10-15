<?php

/**
 * @file calendar.php
 * @brief Calendar controller.
 */

/// Controller for event manager.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @version 20/03/2007 James Hogan (jh559)
 *	- Doxygen tidy up.
 * @version 27/07/2007 James Hogan (jh559)
 *	- Major shift of stuff into helpers/calendar_control
 */
class Calendar extends controller
{
	/// Default constructor.
	function __construct()
	{
		parent::controller();
	}
	
	function _remap()
	{
		$this->load->model('subcontrollers/calendar_subcontroller');
		$this->calendar_subcontroller->_SetDefault('index');
		$this->calendar_subcontroller->_AddPermission('create', 'edit', 'index');
		$this->calendar_subcontroller->_map(func_get_args());
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
