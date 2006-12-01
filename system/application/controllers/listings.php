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
		
		// This array gets sent to the view listings_view.php
		$data = Array ();
		
		// I don't trust users to set their clocks properly
		$data['server_dt'] = time(); 
		// Set title and other such
		$data['title'] = 'Listing viewer prototype';
		
		// define some dummy events with a rough schema until we have access
		// to some real data to play with
		$data['dummies'] = array (
			array (
				'ref_id' => '1',
				'name' => 'House Party',
				'start' => mktime(18,30,0, 10,28,6),
				'end'   => mktime( 4,30,0, 10,29,6),
				'date' => '2006-11-24',
				'starttime' => '2100',
				'endtime' => '0000',
				'system_update_ts' => '3',
				'user_update_ts' => '2',
				'blurb' => 'Bangin\' house party in my house!',
				'shortloc' => 'my house',
				'type' => 'social'
			),
			array (
				'ref_id' => '2',
				'name' => 'boring lecture about vegetables',
				'start' => mktime(12,30,0, 10,29,6),
				'end'   => mktime(17,30,0, 10,29,6),
				'date' => '2006-11-21',
				'starttime' => '1245',
				'endtime' => '1500',
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'this will be well good i promise',
				'shortloc' => 'L/049',
				'type' => 'academic'
			)
		);
		
		// Slice up the events at 4:00am
		$data['dummies'] = $this->event_manager->SliceOccurrences(
				$data['dummies'], 4*60);
		
		/*
		 * $data['dummies'] is an array of event occurrences:
		 *  'start':       timestamp of start of occurrence
		 *  'end':         timestamp of end of occurrence
		 *  'slice_start': timestamp of start of slice
		 *  'slice_end':   timestamp of end of slice
		 */
		
		// Turn times into Academic_times for max flexibility.
		// When stringified, the timestamps emerge.
		foreach ($data['dummies'] as $slice_data) {
			$slice_data['start'] = new Academic_time($slice_data['start']);
			$slice_data['end'] = new Academic_time($slice_data['end']);
			$slice_data['slice_start'] = new Academic_time($slice_data['slice_start']);
			$slice_data['slice_end'] = new Academic_time($slice_data['slice_end']);
		}
		
		$this->load->view('listings_view',$data);
	}
}
?>