<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Calendar_view_todo_list.php
 * @brief Calendar view for the todo list.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @pre loaded(library Calendar_frontend)
 *
 * To-do list.
 *
 * @version 29-03-2007 James Hogan (jh559)
 *	- Created.
 */

/// To-do list calendar view class.
class CalendarViewTodoList extends CalendarView
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct('calendar/todo_list');
		
		$CI = & get_instance();
		
		$this->SetData('InlineAdder', TRUE);
		$this->SetData('InlineAdderTarget', site_url('calendar/actions/add/todo'.$CI->uri->uri_string()));
	}
	
	/// Disable the cunning inline todo adder.
	function DisableInlineAdder()
	{
		$this->SetData('InlineAdder', FALSE);
	}
	
	/// Process the calendar data to produce view data.
	/**
	 * @param $Data CalendarData Calendar data.
	 * @param $Categories array[category] Array of categories.
	 *
	 * This should be the data which is specific to the view.
	 * General data such as day information should be calculated then passed in.
	 */
	protected function ProcessEvents(&$Data, $Categories)
	{
		$items = $Data->GetTodoOccurrences();
		
		$this->SetData('Items', $items);
	}
}

/// Dummy class.
class Calendar_view_todo_list
{

}


?>