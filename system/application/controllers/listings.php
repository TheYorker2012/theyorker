<?php

/**
 * @brief Controller for event manager.
 * @author David Harker (dhh500@york.ac.uk)
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class Listings extends Controller {

	/**
	 * @brief Default constructor.
	 */
	function Listings()
	{
		parent::Controller();
		
		// Used for processing the events
		$this->load->library('event_manager');
		
		// Used for producing friendly date strings
		$this->load->library('academic_calendar');
	}
	
	/**
	 * @brief Default function.
	 */
	function index()
	{
		// Sorry about the clutter, this will be moved in a bit but it isn't
		// practical to put it in the view
		$extra_head = <<<EXTRAHEAD
			<script src="/javascript/prototype.js" type="text/javascript"></script>
			<script src="/javascript/scriptaculous.js" type="text/javascript"></script>
			<script src="/javascript/listings.js" type="text/javascript"></script>
			<link href="/stylesheets/listings.css" rel="stylesheet" type="text/css" />
EXTRAHEAD;
		
		
		// Load my "minitemplater" helper.
		// This is a very basic S&R script
		// : Allows chunks of template code to be parsed without cluttering
		// up the script :)
		$this->load->helper('minitemplater');
		
		// This array get sent to the view listings_view.php
		$data = Array();
		
		// I don't trust users to set their clocks properly
		$data['server_dt'] = time(); 
		// Set title and other such
		$data['title'] = 'Listing viewer prototype';
		
		// this is temporary for testing only
		$data['days'] = array();
		$daycalc = array();
		$dayofweek = date('N',time ()) - 1;
		$monday = strtotime('-'.$dayofweek.'day', time());
		for ($dayoffset = 0; $dayoffset < 7; $dayoffset++) {
			$day_ts = strtotime('+'.$dayoffset." day",$monday);
			
			$data['days'][] = date ("jS M", $day_ts);
			$daycalc[] = date ('d#m#y',$day_ts);
		}
		
		// define some dummy events with a rough schema until we have access
		// to some real data to play with
		$events = array (
			array (
				'ref_id' => '1',
				'name' => 'House Party',
				'start' => mktime(21, 0,0, 12, 4,2006),
				'end'   => mktime( 0, 0,0, 12, 5,2006),
				'system_update_ts' => '3',
				'user_update_ts' => '2',
				'blurb' => 'Bangin\' house party in my house!',
				'shortloc' => 'my house',
				'type' => 'social'
			),
			array (
				'ref_id' => '2',
				'name' => 'Boring lecture about vegetables',
				'start' => mktime(12,45,0, 12, 8,2006),
				'end'   => mktime(15, 0,0, 12, 8,2006),
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'this will be well good i promise',
				'shortloc' => 'L/049',
				'type' => 'academic'
			),
			array (
				'ref_id' => '3',
				'name' => 'Social Gathering',
				'start' => mktime(21, 0,0, 12, 4,2006),
				'end'   => mktime( 0, 0,0, 12, 5,2006),
				'system_update_ts' => '3',
				'user_update_ts' => '2',
				'blurb' => 'Bangin\' house party in my house!',
				'shortloc' => 'my house',
				'type' => 'social'
			),
			array (
				'ref_id' => '4',
				'name' => 'Mince Pies and Punch',
				'start' => mktime(12,45,0, 12, 8,2006),
				'end'   => mktime(15, 0,0, 12, 8,2006),
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'this will be well good i promise',
				'shortloc' => 'L/049',
				'type' => 'academic'
			),
			array (
				'ref_id' => '5',
				'name' => 'International talk-like-a-pirate day',
				'start' => mktime(21, 0,0, 12, 5,2006),
				'end'   => mktime( 0, 0,0, 12, 6,2006),
				'system_update_ts' => '3',
				'user_update_ts' => '2',
				'blurb' => 'Bangin\' house party in my house!',
				'shortloc' => 'my house',
				'type' => 'social'
			),
			array (
				'ref_id' => '2',
				'name' => 'boring lecture about vegetables',
				'start' => mktime(12,45,0, 12, 8,2006),
				'end'   => mktime(15, 0,0, 12, 8,2006),
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'this will be well good i promise',
				'shortloc' => 'L/049',
				'type' => 'academic'
			),
			array (
				'ref_id' => '1',
				'name' => 'House Party',
				'start' => mktime(21, 0,0, 12, 4,2006),
				'end'   => mktime( 0, 0,0, 12, 5,2006),
				'system_update_ts' => '3',
				'user_update_ts' => '2',
				'blurb' => 'Bangin\' house party in my house!',
				'shortloc' => 'my house',
				'type' => 'social'
			),
			array (
				'ref_id' => '1',
				'name' => 'House Party',
				'start' => mktime(21, 0,0, 12, 5,2006),
				'end'   => mktime( 6, 0,0, 12, 6,2006),
				'system_update_ts' => '3',
				'user_update_ts' => '2',
				'blurb' => 'Bangin\' house party in my house!',
				'shortloc' => 'my house',
				'type' => 'social'
			),
			array (
				'ref_id' => '2',
				'name' => 'boring lecture about vegetables',
				'start' => mktime(12,45,0, 12, 6,2006),
				'end'   => mktime(15, 0,0, 12, 6,2006),
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'this will be well good i promise',
				'shortloc' => 'L/049',
				'type' => 'academic'
			),
			array (
				'ref_id' => '2',
				'name' => 'MARATHON Noodle eating contest',
				'start' => mktime(12,45,0, 12, 6,2006),
				'end'   => mktime(15, 0,0, 12, 6,2006),
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'Noodleishous!',
				'shortloc' => 'L/049',
				'type' => 'academic'
			),
			array (
				'ref_id' => '2',
				'name' => 'Regional \'Pong\' championships, semi final',
				'start' => mktime(12,45,0, 12, 6,2006),
				'end'   => mktime(15, 0,0, 12, 6,2006),
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => '|&nbsp;.&nbsp;&nbsp;&nbsp;&nbsp;|<br />
							|&nbsp;&nbsp;.&nbsp;&nbsp;&nbsp;|<br />
							|&nbsp;&nbsp;&nbsp;.&nbsp;&nbsp;|<br />
							|&nbsp;&nbsp;&nbsp;&nbsp;.&nbsp;|<br />
							|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.|<br />
							|&nbsp;&nbsp;&nbsp;&nbsp;.&nbsp;|<br />
							|&nbsp;&nbsp;&nbsp;.&nbsp;&nbsp;|<br />etc.',
				'shortloc' => 'L/049',
				'type' => 'academic'
			),
			array (
				'ref_id' => '2',
				'name' => 'Better than vegetables',
				'start' => mktime(18, 0,0, 12, 7,2006),
				'end'   => mktime(20, 0,0, 12, 7,2006),
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'Just a few pints',
				'shortloc' => 'McQ\'s',
				'type' => 'social'
			)
		);
		
		$data['dummies'] = $this->_ProcessEvents($events, $daycalc);
		
		$pass_data['subdata'] = $data;
		$pass_data['extra_head'] = $extra_head;
		$pass_data['content_view'] = "listings/listings";
		// load crazy frame deely		
		$this->load->view('frames/student_frame',$pass_data);
		
		//$this->load->view('listings_view',$data);
	}
	
	/**
	 * @brief Process events ready for the view.
	 * @param array $Occurrences Array of event occurrences (using timestamps).
	 * @param array $DayCalc Visible calendar information.
	 * @return array similar to @a $Occurrences with the following extra fields:
	 *	- 'date' (the date of the start of the slice formatted as 'Y-m-d').
	 *	- 'day' (the day index on the visible calendar (using @a $DayCalc).
	 *	- 'starttime' (the start time of the occurrence using user preferences to format).
	 *	- 'endtime' (the end time of the occurrence using user preferences to format).
	 */
	function _ProcessEvents($Occurrences, $DayCalc)
	{
		// Slice up the events
		$event_occurrences = $this->event_manager->SliceOccurrences($Occurrences, 4*60);
		// Perform date formatting
		$return_array = array();
		foreach ($event_occurrences as $event_index => $event_data) {
			// Convert timestamps to Academic_times so its easier to extract
			// date and time in certain formats
			$event_data['start']       = new Academic_time($event_data['start']);
			$event_data['end']         = new Academic_time($event_data['end']);
			$event_data['slice_start'] = new Academic_time($event_data['slice_start']);
			$event_data['slice_end']   = new Academic_time($event_data['slice_end']);
			
			// Produce and new 'date' field with string date
			$event_data['date'] = $event_data['slice_start']->Format('Y-m-d');
			
			// Which day of the current view?
			$event_data['day'] = $this->_GetDowOffset(
					$event_data['slice_start']->Timestamp(),
					$DayCalc);
			
			// Start and end time (use slice start/end for the moment)
			// It should be made obvious when a slice is only part of an event
			$event_data['starttime'] = $event_data['start']->Time();
			$event_data['endtime'] = $event_data['end']->Time();
			
			// Add to the return array
			$return_array[] = $event_data;
		}
		return $return_array;
	}
	
	/**
	 * @brief Find which day of the visible calendar a timestamp lies in.
	 * @param timestamp $Timestamp Timestamp to find.
	 * @param array $DayCalc Visible calendar information.
	 * @return Array index:
	 *	- Index of a day in @a $DayCalc that matches @a $Timestamp.
	 *	- -1 if no matches are found.
	 *
	 * Returns the day of the week that a date falls on if
	 *	that date is within the range of the current calendar
	 */
	function _GetDowOffset($Timestamp, $DayCalc) {
		$formatted_time = date('d#m#y',$Timestamp);
		foreach ($DayCalc as $offset => $calendar_date) {
			if ($formatted_time == $calendar_date) {
				return $offset;
			}
		}
		return -1;
	}
}
?>