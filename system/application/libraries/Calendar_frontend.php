<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Calendar_frontend.php
 * @brief Front end of calendar framework.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @pre loaded(library frames)
 *
 * Calendar data processing and display classes.
 *
 * @version 29-03-2007 James Hogan (jh559)
 *	- Created.
 */

/// Main calendar display class.
abstract class CalendarView extends FramesView
{
	/// array[category] Stored array of categories.
	private $mCategories = NULL;
	/// CalendarData Stored calendar data.
	private $mData = NULL;

	/// Primary constructor.
	/**
	 * @param $ViewFile string Path of the view file to use.
	 */
	function __construct($ViewFile)
	{
		parent::__construct($ViewFile);
	}
	
	/// Set the categories to use.
	/**
	 * @param $Categories array[category] Array of categories.
	 */
	function SetCategories($Categories)
	{
		$this->mCategories = $Categories;
	}
	
	/// Set the actual calendar data to use.
	/**
	 * @param $Data CalendarData Calendar data.
	 */
	function SetCalendarData(&$Data)
	{
		$this->mData = &$Data;
	}
	
	/// Process the calendar data to produce view data.
	/**
	 * @param $Data CalendarData Calendar data.
	 * @param $Categories array[category] Array of categories.
	 *
	 * This should be the data which is specific to the view.
	 * General data such as day information should be calculated then passed in.
	 */
	protected abstract function ProcessEvents(&$Data, $Categories);
	
	/// Load the view.
	function Load()
	{
		/// Process the data before loading
		$this->ProcessEvents($this->mData, $this->mCategories);
		parent::Load();
	}
}

/// Dummy class
class Calendar_frontend
{
	/// @todo Get days in range etc.
}

?>