<?php

/// Office Reviews
/**
 * @author Nick Evans (nse500@cs.york.ac.uk)
 * @author Frank Burton (frb501@cs.york.ac.uk)
 *
 * The URI is mapped using config/routes.php
 *
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
	private function _SetupNavbar($DirectoryEntry, $ContextType)
	{
		$this->load->library('frame_directory');

		$navbar = $this->frame_public->GetNavbar();
		$navbar->AddItem('comments', 'Comments',
				'/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/comments');
		$navbar->AddItem('reviews', 'Reviews',
				'/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/review');
		$navbar->AddItem('photos', 'Photos',
				'/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/photos');
		$navbar->AddItem('tags', 'Tags',
				'/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/tags');
		$navbar->AddItem('information', 'Information',
				'/office/reviews/'.$DirectoryEntry.'/'.$ContextType.'/information');
	}

	// this is blank 
	function index()
	{
	}

	/// Reviews Overview Page
	function overview($organisation)
	{
		$this->pages_model->SetPageCode('office_reviews_overview');

		// Check the user has logged into the office
		if (CheckPermissions('office')) {

			// Insert main text from pages information (sample)
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
	
			// Set up the view
			$the_view = $this->frames->view('reviews/office_review_overview', $data);

			// Set up the public frame
			$this->frame_public->SetTitleParameters(
					array('organisation' => $data['organisation']['name'],
						  'content_type' => $ContextType));
			$this->main_frame->SetContent($the_view);}

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	// Reviews information page
	function information($ContextType, $organisation)
	{
		$this->pages_model->SetPageCode('office_reviews_information');

		// Check the user has logged into the office
		if (CheckPermissions('office')) {

			//Get navigation bar and tell it the current page
			$data = $this->organisations->_GetOrgData($organisation);
			$this->_SetupNavbar($organisation,$ContextType);
			$this->main_frame->SetPage('information');

			// Insert main text from pages information (sample)
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
	
			// Set up the view
			$the_view = $this->frames->view('reviews/office_review_information', $data);

			// Set up the public frame
			$this->frame_public->SetTitleParameters(
					array('organisation' => $data['organisation']['name'],
						  'content_type' => $ContextType));
			$this->main_frame->SetContent($the_view);}

		// Load the public frame view
		$this->main_frame->Load();
	}

	function tags($ContextType, $organisation)
	{
		$this->pages_model->SetPageCode('office_reviews_tags');

		// Check the user has logged into the office
		if (CheckPermissions('office')) {

			//Get navigation bar and tell it the current page
			$data = $this->organisations->_GetOrgData($organisation);
			$this->_SetupNavbar($organisation,$ContextType);
			$this->main_frame->SetPage('tags');
			
			// Insert main text from pages information (sample)
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
	
			// Set up the view
			$the_view = $this->frames->view('reviews/office_review_tags', $data);

			// Set up the public frame
			$this->frame_public->SetTitleParameters(
					array('organisation' => $data['organisation']['name'],
						  'content_type' => $ContextType));
			$this->main_frame->SetContent($the_view);}

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function photos($ContextType, $organisation)
	{
		$this->pages_model->SetPageCode('office_review_photos');

		// Check the user has logged into the office
		if (CheckPermissions('office')) {

			//Get navigation bar and tell it the current page
			$data = $this->organisations->_GetOrgData($organisation);
			$this->_SetupNavbar($organisation,$ContextType);
			$this->main_frame->SetPage('photos');
	
			// Insert main text from pages information (sample)
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
	
			// Set up the view
			$the_view = $this->frames->view('directory/viparea_directory_photos', $data);
	
			// Set up the public frame
			$this->frame_public->SetTitleParameters(
					array('organisation' => $data['organisation']['name'],
						  'content_type' => $ContextType));
			$this->main_frame->SetContent($the_view);}
		
		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function review($ContextType, $organisation)
	{
		$this->pages_model->SetPageCode('office_review_reviews');

		// Check the user has logged into the office
		if (CheckPermissions('office')) {

			//Get navigation bar and tell it the current page
			$data = $this->organisations->_GetOrgData($organisation);
			$this->_SetupNavbar($organisation,$ContextType);
			$this->main_frame->SetPage('reviews');
	
			// Insert main text from pages information (sample)
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
	
			// Set up the view
			$the_view = $this->frames->view('reviews/office_review_reviews', $data);
		
			// Set up the public frame
			$this->frame_public->SetTitleParameters(
					array('organisation' => $data['organisation']['name'],
						  'content_type' => $ContextType));
			$this->main_frame->SetContent($the_view);}
		
		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function reviewedit($ContextType, $organisation, $ArticleId)
	{
		$this->pages_model->SetPageCode('office_review_reviewedit');

		// Check the user has logged into the office
		if (CheckPermissions('office')) {
			//Get navigation bar and tell it the current page
			$data = $this->organisations->_GetOrgData($organisation);
			$this->_SetupNavbar($organisation,$ContextType);
			$this->main_frame->SetPage('reviews');
	
			// Insert main text from pages information (sample)
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
	
			// Set up the view
			$the_view = $this->frames->view('reviews/office_review_reviewedit', $data);

			// Set up the public frame
			$this->frame_public->SetTitleParameters(
					array('organisation' => $data['organisation']['name'],
						  'content_type' => $ContextType));
			$this->main_frame->SetContent($the_view);}

		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function comments($ContextType, $organisation)
	{
		$this->pages_model->SetPageCode('office_review_comments');

		// Check the user has logged into the office
		if (CheckPermissions('office')) {
			//Get navigation bar and tell it the current page
			$data = $this->organisations->_GetOrgData($organisation);
			$this->_SetupNavbar($organisation,$ContextType);
			$this->main_frame->SetPage('comments');
	
			// Insert main text from pages information (sample)
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

			// Set up the view
			$the_view = $this->frames->view('reviews/office_review_comments', $data);

			// Set up the public frame
			$this->frame_public->SetTitleParameters(
					array('organisation' => $data['organisation']['name'],
						  'content_type' => $ContextType));
			$this->main_frame->SetContent($the_view);}
		
		// Load the public frame view
		$this->main_frame->Load();
	}
}
?>
