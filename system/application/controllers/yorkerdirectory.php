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

		$this->load->model('directory_model');

		$this->load->helper('text');
	}

	/// Set up the directory frame
	/**
	 * @param $OrganisationData Organisation data array.
	 * @pre @a $OrganisationData is valid organisation array.
	 * @post Frame_directory frame is loaded and ready to use.
	 */
	private function _SetupOrganisationFrame($OrganisationData)
	{
		$this->load->library('frame_directory');

		$navbar = $this->frame_directory->GetNavbar();
		$navbar->AddItem('about', 'About',
				'/images/prototype/news/uk.png',
				'/directory/'.$OrganisationData['shortname']);
		$navbar->AddItem('events', 'Events',
				'/images/prototype/news/feature.gif',
				'/directory/'.$OrganisationData['shortname'].'/events');
		$navbar->AddItem('members', 'Members',
				'/images/prototype/news/feature.gif',
				'/directory/'.$OrganisationData['shortname'].'/members');
		$navbar->AddItem('reviews', 'Reviews',
				'/images/prototype/news/feature.gif',
				'/directory/'.$OrganisationData['shortname'].'/reviews');
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
		
		$maintext = $this->pages_model->GetProperty('maintext', 'text');
		if (FALSE === $maintext) {
			$data['maintext'] = '';
		} else {
			$data['maintext'] = $maintext->GetText();
		}

		// Get the search pattern from POST (optional)
		$search_pattern = $this->input->post('search_directory', TRUE);
		// Get the organisations matching the search and pass the search pattern
		// to the view as well
		$data['organisations'] = $this->_GetOrgs($search_pattern);
		$data['search'] = $search_pattern;
		
		// Get organisation types
		$data['organisation_types'] = $this->_GetOrganisationTypes($data['organisations']);

		//Libary for AtoZ system
		$this->load->library('character_lib'); //This character libary is used by the view, so load it here

		// Set up the directory view
		$directory_view = $this->frames->view('directory/directory', $data);

		// Set up the public frame to use the directory view
		$this->frame_public->SetTitleParameters();
		$this->frame_public->SetContent($directory_view);

		// Load the public frame view
		$this->frame_public->Load();
	}

	/// Directory organisation page.
	function view($organisation)
	{
		$this->pages_model->SetPageCode('directory_view');
		
		$data = $this->_GetOrgData($organisation);
		$this->_SetupOrganisationFrame($data['organisation']);

		$subpageview='directory/directory_view';

		// Set up the directory view
		$directory_view = $this->frames->view($subpageview, $data);

		// Set up the directory frame to use the directory events view
		$this->frame_directory->SetPage('about');
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
		
		$data = $this->_GetOrgData($organisation);
		$this->_SetupOrganisationFrame($data['organisation']);

		$this->load->library('frame_messages');
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
				$this->frame_messages->AddMessage(
						'Unrecognised date range: "'.$DateRange.'"'
					);
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

		// Set up the events list
		$events_list = new ViewCalendarList();
		$events_list->SetUriBase('directory/'.$organisation.'/events/');
		$events_list->SetUriFormat($format);
		$events_list->SetRange($start_time, $end_time);
		$events_list->Retrieve();

		// Set up the directory events view to contain the week select and
		// events list
		$directory_events = new FramesFrame('directory/directory_view_events',$data);
		$directory_events->SetContent($week_select,'week_select');
		$directory_events->SetContent($events_list,'events_list');
		$directory_events->SetData('date_range_description', $range_description);

		// Set up the messages frame to use the directory events view
		$this->frame_messages->SetContent($directory_events);

		// Set up the directory frame to use the messages frame
		$this->frame_directory->SetPage('events');
		$this->frame_directory->SetOrganisation($data['organisation']);
		$this->frame_directory->SetContent($this->frame_messages);

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
		
		$data = $this->_GetOrgData($organisation);
		$this->_SetupOrganisationFrame($data['organisation']);

		$subpageview='directory/directory_view_reviews';

		// Set up the directory view
		$directory_view = $this->frames->view($subpageview, $data);

		// Set up the directory frame to use the directory events view
		$this->frame_directory->SetPage('reviews');
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
		
		$data = $this->_GetOrgData($organisation);
		$this->_SetupOrganisationFrame($data['organisation']);

		$subpageview='directory/directory_view_members';

		// Set up the directory view
		$directory_view = $this->frames->view($subpageview, $data);

		// Set up the directory frame to use the directory events view
		$this->frame_directory->SetPage('members');
		$this->frame_directory->SetOrganisation($data['organisation']);
		$this->frame_directory->SetContent($directory_view);

		// Set up the public frame to use the directory view
		$this->frame_public->SetTitleParameters(
				array('organisation' => $data['organisation']['name']));
		$this->frame_public->SetContent($this->frame_directory);

		// Load the public frame view
		$this->frame_public->Load();
	}

	/// Get organisation types from organisations.
	/**
	 * @param $Organisations array Organisations as returned by _GetOrgs.
	 * @return array of organisation types.
	 */
	private function _GetOrganisationTypes($Organisations)
	{
		$types = array();
		foreach ($Organisations as $organisation) {
			$types[$organisation['type']] = TRUE;
		}
		$result = array();
		foreach ($types as $type => $enabled) {
			$result[] = array(
				'id' => $type,
				'name' => $type,
			);
		}
		return $result;
	}

	/// Temporary function get organisations.
	/**
	 * @param $Pattern string/bool Search pattern or FALSE if all.
	 * @return array of organisations matching pattern.
	 */
	private function _GetOrgs($Pattern)
	{
		$orgs = $this->directory_model->GetDirectoryOrganisations();
		$organisations = array();
		foreach ($orgs as $org) {
			$organisations[] = array(
				'name' => $org['organisation_name'],
				'shortname' => $org['organisation_directory_entry_name'],
				'description' => $org['organisation_description'],
				'shortdescription' => word_limiter($org['organisation_description'],30),
				'type' => $org['organisation_type_name'],
			);
		}
		if ($Pattern !== FALSE) {
			$organisations = array(
				array(
					'shortname'   => 'pole_dancing',
					'name'        => 'Pole Dancing',
					'description' => 'A fitness club',
					'type'        => 'Athletics Union',
				),
				array(
					'shortname'   => 'costcutter',
					'name'        => 'Costcutter',
					'description' => 'Campus shop',
					'type'        => 'College & Campus',
				),
			);
		}
		return $organisations;
	}

	/// Temporary function get organisation data.
	/**
	 * @param $OrganisationShortName Short name of organisation.
	 * @return Organisation data relating to specified organisation or FALSE.
	 */
	private function _GetOrgData($OrganisationShortName)
	{
		$data = array();

		$orgs = $this->directory_model->GetDirectoryOrganisationByEntryName($OrganisationShortName);
		if (1 === count($orgs)) {
			foreach ($orgs as $org) {
				$data['organisation'] = array(
					'name'        => $org['organisation_name'],
					'shortname'   => $org['organisation_directory_entry_name'],
					'description' => $org['organisation_description'],
					'type'        => $org['organisation_type_name'],
					'website'     => $org['organisation_url'],
					'location'    => $org['organisation_location'],
					'open_times'  => $org['organisation_opening_hours'],


					'blurb'       => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nulla lorem magna, tincidunt sed, feugiat nec, consectetuer vitae, nisl. Vestibulum gravida ipsum non justo. Vivamus sem. Quisque ut sem vitae elit luctus lobortis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
					'type'        => 'Organisation',
					'cards'       => array(
						array(
							'name' => 'Daniel Ashby',
							'title' => 'Editor',
							'course' => 'Politics and Philosophy',
							'blurb' => 'The guy in charge',
							'email' => 'editor@theyorker.co.uk',
							'phone_mobile' => '07777 777777',
							'phone_internal' => '01904 444444',
							'phone_external' => '01904 555555',
							'postal_address' => '',
						),
						array(
							'name' => 'Nick Evans',
							'title' => 'Technical Director',
							'course' => 'Computer Science',
							'blurb' => 'The other guy',
							'email' => 'webmaster@theyorker.co.uk',
							'phone_internal' => '07788 888888',
							'phone_external' => '01904 333333',
							'postal_address' => '01904 666666',
						),
					),
					'reviews'     => array(
						array(
							'author' => 'Ian Benest',
							'publish_date' => '4/12/2006',
							'description' => 'I didn\'t like this. It sucked ass. Yo suck ass. Said the Farmer. The farmer doesn\'t like dan. He doesn\'t know dan. Dan doesn\'t know the farmer. Barry Scott sells cillit bang.',
						),
						array(
							'author' => 'Detlef Plump',
							'publish_date' => '5/12/2006',
							'description' => 'Another review here. Here be another review. It be here really. Yarr, tharr be reviews here. Another review here. Here be another review. It be here really. Yarr, tharr be reviews here. Another review here. Here be another review. It be here really. Yarr, tharr be reviews here. Another review here. Here be another review. It be here really. Yarr, tharr be reviews here.',
						),
					),
				);
			}
		} else {
			$data['organisation'] = array(
				'shortname'   => $OrganisationShortName,
				'name'        => 'FragSoc',
				'description' => 'The people who run this website',
				'blurb'       => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nulla lorem magna, tincidunt sed, feugiat nec, consectetuer vitae, nisl. Vestibulum gravida ipsum non justo. Vivamus sem. Quisque ut sem vitae elit luctus lobortis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
				'website'     => 'http://www.fragsoc.com',
				'location'    => 'Goodricke College',
				'open_times'  => 'Every Other Weekend',
				'type'        => 'Organisation',
				'cards'       => array(
					array(
						'name' => 'Daniel Ashby',
						'title' => 'Editor',
						'course' => 'Politics and Philosophy',
						'blurb' => 'The guy in charge',
						'email' => 'editor@theyorker.co.uk',
						'phone_mobile' => '07777 777777',
						'phone_internal' => '01904 444444',
						'phone_external' => '01904 555555',
						'postal_address' => '',
					),
					array(
						'name' => 'Nick Evans',
						'title' => 'Technical Director',
						'course' => 'Computer Science',
						'blurb' => 'The other guy',
						'email' => 'webmaster@theyorker.co.uk',
						'phone_internal' => '07788 888888',
						'phone_external' => '01904 333333',
						'postal_address' => '01904 666666',
					),
				),
				'reviews'     => array(
					array(
						'author' => 'Ian Benest',
						'publish_date' => '4/12/2006',
						'description' => 'I didn\'t like this. It sucked ass. Yo suck ass. Said the Farmer. The farmer doesn\'t like dan. He doesn\'t know dan. Dan doesn\'t know the farmer. Barry Scott sells cillit bang.',
					),
					array(
						'author' => 'Detlef Plump',
						'publish_date' => '5/12/2006',
						'description' => 'Another review here. Here be another review. It be here really. Yarr, tharr be reviews here. Another review here. Here be another review. It be here really. Yarr, tharr be reviews here. Another review here. Here be another review. It be here really. Yarr, tharr be reviews here. Another review here. Here be another review. It be here really. Yarr, tharr be reviews here.',
					),
				),
			);
		}
		return $data;
	}
}
?>
