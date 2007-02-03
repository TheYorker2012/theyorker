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
class Reviews extends Controller
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
	private function _SetupOrganisationFrame($DirectoryEntry, $ContextType)
	{
		$this->load->library('frame_directory');

		$navbar = $this->frame_public->GetNavbar();
		$navbar->AddItem('comments', 'Comments',
				'/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/comments');
		$navbar->AddItem('review', 'Reviews',
				'/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/review');
		$navbar->AddItem('photos', 'Photos',
				'/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/photos');
		$navbar->AddItem('information', 'Information',
				'/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/information');
	}

	/// Directory index page.
	// this is blank lol
	function index()
	{
	}

	/// Directory organisation page.
	function information($ContextType, $organisation)
	{
		$this->pages_model->SetPageCode('office_reviews_information');

		//Get Data And toolbar
		$data = $this->organisations->_GetOrgData($organisation);
		$this->_SetupOrganisationFrame($organisation,$ContextType);

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		// Set up the directory view
		$the_view = $this->frames->view('reviews/office_review_information', $data);

		// Load the main frame
		if (SetupMainFrame('office')) {
			// Set up the public frame
			$this->frame_public->SetTitleParameters(
					array('organisation' => $data['organisation']['name'],
						  'content_type' => $ContextType));
			$this->frame_public->SetContent($the_view);
		}

		// Load the public frame view
		$this->frame_public->Load();
	}
	function photos($ContextType, $organisation)
	{
		$this->pages_model->SetPageCode('office_review_photos');

		//Get Data And toolbar
		$data = $this->organisations->_GetOrgData($organisation);
		$this->_SetupOrganisationFrame($organisation,$ContextType);

		// Set up the directory view
		$the_view = $this->frames->view('directory/viparea_directory_photos', $data);

		// Set up the public frame
		$this->frame_public->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
					  'content_type' => $ContextType));
		$this->frame_public->SetContent($the_view);

		// Load the public frame view
		$this->frame_public->Load();
	}
	function review($ContextType, $organisation)
	{
		$this->pages_model->SetPageCode('office_review_reviews');

		//Get Data And toolbar
		$data = $this->organisations->_GetOrgData($organisation);
		$this->_SetupOrganisationFrame($organisation,$ContextType);

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
		$data['map_text'] = $this->pages_model->GetPropertyWikitext('map_text');

		// Set up the directory view
		$the_view = $this->frames->view('reviews/office_review_reviews', $data);

		// Set up the public frame
		$this->frame_public->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
					  'content_type' => $ContextType));
		$this->frame_public->SetContent($the_view);

		// Load the public frame view
		$this->frame_public->Load();
	}
	function comments($ContextType, $organisation)
	{
		$this->pages_model->SetPageCode('office_review_comments');

		//Get Data And toolbar
		$data = $this->organisations->_GetOrgData($organisation);
		$this->_SetupOrganisationFrame($organisation,$ContextType);

		// Set up the review view
		$the_view = $this->frames->view('reviews/office_review_comments', $data);

		// Set up the public frame
		$this->frame_public->SetTitleParameters(
				array('organisation' => $data['organisation']['name'],
					  'content_type' => $ContextType));
		$this->frame_public->SetContent($the_view);

		// Load the public frame view
		$this->frame_public->Load();
	}
}
?>
