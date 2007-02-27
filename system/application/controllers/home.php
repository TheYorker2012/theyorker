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
	}
	
	/**
	* Displays prototype homepage, in the prototype student frame
	*/
	function pagelist()
	{
		if (!CheckPermissions('public')) return;
		
		$data = array(
			'test' => 'I set this variable from the controller!',
		);
		
		// Set up the public frame
		$this->main_frame->SetTitle('List');
		$this->main_frame->SetContentSimple('general/list', $data);
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	function index()
	{
		if (!CheckPermissions('public')) return;
		
		$this->pages_model->SetPageCode('home_main');
		
		$data = array();
		
		$data['welcome_title'] = $this->pages_model->GetPropertyText('welcome_title');
		$data['welcome_text']  = $this->pages_model->GetPropertyWikitext('welcome_text');
		
		// Set up the public frame
		$this->main_frame->SetContentSimple('general/home', $data);

		$this->main_frame->SetExtraCss('/stylesheets/home.css');
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

}
?>
