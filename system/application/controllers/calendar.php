<?php

/**
 * @brief Controller for event manager.
 * @author David Harker (dhh500@york.ac.uk)
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class Calendar extends Controller {

	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
		
		// Load libraries
		$this->load->library('event_manager');      // Processing events
		$this->load->library('academic_calendar');  // Using academic calendar
		$this->load->library('date_uri');           // Nice date uri segments
		$this->load->library('view_calendar_days'); // Days calendar view
	}
	
	/**
	 * @brief Default function.
	 */
	function index()
	{
		$this->week();
	}
	
	/**
	 * @brief Show the calendar between certain dates.
	 * @param $DateRange string Single date accepted by Date_uri.
	 */
	function week($DateRange = '')
	{
		if (!CheckPermissions('public')) return;
		
		$this->pages_model->SetPageCode('calendar_personal');
		if (!empty($DateRange)) {
			// $DateRange Not empty
			
			// Read the date, only allowing a single date (no range data)
			$uri_result = $this->date_uri->ReadUri($DateRange, FALSE);
			if ($uri_result['valid']) {
				// $DateRange Valid
				$start_time = $uri_result['start'];
				$start_time = $start_time->BackToMonday();
				$format = $uri_result['format']; // Use the format in all links
				$days = 7; // force 7 days until view can handle different values.
				
				$this->_ShowCalendar(
						$start_time, $days,
						'/calendar/week/', $format
					);
				return;
				
			} else {
				// $DateRange Invalid
				$this->main_frame->AddMessage('error','Unrecognised date: "'.$DateRange.'"');
			}
		}
		
		// Default to this week
		$format = 'ac';
		$base_time = new Academic_time(time());
		
		$monday = $base_time->BackToMonday();
	
		$this->_ShowCalendar(
				$monday, 7,
				'/calendar/week/', $format
			);
	}
	
	/**
	 * @brief Show the calendar between certain Academic_times.
	 * @param $StartTime Academic_time Start date.
	 * @param $Days integer Number of days to display.
	 * @param $UriBase string The base of the uri on which to build links,
	 *	e.g. '/calendar/week/'
	 * @param $UriFormat string Uri date format identifier as used in Date_uri.
	 */
	function _ShowCalendar($StartTime, $Days, $UriBase, $UriFormat)
	{
		// Sorry about the clutter, this will be moved in a bit but it isn't
		// practical to put it in the view
		$extra_head = <<<EXTRAHEAD
		
			<script src="/javascript/prototype.js" type="text/javascript"></script>
			<script src="/javascript/scriptaculous.js" type="text/javascript"></script>
			<script src="/javascript/calendar.js" type="text/javascript"></script>
			<link href="/stylesheets/calendar.css" rel="stylesheet" type="text/css" />
			
EXTRAHEAD;
		
		// Set up the days view
		$view_calendar_days = new ViewCalendarDays();
		$view_calendar_days->SetUriBase($UriBase);
		$view_calendar_days->SetUriFormat($UriFormat);
		$view_calendar_days->SetRange($StartTime, $Days);
		// Get the data from the db, then we're ready to load
		$view_calendar_days->Retrieve();
		
		// Set up the public frame to use the messages frame
		$this->main_frame->SetExtraHead($extra_head);
		$this->main_frame->SetContent($view_calendar_days);
		
		// Load the public frame view
		$this->main_frame->Load();
	}
	
	/**
	 * @brief Save any change made at UI to DB and report success or failure
	 */
	function ajaxCalUpdate () {
		// this is the value that should be passed with each event to allow
		// it to be uniquely identified back at your end here.
		$refid == $this->uri->segment(3);
		
		// The operation that is being carried out
		// Will be HIDE or SHOW at the moment
		// "Phase 2" might see this include operations like SUBSCRIBE etc.
		// however response will be XML, and this doesn't need doing in that
		// way for basic functioning.
		$op == $this->uri->segment(4);
		
		/*
		Code to save the data from the script based on the above to variables
		and echo OK|message or FAIL|message depending on the outcome
		examples:
			OK|Event <b>$title</b> has been hidden from your calendar
			FAIL|You are not logged in
		*/
		echo "OK|Dummy message!";
		
	}
	
}
?>
