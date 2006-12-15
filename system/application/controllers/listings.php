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
		
		// Load libraries
		$this->load->library('event_manager');      // Processing events
		$this->load->library('academic_calendar');  // Using academic calendar
		$this->load->library('date_uri');           // Nice date uri segments
		$this->load->library('frame_public');       // Main public frame
		$this->load->library('view_listings_days'); // Days listings view
		
		date_default_timezone_set(Academic_time::InternalTimezone());
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
	 *
	 * Will look for a date in the uri using Date_uri.
	 */
	function week()
	{
		// Read the uri
		$uri_result = $this->date_uri->ReadUri(3);
		
		if ($uri_result['valid']) {
			// Valid
			$base_time = $uri_result['date'];
			$format = $uri_result['format'];
			
			$monday = $base_time->BackToMonday();
			
			$this->_ShowCalendar(
					$monday, 7,
					'listings/week/', $format
				);
			return;
			
		} else {
			// Invalid
			$format = 'academic-multiple';
			$base_time = new Academic_time(time());
			
			$monday = $base_time->BackToMonday();
		
			$this->_ShowCalendar(
					$monday, 7,
					'listings/week/', $format
				);
		}
	}
	
	/**
	 * @brief Show the calendar between certain Academic_times.
	 * @param $StartTime Academic_time Start date.
	 * @param $Days integer Number of days to display.
	 * @param $UriBase string The base of the uri on which to build links,
	 *	e.g. 'listings/week/'
	 * @param $UriFormat string Uri date format identifier as used in Date_uri.
	 */
	function _ShowCalendar($StartTime, $Days, $UriBase, $UriFormat)
	{
		// Sorry about the clutter, this will be moved in a bit but it isn't
		// practical to put it in the view
		$extra_head = <<<EXTRAHEAD
			<script src="/javascript/prototype.js" type="text/javascript"></script>
			<script src="/javascript/scriptaculous.js" type="text/javascript"></script>
			<script src="/javascript/listings.js" type="text/javascript"></script>
			<link href="/stylesheets/listings.css" rel="stylesheet" type="text/css" />
EXTRAHEAD;
		
		// Set up the days view
		$view_listings_days = new ViewListingsDays();
		$view_listings_days->SetUriBase($UriBase);
		$view_listings_days->SetUriFormat($UriFormat);
		$view_listings_days->SetRange($StartTime, $Days);
		// Get the data from the db, then we're ready to load
		$view_listings_days->Retrieve();
		
		// Set up the public frame to use the listings view
		$this->frame_public->SetTitle('Listing viewer prototype');
		$this->frame_public->SetExtraHead($extra_head);
		$this->frame_public->SetContent($view_listings_days);
		
		// Load the public frame view
		$this->frame_public->Load();
	}
	
	
}
?>