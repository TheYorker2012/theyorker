<?php

/// Yorker directory.
/**
 * @author Owen Jones (oj502@york.ac.uk)
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * The URI /directory maps to this controller (see config/routes.php).
 *
 * Any 2nd URI segment is sent to Yorkerdirectory::view (see config/routes.php).
 *
 * Any 3rd URI segment (e.g. events) is sent to the function with the same value.
 *	(see config/routes.php).
 */
class Yorkerdirectory extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();

		// Make use of the public frame
		$this->load->library('frame_public');
		$this->load->library('organisations');

		$this->load->model('directory_model');

		$this->load->helper('text');
		$this->load->helper('wikilink');
	}

	/// Set up the directory frame
	/**
	 * @param $OrganisationData Organisation data array.
	 * @pre @a $OrganisationData is valid organisation array.
	 * @post Frame_directory frame is loaded and ready to use.
	 */
	private function _SetupOrganisationFrame($DirectoryEntry)
	{
		$this->load->library('frame_directory');
		$data = $this->organisations->_GetOrgData($DirectoryEntry);
		$navbar = $this->frame_public->GetNavbar();
		if($data['organisation']['type'] == 'Societies')
		{
			$navbar->AddItem('reviews', 'Reviews',
					'/directory/'.$DirectoryEntry.'/reviews');
		}
		$navbar->AddItem('members', 'Members',
				'/directory/'.$DirectoryEntry.'/members');
		$navbar->AddItem('events', 'Events',
				'/directory/'.$DirectoryEntry.'/events');
		$navbar->AddItem('about', 'About',
				'/directory/'.$DirectoryEntry);
	}

	/// Directory index page.
	/**
	 * @note POST data:
	 *	- 'search' (search pattern, optional)
	 */
	function index()
	{
		$this->pages_model->SetPageCode('directory_index');
		
		$data = array();
		
		$data['maintext'] = $this->pages_model->GetPropertyText('maintext');

		// Get the search pattern from POST (optional)
		$search_pattern = $this->input->post('search_directory', TRUE);
		// Get the organisations matching the search and pass the search pattern
		// to the view as well
		$data['organisations'] = $this->organisations->_GetOrgs($search_pattern);
		$data['search'] = $search_pattern;
		
		// Get organisation types
		$data['organisation_types'] = $this->organisations->_GetOrganisationTypes($data['organisations'], TRUE);

		//Libary for AtoZ system
		$this->load->library('character_lib'); //This character libary is used by the view, so load it here

		// Set up the directory view
		$directory_view = $this->frames->view('directory/directory', $data);

		// Set up the public frame to use the directory view
		$this->frame_public->SetContent($directory_view);

		// Load the public frame view
		$this->frame_public->Load();
	}

	/// Directory organisation page.
	function view($organisation)
	{
		$this->pages_model->SetPageCode('directory_view');
		
		$data = $this->organisations->_GetOrgData($organisation);
		$this->_SetupOrganisationFrame($organisation);

		$subpageview='directory/directory_view';
		
		//Reviews
		$this->load->model('articles_model');
		$reviews = $this->articles_model->GetDirectoryOrganisationReviewsByEntryName($organisation);
		// sort into types
		$directory_reviews = array();
		$review_types = array();
		foreach ($reviews as $review) {
			if (NULL === $review['type']) {
				$directory_reviews[] = $review;
			} else {
				if (!array_key_exists($review['type'], $review_types)) {
					$review_types[$review['type']] = array();
				}
				$review_types[$review['type']][] = $review;
			}
		}
		$data['organisation']['reviews_untyped'] = $directory_reviews;
		$data['organisation']['reviews_by_type'] = $review_types;

		// Set up the directory view
		$directory_view = $this->frames->view($subpageview, $data);

		// Set up the directory frame to use the directory events view
		$this->frame_public->SetPage('about');
		$this->frame_directory->SetOrganisation($data['organisation']);
		$this->frame_directory->SetContent($directory_view);
		
		// Set up the public frame to use the directory view
		$this->frame_public->SetTitleParameters(
				array('organisation' => $data['organisation']['name']));
		$this->frame_public->SetContent($this->frame_directory);

		// Load the public frame view
		$this->frame_public->Load();
	}

	/// Directory events page.
	function events($organisation, $DateRange = '')
	{
		$this->pages_model->SetPageCode('directory_events');
		
		$data = $this->organisations->_GetOrgData($organisation);
		$this->_SetupOrganisationFrame($organisation);

		$this->load->model('calendar/events_model');
		
		$this->load->library('view_calendar_select_week');
		$this->load->library('view_calendar_list');
		$this->load->library('date_uri');

		// Sorry about the clutter, this will be moved in a bit but it isn't
		// practical to put it in the view
		$extra_head = <<<EXTRAHEAD
			<script src="/javascript/prototype.js" type="text/javascript"></script>
			<script src="/javascript/scriptaculous.js" type="text/javascript"></script>
			<script src="/javascript/calendar.js" type="text/javascript"></script>
			<link href="/stylesheets/calendar.css" rel="stylesheet" type="text/css" />
EXTRAHEAD;

		$use_default_range = FALSE;
		if (empty($DateRange)) {
			// $DateRange Empty
			$use_default_range = TRUE;

		} else {
			$uri_result = $this->date_uri->ReadUri($DateRange);
			if ($uri_result['valid']) {
				// valid
				$start_time = $uri_result['start'];
				$end_time = $uri_result['end'];
				$format = $uri_result['format'];
				$range_description = $uri_result['description'];

			} else {
				// invalid
				$this->frame_public->AddMessage('error','Unrecognised date range: "'.$DateRange.'"');
				$use_default_range = TRUE;
			}
		}

		if ($use_default_range) {
			// Default to this week
			$start_time = Academic_time::NewToday();
			$start_time = $start_time->BackToMonday();
			$end_time = $start_time->Adjust('1week');
			$format = 'ac';
			//$range_description = 'from today for 1 week';
			$range_description = 'this week';
		}

		// Use the start time, end time, and format to set up views

		//$weeks_start = $start_time->Adjust('-2week')->BackToMonday();
		$weeks_start = $this->academic_calendar->AcademicDayOfTerm(
				$start_time->AcademicYear(),
				$start_time->AcademicTerm(),
				1,
				0,0,0
			);
		/*if ($weeks_start->Timestamp() < $monday->Timestamp()) {
			$weeks_start = $monday;
		}*/

		/*$weeks_end = $end_time->Adjust('5week')->BackToMonday();
		if ($weeks_end->Timestamp() < $monday->Timestamp()) {
			$weeks_end = $monday->Adjust('5week');
		}*/

		// Set up the week select view
		$week_select = new ViewCalendarSelectWeek();
		$week_select->SetUriBase('directory/'.$organisation.'/events/');
		$week_select->SetUriFormat($format);
		//$week_select->SetRange($weeks_start, $weeks_end);
		$week_select->SetAcademicTerm($weeks_start->AcademicYear(), $weeks_start->AcademicTerm());
		$week_select->SetSelectedWeek($start_time, $end_time);
		$week_select->Retrieve();

		$occurrence_filter = new EventOccurrenceFilter();
		$occurrence_filter->EnableSource('all');
		$occurrence_filter->SetSpecialCondition(
				'organisations.organisation_directory_entry_name = '.
				$this->db->escape($organisation)
			);

		// Set up the events list
		$events_list = new ViewCalendarList();
		$events_list->SetUriBase('directory/'.$organisation.'/events/');
		$events_list->SetUriFormat($format);
		$events_list->SetRange($start_time, $end_time);
		$events_list->SetOccurrenceFilter($occurrence_filter);
		$events_list->Retrieve();

		// Set up the directory events view to contain the week select and
		// events list
		$directory_events = new FramesFrame('directory/directory_view_events',$data);
		$directory_events->SetContent($week_select,'week_select');
		$directory_events->SetContent($events_list,'events_list');
		$directory_events->SetData('date_range_description', $range_description);

		// Set up the directory frame to use the messages frame
		$this->frame_public->SetPage('events');
		$this->frame_directory->SetOrganisation($data['organisation']);
		$this->frame_directory->SetContent($directory_events);

		// Set up the public frame to use the directory frame
		$this->frame_public->SetTitleParameters(
				array('organisation' => $data['organisation']['name']));
		$this->frame_public->SetExtraHead($extra_head);
		$this->frame_public->SetContent($this->frame_directory);

		// Load the public frame view
		$this->frame_public->Load();
	}

	/// Directory reviews page.
	function reviews($organisation)
	{
		$this->pages_model->SetPageCode('directory_reviews');
		$this->_SetupOrganisationFrame($organisation);
		
		$data = $this->organisations->_GetOrgData($organisation);
		
		$this->load->model('articles_model');
		$reviews = $this->articles_model->GetDirectoryOrganisationReviewsByEntryName($organisation);
		// sort into types
		$directory_reviews = array();
		$review_types = array();
		foreach ($reviews as $review) {
			if (NULL === $review['type']) {
				$directory_reviews[] = $review;
			} else {
				if (!array_key_exists($review['type'], $review_types)) {
					$review_types[$review['type']] = array();
				}
				$review_types[$review['type']][] = $review;
			}
		}
		$data['organisation']['reviews_untyped'] = $directory_reviews;
		$data['organisation']['reviews_by_type'] = $review_types;

		// Set up the directory view
		$directory_view = $this->frames->view('directory/directory_view_reviews', $data);

		// Set up the directory frame to use the directory events view
		$this->frame_public->SetPage('reviews');
		$this->frame_directory->SetOrganisation($data['organisation']);
		$this->frame_directory->SetContent($directory_view);

		// Set up the public frame to use the directory view
		$this->frame_public->SetTitleParameters(
				array('organisation' => $data['organisation']['name']));
		$this->frame_public->SetContent($this->frame_directory);

		// Load the public frame view
		$this->frame_public->Load();
	}

	/// Directory members page.
	function members($organisation)
	{
		$this->pages_model->SetPageCode('directory_members');
		$this->_SetupOrganisationFrame($organisation);
		
		// Normal organisation data
		$data = $this->organisations->_GetOrgData($organisation);
		
		// Members data
		$members = $this->directory_model->GetDirectoryOrganisationCardsByEntryName($organisation);
		// translate into nice names for view
		$data['organisation']['cards'] = array();
		foreach ($members as $member) {
			$data['organisation']['cards'][] = array(
				'name' => $member['business_card_name'],
				'title' => $member['business_card_title'],
				#'course' => $member['business_card_course'],
				'blurb' => $member['business_card_blurb'],
				'email' => $member['business_card_email'],
				'phone_mobile' => $member['business_card_mobile'],
				'phone_internal' => $member['business_card_phone_internal'],
				'phone_external' => $member['business_card_phone_external'],
				'postal_address' => $member['business_card_postal_address'],
				'colours' => array(
					'background' => $member['business_card_colour_background'],
					'foreground' => $member['business_card_colour_foreground'],
				),
				'type' => $member['business_card_type_name'],
			);
		}
		
		// Set up the directory view
		$directory_view = $this->frames->view('directory/directory_view_members', $data);

		// Set up the directory frame to use the directory events view
		$this->frame_public->SetPage('members');
		$this->frame_directory->SetOrganisation($data['organisation']);
		$this->frame_directory->SetContent($directory_view);

		// Set up the public frame to use the directory view
		$this->frame_public->SetTitleParameters(
				array('organisation' => $data['organisation']['name']));
		$this->frame_public->SetContent($this->frame_directory);

		// Load the public frame view
		$this->frame_public->Load();
	}
}
?>
