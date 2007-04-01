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
		$this->load->model('Home_Model');
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
		$this->main_frame->SetContentSimple('general/list', $data);
		$this->main_frame->SetTitle('List');
		
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
		$data['primary_article'] = $this->News_model->GetSummaryArticle($article_ids[0],"Left",'%W, %D %M %Y','medium',true);
		$data['secondary_article'] = $this->News_model->GetSummaryArticle($article_ids[1],"Left");
		$data['tertiary_article'] = $this->News_model->GetSummaryArticle($article_ids[2],"Left");

		//Obtain weather
		$data['weather_forecast'] = $this->Home_Model->GetWeather();
		
		//Obtain banner
		$data['banner'] = $this->Home_Model->GetBannerImage();

		// Set up the public frame
		$this->main_frame->SetContentSimple('general/home', $data);

		$this->main_frame->SetExtraCss('/stylesheets/home.css');
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

}
?>
