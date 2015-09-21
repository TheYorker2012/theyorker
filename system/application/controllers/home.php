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
		//$this->load->model('Home_Model');
		$this->load->model('Links_Model');
		$this->load->model('Article_Model');
		$this->load->model('Home_Hack_Model');
		$this->load->model('polls_model');
		$this->load->library('Homepage_boxes');
		$this->load->library('Polls_view');
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

	function index()
	{
		OutputModes(array('xhtml','fbml'));
		if (!CheckPermissions('public')) return;

		if ('fbml' === OutputMode()) {
			return $this->_FacebookHome();
		}

		$this->load->model('home_hack_model2');
		$this->load->model('flickr_model');
		$this->load->model('crosswords_model');
		$this->load->model('comments_model');
		$this->load->model('advert_model');

		$spotlight = $this->home_hack_model2->getArticlesByTags(array('front-page'), 1);
		$this->home_hack_model2->ignore($spotlight);
		$uninews = $this->home_hack_model2->getArticlesByTags(array('news'), 3);
		$sport = $this->home_hack_model2->getArticlesByTags(array('sport'), 4);
		$arts = $this->home_hack_model2->getArticlesByTags(array('arts'), 6);
		$comment = $this->home_hack_model2->getArticlesByTags(array('comment'), 4);
		$lifestyle = $this->home_hack_model2->getArticlesByTags(array('lifestyle'), 4);
		$photos = $this->flickr_model->getLatestPhotos(9);

		$this->load->library('adverts');
		//$advert = $this->advert_model->SelectLatestAdvert2();
		
		

		$boxes = array();

		$boxes[] = array(
			'type'			=>	'spotlight',
			'articles'		=>	$spotlight
		);
		$boxes[] = array(
			'type'			=>	'article_rollover',
			'title'			=>	'latest news',
			'title_link'	=>	'/news',
			'articles'		=>	$uninews
		);
		$boxes[] = array(
			'type'			=>	'adsense_third',
			'last'			=>	true,
			'advert'		=>  $advert			
		);
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest sport',
			'title_link'	=>	'/sport',
			'size'			=>	'1/3',
			'last'			=>	false,
			'articles'		=>	$sport
		);

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest comment',
			'title_link'	=>	'/comment',
			'size'			=>	'1/3',
			'last'			=>	false,
			'articles'		=>	$comment
		);

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest lifestyle',
			'title_link'	=>	'/lifestyle',
			'size'			=>	'1/3',
			'last'			=>	true,
			'articles'		=>	$lifestyle
		);
		
		$comments_config = $this->config->item('comments');
		$boxes[] = array(
			'type'			=>	'comments_latest',
			'title'			=>	'latest comments',
			'title_link'	=>	'',
			'size'			=>	'1/2',
			'last'			=>	true,
			'comments'		=>	$this->comments_model->GetLatestComments(10),
			'comments_per_page' => $comments_config['max_per_page']
		);

		/*
		 $boxes[] = array(
			'type'			=>	'crossword_latest',
			'title'			=>	'latest crosswords',
			'title_link'	=>	'/crosswords',
			'size'			=>	'1/2',
			'last'			=>	false,
			'next'			=>	$this->crosswords_model->GetCrosswords(null,null,null,true,null,null,1,'ASC'),
			'latest'		=>	$this->crosswords_model->GetCrosswords(null,null,null,null,true,null,2,'DESC')
		);
		*/

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest arts',
			'title_link'	=>	'/arts',
			'size'			=>	'1/2',
			'last'			=>	false,
			'articles'		=>	$arts
		);
		
		$boxes[] = array(
			'type'			=>	'photo_bar',
			'size'			=>	'full',
			'last'			=>	true,
			'photos'		=>	$photos
		);
		
		$boxes[] = array(
			'type'			=>	'adsense_half',
			'last'			=>	false
		);

		$boxes[] = array(
			'type'			=>	'advert_half',
			'last'			=>	true
		);

		$data = array(
			'boxes'	=>	$boxes
		);
		$this->pages_model->SetPageCode('home_main');
		$this->main_frame->SetData('menu_tab', 'home');
		$this->main_frame->SetContentSimple('flexibox/layout', $data);
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->Load();


		//Obtain weather
		//$data['weather_forecast'] = $this->Home_Model->GetWeather();

		// Minifeeds
		//list($data['events'], $data['todo']) = $this->_GetMiniCalendars();

		// Poll data
		/*
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
		*/

		//poll handling
		/*
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
		*/
	}

}
?>
