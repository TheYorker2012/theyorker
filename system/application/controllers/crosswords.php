<?php

/** Displays and manages crossword puzzles
 */
class Crosswords extends Controller
{
	function __construct()
	{
		parent::Controller();

		$this->load->helper('crosswords');
		$this->load->model('crosswords_model');
	}

	function _remap($arg = null)
	{
		if (null === $arg) {
			return $this->index();
		}
		$args = func_get_args();

		// First argument is one of:
		// "tips"
		if ('tips' === $arg) {
			array_shift($args);
			return call_user_func_array(array(&$this, 'tips'), $args);
		}
		// crossword id
		if (is_numeric($arg)) {
			return call_user_func_array(array(&$this, 'crossword_by_id'), $args);
		}
		// date
		if (false) {
			return call_user_func_array(array(&$this, 'crossword_by_date'), $args);
		}
		// category short name
		return call_user_func_array(array(&$this, 'category'), $args);
	}

	function index()
	{
		OutputModes('xhtml',FeedOutputModes());
		if (!CheckPermissions('public')) return;

		$this->main_frame->SetFeedTitle('The Yorker Crosswords');
		if (FeedOutputMode()) {
			$this->main_frame->Channel()->SetDescription('All crosswords.');
			$this->_fillCrosswordFeed();
		}
		else {
			$this->load->helper('input_date');
			$search = new InputInterfaces;
			$date_interface = new InputDateInterface('search_date', time());
			$date_interface->setTimeEnabled(false);
			$search->Add('Find by date', $date_interface);
			$num_errors = $search->Validate();
			if (0 == $num_errors && $search->Updated()) {
				$values = $search->UpdatedValues();
			}

			// Load categories
			$categories = $this->crosswords_model->GetAllCategories();
			foreach ($categories as &$category) {
				// And information about the latest few crosswords
				$category['latest']	= $this->crosswords_model->GetCrosswords(null,$category['id'], null,null,true,null, 3,'DESC');
				$category['next']	= $this->crosswords_model->GetCrosswords(null,$category['id'], null,true,null,null, 1,'ASC');
			}
			$data = array(
				'Categories' => &$categories,
				'Search' => &$search,
			);
			$this->main_frame->IncludeCss('stylesheets/crosswords_index.css');
			$this->main_frame->SetContentSimple('crosswords/index', $data);
			/// @todo Use pages interface
			$this->main_frame->SetTitle('Crosswords');
		}
		$this->main_frame->Load();
	}

	function category($cat = null, $arg2 = null)
	{
		$category = $this->crosswords_model->GetCategoryByShortName($cat);
		if (null === $category) {
			show_404();
		}

		OutputModes('xhtml',FeedOutputModes());
		if (!CheckPermissions('public')) return;

		$this->main_frame->SetFeedTitle('The Yorker '.$category['name']);
		if (FeedOutputMode()) {
			$this->main_frame->Channel()->SetDescription('All crosswords in category "'.$category['name'].'".');
			$this->_fillCrosswordFeed($category['id']);
		}
		elseif (null === $arg2) {
			$category['latest']	= $this->crosswords_model->GetCrosswords(null,$category['id'], null,null,true,null, 5,'DESC');
			$category['next']	= $this->crosswords_model->GetCrosswords(null,$category['id'], null,true,null,null, 1,'ASC');

			$data = array(
				'Category' => &$category,
				'Links' => array(
					'crosswords home' => site_url('crosswords'),
				),
			);
			$this->main_frame->IncludeCss('stylesheets/crosswords_index.css');
			$this->main_frame->SetContentSimple('crosswords/category', $data);
			/// @todo Use pages interface
			$this->main_frame->SetTitle($category['name']);
		}
		elseif ($arg2 == 'archive') {
			$category['latest']	= $this->crosswords_model->GetCrosswords(null,$category['id'], null,null,true,null, null,'DESC');
			$category['next']	= $this->crosswords_model->GetCrosswords(null,$category['id'], null,true,null,null, 1,'ASC');

			$data = array(
				'Category' => &$category,
				'Links' => array(
					'crosswords home' => site_url('crosswords'),
				),
			);
			$this->main_frame->IncludeCss('stylesheets/crosswords_index.css');
			$this->main_frame->SetContentSimple('crosswords/category', $data);
			/// @todo Use pages interface
			$this->main_frame->SetTitle($category['name'].' Archive');
		}
		else {
			show_404();
		}

		$this->main_frame->Load();
	}

