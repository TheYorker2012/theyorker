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
		$this->load->model('Article_Model');
		$this->load->model('Home_Hack_Model');
		$this->load->model('polls_model');
		$this->load->library('Homepage_boxes');
		$this->load->library('Polls_view');
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
		// lets be explicit just in case:
		$sources->EnableGroup('owned');
		$sources->EnableGroup('subscribed');
		$sources->EnableGroup('private');
		$sources->EnableGroup('active');
		$sources->DisableGroup('inactive');
		
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

		$TodoView = NULL;
// 		$TodoView = new CalendarViewTodoList();
// 		$TodoView->SetCalendarData($calendar_data);
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
		
		//poll handling
		$poll_id = $this->polls_model->GetDisplayedPoll();
		$user_voted = $this->polls_model->HasUserVoted($poll_id, $this->user_auth->entityId);
		$poll_show_results = false;
		if ($poll_id && !$user_voted)
		{
			if (isset($_POST['submit_vote'])) {
				if ($this->input->post('poll_vote'))
				{
					if ($this->polls_model->IsChoicePartOfPoll($poll_id, $this->input->post('poll_vote')))
					{
						$this->polls_model->SetUserPollVote($poll_id, $this->user_auth->entityId, $this->input->post('poll_vote'));
						$this->messages->AddMessage('success', 'Your vote has been cast.');
						$user_voted = true;
					}
					else
					{
						$this->messages->AddMessage('error', 'Invalid option.');
					}
				}
			}
			elseif (isset($_POST['submit_results'])) {
				$poll_show_results = true;
			}
		} else {
			$poll_show_results = true;
		}
		
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
//			'features' => array(),
			'arts' => array(),
//			'videocasts' => array(),
			'lifestyle' => array(),
//			'blogs' => array()
		);

		// Get the article ids of all articles to be displayed
		$article_all_ids = $this->Home_Hack_Model->getLatestArticleIds(
			array(
				'uninews' => 4,
				'sport' => 4,
//				'features' => 1,
				'arts' => 4,
//				'videocasts' => 1,
				'lifestyle' => 4,
//				'blogs' => 1,
			)
		);

		// Create an array to map an article id to an article type
		$article_base_types = array();
		foreach($article_all_ids as $type => $ids) {
			foreach($ids as $id)
				$article_base_types[$id] = $type;
		}

		// Get the ids of articles which require titles
		$article_title_ids = array();
		foreach($article_all_ids as $type => $ids) {
			foreach($ids as $id) {
				$article_title_ids[] = $id;
			}
		}

		// Get the article titles
		$article_titles = $this->Home_Hack_Model->getArticleTitles($article_title_ids, '%W, %D %M %Y');
		foreach($article_titles as $title) {
			$type = $article_base_types[$title['id']];
			$title['photo_xhtml'] = $this->image->getThumb($title['photo_id'], 'small', false, array('class' => 'left'));
			$data['articles'][$type][] = $title;
		}

		// Get latest comments made on articles
		$this->load->library('comment_views');
		$data['latest_comments'] = $this->comment_views->GetLatestComments();

		//Obtain weather
		//$data['weather_forecast'] = $this->Home_Model->GetWeather();

		//Obtain banner
		//$data['banner'] = $this->Home_Model->GetBannerImageForHomepage();
		
		// Minifeeds
		list($data['events'], $data['todo']) = $this->_GetMiniCalendars();

		$this->load->helper('crosswords_miniview');
		$data['crosswords'] = new CrosswordsMiniView(3);
		
		// Poll data
		if ($poll_id)
		{
			$data['poll_vote_box'] = new PollsVoteBox(
				$this->polls_model->GetPollDetails($poll_id),
				$this->polls_model->GetPollChoiceVotes($poll_id),
				$user_voted,
				$poll_show_results
			);
		}
		else
		{
			$data['poll_vote_box'] = null;
		}

		$this->load->model('flickr_model');
		$data['photos'] = $this->flickr_model->getLatestPhotos(9);

		$this->main_frame->SetData('menu_tab', 'home');
		$this->main_frame->SetContentSimple('general/home', $data);
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->Load();
	}

}
?>
