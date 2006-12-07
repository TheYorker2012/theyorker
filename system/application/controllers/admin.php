<?php

/**
 * @brief Yorker directory.
 * @author Owen Jones (oj502@york.ac.uk)
 * 
 * The URI /admin maps to this controller this will be an admin menu when it is made
 *
 * Each admin page will be a function of this controler, so the pages will be /admin/pagename
 */
class Admin extends Controller {

	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}

	/**
	 * @brief Admin menu index page.
	 */
	function index()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('Admin');
		$this->frame_public->SetContentSimple('admin/admin');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	/**
	 * @admin page to edit the directory.
	 */
	function yorkerdirectory()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('Directory Admin');
		$this->frame_public->SetContentSimple('directory/admin_directory');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	function yorkerdirectoryview()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('Directory Admin');
		$this->frame_public->SetContentSimple('directory/admin_directory_view');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
	
	/**
	* @admin pages for news system
	*/
	function news($Segment3 = '', $Segment4 = '')
	{
		switch ($Segment3) {
			case 'request':
				switch ($Segment4) {
					case 'view':
						$admin_view_name = 'news/admin_request_view';
						break;
					default:
						$admin_view_name = 'news/admin_request_new';
						break;
				}
				break;
			default:
				$admin_view_name = 'news/admin_news';
				break;
		}
		// Set up the public frame
		$this->frame_public->SetTitle('News Admin');
		$this->frame_public->SetContentSimple($admin_view_name);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	/**
	 * @admin page to add entry to faq.
	 */
	function addfaq()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('FAQ Admin');
		$this->frame_public->SetContentSimple('faq/addfaq');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	/**
	 * @admin page to add entry to howdoi
	 */
	function addhowdoi()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('How Do I? Admin');
		$this->frame_public->SetContentSimple('faq/addhowdoi');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	/**
	 * @admin page to edit entry in faq
	 */
	function editfaq()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('FAQ Admin');
		$this->frame_public->SetContentSimple('faq/editfaq');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}
?>