	function crossword_by_id($id = null, $operation = null, $comment_include = null)
	{
		if ($operation == 'ajax') {
			OutputModes('ajax');
		}
		if (!CheckPermissions('public')) return;
		$loggedIn = $this->user_auth->isLoggedIn;

		if (null === $id || !is_numeric($id)) {
			show_404();
		}
		$id = (int)$id;

		$crossword = $this->crosswords_model->GetCrosswords($id,null, null,null,true,null);
		if (count($crossword) == 0) {
			show_404();
		}
		$crossword = $crossword[0];

		$puzzle = 0;
		$worked = $this->crosswords_model->LoadCrossword($crossword['id'], $puzzle);
		if (!$worked) {
			show_404();
		}

		// WARNING: SOLUTION NOT NECESSARILY CLEARED

		if (null === $operation || 'view' === $operation) {
			$puzzle->grid()->clearSolutions();
			$crosswordView = new CrosswordView($puzzle);
			$crosswordView->setClueTypes($crossword['has_quick_clues'], $crossword['has_cryptic_clues']);
			if (!$loggedIn) {
				$crosswordView->setReadOnly(!$crossword['expired']);
			}
			else {
				$success = $this->crosswords_model->LoadCrosswordVersion($crossword['id'], $this->user_auth->entityId, $puzzle);
			}

			// Comment thread
			$comments_thread = null;
			$comments_require_expiration = false;
			if ((!$comments_require_expiration || $crossword['expired']) && is_numeric($crossword['public_thread_id'])) {
				$this->load->library('comment_views');
				$this->comment_views->SetUri('/crosswords/'.$crossword['id'].'/view/');
				$comments_thread = $this->comment_views->CreateStandard((int)$crossword['public_thread_id'], $comment_include);
			}


			$data = array();
			$data['Crossword'] = &$crossword;
			$data['Winners'] = $this->crosswords_model->GetWinners($crossword['id']);
			$data['Grid'] = &$crosswordView;
			$data['LoggedIn'] = $loggedIn;
			$data['Paths'] = array(
				'ajax' => site_url('/crosswords/'.$crossword['id'].'/ajax'),
			);
			$data['Tips'] = new CrosswordTipsList(null, $crossword['id']);
			$data['Comments'] = $comments_thread;
			$data['Links'] = array(
				'more crosswords in the "'.$crossword['category_name'].'" category' => site_url('crosswords/'.$crossword['category_short_name']),
				'crosswords home' => site_url('crosswords'),
			);
			$data['ShareUrl'] = $_SERVER['SERVER_NAME'].'/crosswords/'.$crossword['id'];

			$this->main_frame->SetContentSimple('crosswords/crossword', $data);
			/// @todo Use pages interface
			$pub = new Academic_time($crossword['publication']);
			$this->main_frame->SetTitle('Crossword '.$pub->Format('D ').$pub->AcademicTermNameUnique().' week '.$pub->AcademicWeek());
			$this->main_frame->AddDescription('Online crossword');
			$this->main_frame->SetMainImage(site_url('crosswords/'.$crossword['id'].'/preview'));
		}
		elseif ($operation == 'ajax') {
			$op2 = $comment_include;
			$root = array(
				'_tag' => 'crossword',
			);

			if ($op2 == 'winners') {
				$root['expired'] = ($crossword['expired'] ? "yes" : "no" );
				$root['positions'] = $crossword['winners'];
				$winners = $this->crosswords_model->GetWinners($crossword['id']);
				$root['winners'] = array();
				foreach ($winners as $position => &$winner) {
					$root['winners'][] = array(
						'_tag' => 'winner',
						'_attr' => array(
							'position' => $position,
						),
						'name' => $winner['firstname'].' '.$winner['surname'],
					);
				}
			}
			elseif ($op2 == 'solution') {
				$root['solution'] = array(
					'_attr' => array(
						'available' => ($crossword['expired']?"yes":"no"),
					),
				);
				if ($crossword['expired']) {
					$root['solution']['grid'] = array();
					$grid = &$puzzle->grid();
					$height = $grid->height();
					$width = $grid->width();
					for ($y = 0; $y < $height; ++$y) {
						for ($x = 0; $x < $width; ++$x) {
							$state = $grid->cellState($x, $y);
							if (is_string($state)) {
								$root['solution']['grid'][] = array(
									'_tag' => 'letter',
									'_attr' => array(
										'x' => $x,
										'y' => $y,
									),
									$state,
								);
							}
						}
					}
				}
			}
			elseif (!$loggedIn) {
				$this->main_frame->Error(array(
					'class' => 'error',
					'text' => 'Not logged in.',
				));
				$root['status'] = 'fail';
			}
			else {
				$puzzle->grid()->clearSolutions();
				$worked = $puzzle->importGrid($_POST['xw']);
				if ($worked) {
					// Saving (and autosaving)
					if (isset($_POST['xw']['save']) || isset($_POST['xw']['autosave'])) {
						$success = $this->crosswords_model->SaveCrosswordVersion($crossword['id'], $this->user_auth->entityId, $puzzle);
						$root['status'] = $success ? 'success' : 'fail';
						if (!$success) {
							$this->main_frame->Error(array(
								'class' => 'error',
								'text' => 'Unable to save crossword.',
							));
						}
					}
					// Submitting for marking`
					elseif (isset($_POST['xw']['submit'])) {
						$root['status'] = 'success';
						$correct = $puzzle->isCorrect();
						$root['mark'] = ($correct ? 'correct' : 'incorrect');
						$winner = false;
						if ($correct) {
							$winner = $this->crosswords_model->AddWinner($crossword['id'], $this->user_auth->entityId);
						}
						$root['winner'] = ($winner ? 'yes' : 'no');
					}
					else {
						$this->main_frame->Error(array(
							'class' => 'error',
							'text' => 'Unable to edit crossword.',
						));
						$root['status'] = 'fail';
					}
				}
				else {
					$this->main_frame->Error(array(
						'class' => 'error',
						'text' => 'Invalid crossword data.',
					));
					$root['status'] = 'fail';
				}
			}

			$this->main_frame->SetXml($root);
		}
		elseif ($operation == 'preview')
		{
			$this->crosswords_model->GetCrosswordThumbnail($crossword['id']);
			return;
		}
		else {
			show_404();
		}

		$this->main_frame->Load();
	}

