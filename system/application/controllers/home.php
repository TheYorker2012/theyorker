<?php
/**
 * This controller is the default.
 * It should now diplay a work in progress homepage
 *
 * \author Nick Evans
 * \author Alex Fargus	
 */
class Home extends Controller {

	/**
	 * @brief Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();
		$this->load->model('News_model');
		$this->load->model('Weather_Model');
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
		
		//Various arrays defined
		$data = array();		//Stores all data to be passed to view
		$res = array();

		$data['welcome_title'] = $this->pages_model->GetPropertyText('welcome_title');
		$data['welcome_text']  = $this->pages_model->GetPropertyWikitext('welcome_text');
		
		//Obtain news articles to be displayed
		$article_ids = $this->News_model->GetLatestId('uninews',3);
		$data['primary_article'] = $this->News_model->GetSummaryArticle($article_ids[0],'%W, %D %M %Y','medium');
		$data['secondary_article'] = $this->News_model->GetSimpleArticle($article_ids[1]);
		$data['tertiary_article'] = $this->News_model->GetSimpleArticle($article_ids[2]);

		//Obtain weather
		$data['weather_forecast'] = $this->Weather_Model->GetWeather();

		// Set up the public frame
		$this->main_frame->SetContentSimple('general/home', $data);

		$this->main_frame->SetExtraCss('/stylesheets/home.css');
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

}
?>
