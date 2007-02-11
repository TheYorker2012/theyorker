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
		
		// Load libraries
		$this->load->library('event_manager');      // Processing events
		$this->load->library('academic_calendar');  // Using academic calendar
		$this->load->library('date_uri');           // Nice date uri segments
		$this->load->library('view_calendar_days'); // Days calendar view
		
		
		// Show change events
		$this->load->model('calendar/events_model');
		$ChangedEvents = new EventOccurrenceFilter();
		$ChangedEvents->DisableFilter('private');
		//$ChangedEvents->DisableFilter('active');
		$ChangedEvents->DisableFilter('show');
		$ChangedEvents->SetSpecialCondition(
			'event_occurrence_users.event_occurrence_user_timestamp <
					event_occurrences.event_occurrence_timestamp');
		$notices = $ChangedEvents->GenerateOccurrences(array(
				'state'			=> $ChangedEvents->ExpressionPublicState(),
				'name'			=> 'events.event_name',
				'organisation'	=> 'organisations.organisation_name',
				'start'			=> 'UNIX_TIMESTAMP(event_occurrences.event_occurrence_start_time)',
			));
		foreach ($notices as $notice) {
			$date = new Academic_time($notice['start']);
			$start = $date->Format('l') . ' of week ' . $date->AcademicWeek() .
				' of the ' . $date->AcademicTermName() . ' ' . $date->AcademicTermTypeName();
			
			$message = 'The event ' . $notice['name'] . ' from ' .
				$notice['organisation'] . ' (' . $start . ') has been ' . $notice['state'];
			$this->messages->AddMessage('information', $message, FALSE);
		}
		
		
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
		// the 0-6 (mon-sun) day code
		$day = $this->uri->segment(3);

		// this is the value that should be passed with each event to allow
		// it to be uniquely identified back at your end here.
		$refid = $this->uri->segment(4);

		
		// The operation that is being carried out
		// Will be HIDE or SHOW at the moment
		// "Phase 2" might see this include operations like SUBSCRIBE etc.
		// however response will be XML, and this doesn't need doing in that
		// way for basic functioning.
		$op = $this->uri->segment(5);
		
		/*
		Code to save the data from the script based on the above to variables
		and echo $refid|OK|message or $refid|FAIL|message depending on the outcome
		examples:
			75|OK|Event <b>$title</b> has been hidden from your calendar
			75|FAIL|You are not logged in
		*/
		$success = FALSE;
		$message = '';
		
		// Load the model
		$this->load->model('calendar/events_model');
		
		// Perform the operation putting status in $success and filling $message
		if ($op === 'HIDE') {
			$success = $this->events_model->OccurrenceHide();
			if ($success) {
				$message = 'Event hidden from your calendar';
			} else {
				$message = 'Could not hide event from your calendar';
			}
			
		} elseif ($op === 'SHOW') {
			$success = $this->events_model->OccurrenceShow();
			if ($success) {
				$message = 'Event shown in your calendar';
			} else {
				$message = 'Could not show event in your calendar';
			}
			
		} elseif ($op === 'RSVP') {
			$success = $this->events_model->OccurrenceRsvp();
			if ($success) {
				$message = 'RSVP\'d event';
			} else {
				$message = 'Could not RSVP event';
			}
		}
		
		$status = ($success?'OK':'FAIL');
		// Send the data back to the js
		$this->load->view('calendar_blank',
				array ('replyString' => $day.'|'.$refid.'|'.$status.'|'.$message)
			);
		
	}
	
}
?>