	function crossword_by_date($date = null)
	{
		if (!CheckPermissions('public')) return;
		$this->main_frame->Load();
	}

	function tips($category = null)
	{
		OutputModes('xhtml',FeedOutputModes());
		if (!CheckPermissions('public')) return;

		if (null === $category) {
			$data = array(
				'Categories' => $this->crosswords_model->GetTipCategories(null, true),
				'SelfUri' => $this->uri->uri_string(),
			);
			$this->main_frame->SetFeedTitle('The Yorker Crossword Tips');
			if (FeedOutputMode()) {
				$this->main_frame->Channel()->SetDescription('All crossword tips.');
				$this->_fillTipsFeed();
			}
			else {
				$this->main_frame->setContentSimple('crosswords/tips', $data);
				/// @todo Use pages interface
				$this->main_frame->SetTitle('Crossword Tips');
			}
		}
		else {
			$category_info = null;
			if (is_numeric($category)) {
				$category_info = $this->crosswords_model->GetTipCategories((int)$category, true);
				if (empty($category_info)) {
					$category_info = null;
				}
				else {
					$category_info = $category_info[0];
				}
			}

			if (null === $category_info) {
				show_404();
			}

			$this->main_frame->SetFeedTitle('The Yorker Crossword Tips - '.$category_info['name']);
			if (FeedOutputMode()) {
				$this->main_frame->Channel()->SetDescription('All crossword tips in category "'.$category_info['name'].'".');
				$this->_fillTipsFeed($category_info['id']);
			}
			else {
				$data = array(
					'Category' => $category_info,
					'Tips' => new CrosswordTipsList($category_info['id'], null),
					'PostAction' => $this->uri->uri_string(),
				);

				$this->main_frame->setContentSimple('crosswords/tip_cat_view', $data);
				/// @todo Use pages interface
				$this->main_frame->SetTitle('Crossword Tips - '.$category_info['name']);
			}
		}
		$this->main_frame->Load();
	}

