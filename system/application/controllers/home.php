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

$this->main_frame->SetData('menu_tab', 'home');

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
			'features' => array(),
			'arts' => array(),
			'videocasts' => array(),
			'lifestyle' => array(),
			'blogs' => array()
		);

		// Get the article ids of all articles to be displayed
		$article_all_ids = $this->Home_Hack_Model->getLatestArticleIds(
			array(
				'uninews' => 3,
				'sport' => 3,
				'features' => 1,
				'arts' => 1,
				'videocasts' => 1,
				'lifestyle' => 1,
				'blogs' => 1,
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
		if (count($article_all_ids['features']) > 0)
			$article_summary_ids[] = $article_all_ids['features'][0];
		if (count($article_all_ids['arts']) > 0)
			$article_summary_ids[] = $article_all_ids['arts'][0];
		if (count($article_all_ids['videocasts']) > 0)
			$article_summary_ids[] = $article_all_ids['videocasts'][0];

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
		$this->load->library('comment_views');
		$data['latest_comments'] = $this->comment_views->GetLatestComments();

		//Obtain Links
		if ($this->user_auth->isLoggedIn) {
			$data['link'] = $this->Links_Model->GetUserLinks($this->user_auth->entityId);
		} else {
			$data['link'] = $this->Links_Model->GetUserLinks(0);
		}

		//Obtain weather
		//$data['weather_forecast'] = $this->Home_Model->GetWeather();
		$data['weather_forecast'] = array();

		//Obtain quote
		$data['quote'] = $this->Home_Model->GetQuote();

		//Obtain banner
		$data['banner'] = $this->Home_Model->GetBannerImageForHomepage();
		
		//Obtain specials
		//list here the specials to get, along with their title
		$specials = array(
			array('lifestyle','Latest Lifestyle'),
			array('blogs','Latest Blog'),
			);
		//foreach type given setup the data, assumes [0] is has a small image and heading
		foreach ($specials as $special) {
			$data['special'][$special[0]]['title'] = $special[1];
			if (isset($data['articles'][$special[0]][0])) {
				$data['special'][$special[0]]['show'] = true;
				$data['special'][$special[0]]['data'] = $data['articles'][$special[0]][0];
			}
			else {
				$data['special'][$special[0]]['show'] = false;
			}
		}
		/* this is the old method, getting articles set using specials
		$specials_types = $this->Article_Model->getMainArticleTypes();
		foreach ($specials_types as $special){
			$special_id = $this->News_model->GetLatestFeaturedId($special['codename']);
			$data['special'][$special['codename']]['title'] = $special['name'];
			if(!empty($special_id)) {
				$data['special'][$special['codename']]['show'] = true;
				$data['special'][$special['codename']]['data'] = $this->News_model->GetSummaryArticle($special_id);
			}
			else {
				$data['special'][$special['codename']]['show'] = false;
			}
		}*/

		// Minifeeds
		list($data['events'], $data['todo']) = $this->_GetMiniCalendars();
		
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

		// Set up the public frame
		$this->main_frame->SetContentSimple('general/home', $data);

		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->IncludeJs('javascript/prototype.js');
		$this->main_frame->IncludeJs('javascript/scriptaculous.js?load=effects,dragdrop');

		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

}
?>
