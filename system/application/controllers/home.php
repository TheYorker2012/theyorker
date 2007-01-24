<?php
/**
 * This controller is the default.
 * It currently displays only the prototype homepage, in the prototype student frame
 *
 * \author Nick Evans
 */
class Home extends Controller {

	/**
	 * @brief Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}
	
	/**
	* Displays prototype homepage, in the prototype student frame
	*/
	function index()
	{
		$data = array(
			'test' => 'I set this variable from the controller!',
		);
		
		// Set up the public frame
		$this->frame_public->SetTitle('List');
		$this->frame_public->SetContentSimple('general/list', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	function main()
	{
		$this->pages_model->SetPageCode('home_main');
		
		$data = array();
		
		$data['welcome_title'] = $this->pages_model->GetPropertyText('welcome_title');
		$data['welcome_text']  = $this->pages_model->GetPropertyWikitext('welcome_text');
		
		// Set up the public frame
		$this->frame_public->SetContentSimple('general/home', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

}
?>