	private function _fillCrosswordFeed($category_id = null, $limit = 20)
	{
		$this->load->library('academic_calendar');

		$channel = &$this->main_frame->Channel();
		$channel->SetImage('http://'.$_SERVER['HTTP_HOST'].'/images/crosswords/xw.png', 64, 64);
		$channel->SetEditor('crosswords@theyorker.co.uk (crosswords editor)');
		$channel->SetWebmaster('webmaster@theyorker.co.uk (webmaster)');

		$crosswords = $this->crosswords_model->GetCrosswords(null,$category_id, null,null,true,null, $limit,'DESC');
		$url = 'http://'.$_SERVER['HTTP_HOST'];
		foreach ($crosswords as &$crossword) {
			$item = &$channel->NewItem();

			$item->SetPublicationDate($crossword['publication']);

			$pub = new Academic_time($crossword['publication']);
			$pub_str = $pub->Format('D ').$pub->AcademicTermNameUnique().' week '.$pub->AcademicWeek();
			$title = '('.$crossword['category_name'].') '.$pub_str;
			$item->SetTitle($title);

			$item->AddCategory($crossword['category_name']);

			$item->SetLink($url.'/crosswords/'.(int)$crossword['id']);

			foreach ($crossword['authors'] as $author) {
				$item->AddAuthor($author['fullname'], 'no-reply@theyorker.co.uk');
			}
		}
	}

	private function _fillTipsFeed($category_id = null, $limit = 20)
	{
		$this->load->library('academic_calendar');

		$channel = &$this->main_frame->Channel();
		$channel->SetImage('http://'.$_SERVER['HTTP_HOST'].'/images/crosswords/xw.png', 64, 64);
		$channel->SetEditor('crosswords@theyorker.co.uk (crosswords editor)');
		$channel->SetWebmaster('webmaster@theyorker.co.uk (webmaster)');

		$tips = $this->crosswords_model->GetTips($category_id, null, null, true);
		$url = 'http://'.$_SERVER['HTTP_HOST'];
		foreach ($tips as &$tip) {
			$item = &$channel->NewItem();

			$item->SetPublicationDate($tip['publication']);

			$pub = new Academic_time($tip['publication']);
			$pub_str = $pub->Format('D ').$pub->AcademicTermNameUnique().' week '.$pub->AcademicWeek();
			$title = '('.$tip['category_name'].') '.$pub_str;
			$item->SetTitle($title);

			$item->AddCategory($tip['category_name']);

			$item->SetLink($url.'/crosswords/'.(int)$tip['crossword_id'].'#tip'.(int)$tip['id']);

			$item->SetDescription($tip['content_xhtml']);
		}
	}
}

?>
