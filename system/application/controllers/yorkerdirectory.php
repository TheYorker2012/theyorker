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
class Yorkerdirectory extends Controller {
	
	/// Default constructor.
	function __construct()
	{
		parent::Controller();
		
		// Make use of the public frame
		$this->load->library('frame_public');
	}
	
	/// Directory index page.
	function index()
	{
		$data = array(
			'organisations' => array(
				array(
					'shortname'   => 'fragsoc',
					'name'        => 'FragSoc',
					'description' => 'A computer gaming society',
					'type'        => 'Society',
				),
				array(
					'shortname'   => 'theyorker',
					'name'        => 'The Yorker',
					'description' => 'The people who run this website',
					'type'        => 'Organisation',
				),
				array(
					'shortname'   => 'toffs',
					'name'        => 'Toffs',
					'description' => 'A nightclub in york',
					'type'        => 'Venue',
				),
				array(
					'shortname'   => 'poledancing',
					'name'        => 'Pole Dancing',
					'description' => 'A fitness club',
					'type'        => 'Athletics Union',
				),
				array(
					'shortname'   => 'cookiesoc',
					'name'        => 'Cookie Soc',
					'description' => 'Eat cookies',
					'type'        => 'Society',
				),
				array(
					'shortname'   => 'costcutter',
					'name'        => 'Costcutter',
					'description' => 'Campus shop',
					'type'        => 'College & Campus',
				),
			),
		);
		
		// Set up the directory view
		$directory_view = $this->frames->view('directory/directory', $data);
		
		// Set up the public frame to use the directory view
		$this->frame_public->SetTitle('Directory');
		$this->frame_public->SetContent($directory_view);
		
		// Load the public frame view
		$this->frame_public->Load();
	}
	
	/// Directory organisation page.
	function view($organisation)
	{
		$data = $this->_GetOrgData($organisation);
		$subpageview='directory/directory_view';
		
		// Set up the directory view
		$directory_view = $this->frames->view($subpageview, $data);
		
		// Set up the public frame to use the directory view
		$this->frame_public->SetTitle('Directory');
		$this->frame_public->SetContent($directory_view);
		
		// Load the public frame view
		$this->frame_public->Load();
	}
	
	/// Directory events page.
	function events($organisation)
	{
		$this->load->library('view_listings_select_week');
		$this->load->library('view_listings_list');
		$this->load->library('frame_directory');
	
		// Sorry about the clutter, this will be moved in a bit but it isn't
		// practical to put it in the view
		$extra_head = <<<EXTRAHEAD
			<script src="/javascript/prototype.js" type="text/javascript"></script>
			<script src="/javascript/scriptaculous.js" type="text/javascript"></script>
			<script src="/javascript/listings.js" type="text/javascript"></script>
			<link href="/stylesheets/listings.css" rel="stylesheet" type="text/css" />
EXTRAHEAD;
	
		$data = $this->_GetOrgData($organisation);
		
		$monday = new Academic_time(time());
		$monday = $monday->BackToMonday();
		
		$selected_week = $monday;//->Adjust('1week');
		
		// Set up the week select view
		$week_select = new ViewListingsSelectWeek();
		$week_select->SetRange($monday, 10);
		$week_select->SetSelectedWeek($selected_week);
		$week_select->Retrieve();
		
		// Set up the events list
		$events_list = new ViewListingsList();
		$events_list->SetRange($selected_week, 7);
		$events_list->Retrieve();
		
		// Set up the directory events view to contain the week select and
		// events list
		$directory_events = new FramesFrame('directory/directory_view_events',$data);
		$directory_events->SetContent($week_select,'week_select');
		$directory_events->SetContent($events_list,'events_list');
		
		// Set up the directory frame to use the directory events view
		$this->frame_directory->SetPage('events');
		$this->frame_directory->SetOrganisation($data['organisation']);
		$this->frame_directory->SetContent($directory_events);
		
		// Set up the public frame to use the directory frame
		$this->frame_public->SetTitle('Directory Events');
		$this->frame_public->SetExtraHead($extra_head);
		$this->frame_public->SetContent($this->frame_directory);
		
		// Load the public frame view
		$this->frame_public->Load();
	}
	
	/// Directory reviews page.
	function reviews($organisation)
	{
		$data = $this->_GetOrgData($organisation);
		$subpageview='directory/directory_view_reviews';
		
		// Set up the directory view
		$directory_view = $this->frames->view($subpageview, $data);
		
		// Set up the public frame to use the directory view
		$this->frame_public->SetTitle('Directory');
		$this->frame_public->SetContent($directory_view);
		
		// Load the public frame view
		$this->frame_public->Load();
	}
	
	/// Directory members page.
	function members($organisation)
	{
		$data = $this->_GetOrgData($organisation);
		$subpageview='directory/directory_view_members';
		
		// Set up the directory view
		$directory_view = $this->frames->view($subpageview, $data);
		
		// Set up the public frame to use the directory view
		$this->frame_public->SetTitle('Directory');
		$this->frame_public->SetContent($directory_view);
		
		// Load the public frame view
		$this->frame_public->Load();
	}
	
	/// Temporary function get organisation data.
	private function _GetOrgData($organisation)
	{
		$data = array(
			'organisation' => array(
				'shortname'   => 'theyorker',
				'name'        => 'The Yorker',
				'description' => 'The people who run this website',
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
			),
		);
		return $data;
	}
}
?>
