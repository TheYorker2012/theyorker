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
		$this->load->model('Links_Model');
		$this->load->model('Home_Hack_Model');
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
		$this->load->library('calendar_view_upcoming');
		$this->load->library('calendar_view_todo_list');

		$now = new Academic_time(time());
		$start = $now;
		$end = $now->Midnight()->Adjust('+2day');

		$sources = new CalendarSourceMyCalendar();
		$sources->EnableGroup('todo');
		$sources->SetRange($start->Timestamp(), $end->Timestamp());
		$sources->SetTodoRange(time(), time());

		$calendar_data = new CalendarData();

		$this->messages->AddMessages($calendar_data->FetchEventsFromSources($sources));

		// Display data
		$this->load->library('calendar_view_days');

		$EventsView = new CalendarViewUpcoming();
		$EventsView->SetMiniMode();
		$EventsView->SetCalendarData($calendar_data);
		//$EventsView->SetStartEnd($start->Timestamp(), $end->Timestamp());

		$TodoView = new CalendarViewTodoList();
		$TodoView->SetCalendarData($calendar_data);
		return array($EventsView, $TodoView);
	}
	
	function _FacebookHome()
	{
		$this->pages_model->SetPageCode('home_facebook');
		
		$this->main_frame->SetContentSimple('facebook/home');
		$this->main_frame->Load();
	}
	
	function facebook()
	{
		OutputModes(array('xhtml','fbml'));
		if (!CheckPermissions('public')) return;
		
		return $this->_FacebookHome();
	}

	function index()
	{
		OutputModes(array('xhtml','fbml'));
		if (!CheckPermissions('public')) return;
		
		if ('fbml' === OutputMode()) {
			return $this->_FacebookHome();
		}

		$this->pages_model->SetPageCode('home_main');
		$this->load->library('image');

		//Various arrays defined
		$data = array();		//Stores all data to be passed to view
		$res = array();

		$data['welcome_title'] = $this->pages_model->GetPropertyText('welcome_title');
		$data['welcome_text']  = $this->pages_model->GetPropertyWikitext('welcome_text');

		$data['articles'] = array(
			'uninews' => array(),
			'sport' => array(),
			'features' => array(),
			'arts' => array()
		);

		// Get the article ids of all articles to be displayed
		$article_all_ids = $this->Home_Hack_Model->getLatestArticleIds(
			array(
				'uninews' => 3,
				'sport' => 3,
				'features' => 2,
				'arts' => 2
			)
		);
		//$this->messages->AddDumpMessage('ids',$article_all_ids);

		// Create an array to map an article id to an article type
		$article_base_types = array();
		foreach($article_all_ids as $type => $ids) {
			foreach($ids as $id)
				$article_base_types[$id] = $type;
		}

		// Get the ids of articles which require summaries
		$article_summary_ids = array();
		if (count($article_all_ids['uninews']) > 0)
			$article_summary_ids[] = $article_all_ids['uninews'][0];
		if (count($article_all_ids['sport']) > 0)
			$article_summary_ids[] = $article_all_ids['sport'][0];

		// Get the article summaries, create html for image tags
		$article_summaries = $this->Home_Hack_Model->getArticleSummaries($article_summary_ids, '%W, %D %M %Y');
		foreach($article_summaries as $summary) {
			$type = $article_base_types[$summary['id']];
			$summary['photo_xhtml'] = $this->image->getThumb($summary['photo_id'], 'medium', false, array('class' => 'left'));
			$data['articles'][$type][] = $summary;
		}

		// Get the ids of articles which require titles
		$article_title_ids = array();
		foreach($article_all_ids as $type => $ids) {
			foreach($ids as $id) {
				if (!in_array($id, $article_summary_ids))
					$article_title_ids[] = $id;
			}
		}

		// Get the article titles
		$article_titles = $this->Home_Hack_Model->getArticleTitles($article_title_ids);
		foreach($article_titles as $title) {
			$type = $article_base_types[$title['id']];
			$title['photo_xhtml'] = $this->image->getThumb($title['photo_id'], 'small', false, array('class' => 'left'));
			$data['articles'][$type][] = $title;
		}

		// Get latest comments made on articles
		$data['latest_comments'] = $this->Home_Hack_Model->getLatestComments();

		//Obtain Links
		if ($this->user_auth->isLoggedIn) {
			$data['link'] = $this->Links_Model->GetUserLinks($this->user_auth->entityId);
		} else {
			$data['link'] = $this->Links_Model->GetUserLinks(0);
		}

		//Obtain weather
		$data['weather_forecast'] = $this->Home_Model->GetWeather();

		//Obtain quote
		$data['quote'] = $this->Home_Model->GetQuote();

		//Obtain banner
		$data['banner'] = $this->Home_Model->GetBannerImageForHomepage();

		// Minifeeds
		list($data['events'], $data['todo']) = $this->_GetMiniCalendars();

		// Set up the public frame
		$this->main_frame->SetContentSimple('general/home', $data);

		$this->main_frame->SetExtraCss('/stylesheets/home.css');

		$this->main_frame->SetExtraHead('<script src="/javascript/prototype.js" type="text/javascript"></script><script src="/javascript/scriptaculous.js?load=effects,dragdrop" type="text/javascript"></script>');

		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

}
?>
