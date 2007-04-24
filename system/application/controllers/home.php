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
	
	/**
	 * @return array(Todays events view, Todo view).
	 */
	private function _GetMiniCalendars()
	{
		$this->load->library('academic_calendar');
		$this->load->library('calendar_backend');
		$this->load->library('calendar_source_my_calendar');
		
		$this->load->library('calendar_frontend');
		$this->load->library('calendar_view_agenda');
		$this->load->library('calendar_view_todo_list');
		
		$now = new Academic_time(time());
		$start = $now;
		$end = $now->Midnight()->Adjust('+2day');
		
		$sources = new CalendarSourceMyCalendar();
		$sources->EnableGroup('todo');
		$sources->SetRange($start->Timestamp(), $end->Timestamp());
		
		$calendar_data = new CalendarData();
		
		$this->messages->AddMessages($calendar_data->FetchEventsFromSources($sources));
		
		// Display data
		$this->load->library('calendar_view_days');
		
		$EventsView = new CalendarViewAgenda();
		$EventsView->SetMiniMode();
		$EventsView->SetCalendarData($calendar_data);
		//$EventsView->SetStartEnd($start->Timestamp(), $end->Timestamp());
		
		$TodoView = new CalendarViewTodoList();
		$TodoView->SetCalendarData($calendar_data);
		return array($EventsView, $TodoView);
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

		//Obtain quote
		$data['quote'] = $this->Home_Model->GetQuote();

		//Obtain banner
		$data['banner'] = $this->Home_Model->GetBannerImage();
		
		// Minifeeds
		list($data['events'], $data['todo']) = $this->_GetMiniCalendars();

		// Set up the public frame
		$this->main_frame->SetContentSimple('general/home', $data);

		$this->main_frame->SetExtraCss('/stylesheets/home.css');

		$this->main_frame->SetExtraHead('<script src="/javascript/prototype.js" type="text/javascript"></script><script src="/javascript/scriptaculous.js" type="text/javascript"></script>');

		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

}
?>
