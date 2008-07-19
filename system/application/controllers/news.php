<?php

/**
 * This is the controller for the news section.
 *
 * @author Chris Travis	(cdt502 - ctravis@gmail.com)
 */

class News extends Controller {

	/**
	 * @brief Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();
		// Load news model
		$this->load->model('News_model');
	}

	/// This is the main entry point.
	/**
	 * This gets provided with all url segments after /news
	 */
	function _remap($method = 'uninews')
	{
		$args = func_get_args();
		# If there are articles in a category by this name, use it.
		if (count($this->News_model->getArticleTypeInformation($method)) > 0) {
			call_user_func_array(array(&$this, '_article'), $args);
		}
		# Redirect short URL to full URL
		elseif (is_numeric($method)) {
			call_user_func_array(array(&$this, '_redirect'), $args);
		}
		# If theres a function for the given method that isn't protected, use it.
		elseif (method_exists($this, $method) && substr($method,0,1) != '_') {
			call_user_func_array(array(&$this, $method), array_slice($args, 1));
		}
		# Otherwise not found
		else {
			show_404();
		}
	}

	function _redirect ($article_id = NULL)
	{
		if ($article_id !== NULL) {
			$type = $this->News_model->getArticleType($article_id);
			if ($type !== false) {
				redirect('/news/' . $type->content_type_codename . '/' . $article_id);
			}
		}
		redirect('/news');
	}

	/// Display a news article in a given section.
	function _article($article_type = 'uninews', $article_id = NULL, $CommentInclude = 0)
	{
		// Load public view
		if (!CheckPermissions('public')) return;

		$type_info = $this->News_model->getArticleTypeInformation($article_type);
		if (count($type_info) == 0) {
			$article_type = 'uninews';
			$type_info = $this->News_model->getArticleTypeInformation($article_type);
		}
		
		// The precise article wasn't given so we should show the default.
		// Redirect to the correct URL so that google doesn't index section pages.
		// Get a minimum of information so the redirect is fast.
		if ($article_id === NULL) {
			list($content_codename, $article_id) = $this->News_model->GetDefaultArticleInfo($article_type);
			if (is_numeric($article_id)) {
				redirect('news/'.$content_codename.'/'.$article_id);
			}
		}
		// Get the latest article ids from the model.
		$latest_article_ids = $this->News_model->GetLatestId($article_type,8);
		
		if ($type_info['parent_id'] != NULL) {
			$parent = $this->News_model->getArticleTypeCodename($type_info['parent_id']);
			$this->pages_model->SetPageCode('news_' . $parent['content_type_codename']);
			$this->main_frame->SetTitleParameters(array('section' => ' - ' . $type_info['name']));
		} else {
			$this->pages_model->SetPageCode('news_' . $article_type);
			if ($type_info['has_children']) {
				$this->main_frame->SetTitleParameters(array('section' => ''));
			}
		}

		// Get page specific attributes
		if ($article_type == 'uninews') {
			$data['rss_feed_title'] = $this->pages_model->GetPropertyText('rss_feed_title');
		}

		// Get variable content based on article type
		$data['article_type'] = $article_type;
		$data['puffer_heading'] = $this->pages_model->GetPropertyText('puffer_heading');
		$data['latest_heading'] = $this->pages_model->GetPropertyText('latest_heading');
		$data['other_heading'] = $this->pages_model->GetPropertyText('other_heading');
		$data['related_heading'] = $this->pages_model->GetPropertyText('related_heading');
		$data['links_heading'] = $this->pages_model->GetPropertyText('links_heading');

		// $latest_article_ids has already been found above
		if (($type_info['has_children']) || ($type_info['parent_id'] != NULL)) {
			$this->load->library('image');
			if ($type_info['section'] == 'blogs') {
				if ($type_info['parent_id'] != NULL) {
					$temp_type = $parent['content_type_codename'];
				}
				if ($type_info['has_children']) {
					$temp_type = $article_type;
				}
				$data['blogs'] = $this->News_model->getSubArticleTypes($temp_type);
				foreach ($data['blogs'] as &$blog) {
					$blog['image'] = '/image/'.$blog['image_codename'].'/'.$blog['image'];
				}
			} else {
				$temp_type = $article_type;
				if ($type_info['parent_id'] != NULL) {
					$temp_type = $parent['content_type_codename'];
				}
				$data['puffers'] = $this->News_model->getSubArticleTypes($temp_type);
				foreach ($data['puffers'] as &$puffer) {
					$puffer['image'] = '/image/'.$puffer['image_codename'].'/'.$puffer['image'];
				}
			}
		}

		/// Get requested article id if submitted
		$url_article_id = $article_id;
		// Check if an article id was requested, if so check that the type of article it corresponds
		// to is correct for the current news view, otherwise 404 (so that search engines do not index duplicate pages).
		if ($url_article_id !== NULL) {
			if (is_numeric($url_article_id) && $this->News_model->IdIsOfType($url_article_id,$article_type)) {
				/// Check if requested article is already one of the IDs returned
				$found_article = array_search($url_article_id, $latest_article_ids);
				if ($found_article !== FALSE) {
					/// If it is, remove it from the list
					unset($latest_article_ids[$found_article]);
				}
				/// Put request article id onto front of array so that it becomes the main article
				$latest_article_ids = array_merge(array($url_article_id),$latest_article_ids);
			} else {
				return show_404();
			}
		}

		/// Get all of the latest article
		if (isset($_SESSION['office_news_preview'])) {
			$main_article = $this->News_model->GetFullArticle($latest_article_ids[0],'','%W, %D %M %Y', $_SESSION['office_news_preview']);
			$data['office_preview'] = 1;
			unset($_SESSION['office_news_preview']);
		} else {
			/// If there are no articles for this particular section then show a page anyway
			if (count($latest_article_ids) == 0) {
				$main_article = array(
					'placeholder'			=>	true,
					'id'					=>	0,
					'date'					=>	date('l, jS F Y'),
					'location'				=>	0,
					'public_thread_id'		=>	NULL,
					'heading'				=>	$this->pages_model->GetPropertyText('news:no_articles_heading',TRUE),
					'subheading'			=>	NULL,
					'subtext'				=>	NULL,
					'text'					=>	$this->pages_model->GetPropertyWikitext('news:no_articles_text',TRUE),
					'blurb'					=>	NULL,
					'authors'				=>	array(),
					'links'					=>	array(),
					'related_articles'		=>	array(),
					'fact_boxes'			=>	array(),
				);
			} else {
				$main_article = $this->News_model->GetFullArticle($latest_article_ids[0]);
				/// Check if article requested doesn't exist
				if ($main_article === NULL) {
					redirect('/news/'.$article_type);
				}
			}
		}

		if ($main_article['poll_id'] !== NULL) {
			$this->load->model('polls_model');
			$poll_info = $this->polls_model->GetPollDetails($main_article['poll_id']);
			$poll_options = $this->polls_model->GetPollChoices($main_article['poll_id']);
			$user_info = $this->polls_model->GetCompetitionContactDetails($this->user_auth->entityId);
			if ((!$poll_info['deleted']) && (mktime() > $poll_info['start_time'])) {
				$poll_message = '';
				if (!$this->user_auth->isLoggedIn) {
					$poll_message = 'Please <a href="/login/main/news/' . $article_type . '/' . $article_id . '">login</a> to enter this competition.';
				} elseif (!$this->user_auth->isUser) {
					$poll_message = 'Sorry, organisations may not enter competitions. Please login as an individual to enter.';
				} elseif ($this->user_auth->officeLogin) {
					$poll_message = 'Sorry, members of The Yorker may not enter competitions.';
				} elseif ($this->polls_model->HasUserVoted($main_article['poll_id'], $this->user_auth->entityId)) {
					$poll_message = 'Thank you for entering this competition.';
				} elseif (mktime() > $poll_info['finish_time']) {
					$poll_message = 'Sorry, this competition is now closed.';
				} elseif (isset($_POST['comp_answer'])) {
					if (($user_info['user_firstname'] == '') || ($user_info['user_surname'] == '')) {
						$this->messages->AddMessage('error', 'Please make sure you enter your name before entering this competition.');
					} elseif ($this->polls_model->IsChoicePartOfPoll($main_article['poll_id'], $_POST['comp_answer'])) {
						$this->polls_model->SetUserPollVote($main_article['poll_id'], $this->user_auth->entityId, $_POST['comp_answer']);
						$this->messages->AddMessage('success', 'You have successfully been entered into the competition.');
					}
					redirect('/news/' . $article_type . '/' . $article_id);
				}
				$main_article['article_poll'] = array(
					'info'		=>	$poll_info,
					'options'	=>	$poll_options,
					'message'	=>	$poll_message,
					'user'		=>	$user_info
				);
				$this->load->library('wikiparser');
				$main_article['article_poll']['info']['question'] = $this->wikiparser->parse($main_article['article_poll']['info']['question']);
			}
		}

		//Set page title to include headline
		$this->main_frame->SetTitleParameters(array('headline' => $main_article['heading']));

		/// Get some of the 2nd- and 3rd-latest articles
		$news_previews = array();
		for ($index = 1; $index <= 2 && $index < count($latest_article_ids); $index++) {
			array_push($news_previews, $this->News_model->GetSummaryArticle($latest_article_ids[$index], "Right"));
		}

		/// Get less of the next 3 newest articles
		$news_others = array();
		for ($index = 3; $index < count($latest_article_ids); $index++) {
			array_push($news_others, $this->News_model->GetSimpleArticle($latest_article_ids[$index], "Left"));
		}

		/// Get comments for article
		if (is_numeric($main_article['public_thread_id'])) {
			$this->load->library('comment_views');
			if (FALSE === $CommentInclude) {
				$CommentInclude = NULL;
			}
			$this->comment_views->SetUri('/news/'.$article_type.'/'.$latest_article_ids[0].'/');
			$data['comments'] = $this->comment_views->CreateStandard((int)$main_article['public_thread_id'], $CommentInclude);
		}

		/// Gather all the data into an array to be passed to the view
		$data['main_article'] = $main_article;
		$data['news_previews'] = $news_previews;
		$data['news_others'] = $news_others;
		/// Facebook share link info
		$this->main_frame->SetData('description', $main_article['blurb']);
		$this->main_frame->SetData('medium_type', 'news');
		if (isset($main_article['primary_photo_link']))
			$this->main_frame->SetData('main_image', $main_article['primary_photo_link']);

		// Set up the public frame
		$this->main_frame->SetContentSimple('news/news', $data);

		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	/// News archive section
	function archive()
	{
		if (!CheckPermissions('public')) return;

		// Check for search filters to be applied in POST
		$post_url = array();
		if (is_numeric($this->input->post('archive_reporter'))) {
			$post_url[] = 'reporter';
			$post_url[] = $this->input->post('archive_reporter');
		}
		if (is_numeric($this->input->post('archive_section'))) {
			$post_url[] = 'section';
			$post_url[] = $this->input->post('archive_section');
		}
		if (count($post_url) > 0)
			redirect('/news/archive/' . implode('/', $post_url));

		/// Check for search filters in URL
		$filters = $this->uri->uri_to_assoc();
		if (isset($filters['reporter'])) {
			if (!is_numeric($filters['reporter'])) {
				$this->main_frame->AddMessage('error', 'Unknown reporter, please try again.');
				redirect('/news/archive/');
			} else {
				$this->load->model('businesscards_model');
				$data['byline_info'] = $this->businesscards_model->GetPublicBylineInfo($filters['reporter']);
				if (count($data['byline_info']) == 0) {
					// No byline returned, byline must not be approved so don't show
					unset($data['byline_info']);
				} else {
					/// Process byline image
					$this->load->library('image');
					if ($data['byline_info']['business_card_image_id'] === NULL) {
						$data['byline_info']['business_card_image_href'] = '';
					} else {
						$data['byline_info']['business_card_image_href'] = $this->image->getPhotoURL($data['byline_info']['business_card_image_id'], 'userimage');
					}
				}
			}
		}

		$data['offset'] = 0;
		$base_url = array();
		// Convert filters to format required by news model
		$archive_filters = array();
		foreach ($filters as $field => $value) {
			if ($field == 'page') {
				$data['offset'] = (!is_numeric($value)) ? 0 : $value;
			} else {
				$base_url[] = $field;
				$base_url[] = $value;
				$archive_filters[] = array($field, $value);
			}
		}

		// Get data for search criteria options
		$this->load->model('requests_model');
		$this->load->model('businesscards_model');
		$data['sections'] = $this->requests_model->getBoxes();
		$data['reporters'] = $this->businesscards_model->GetBylines();
		$data['filters'] = &$filters;

		/// Pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url() . 'news/archive/' . implode('/', $base_url) . '/page';
		$config['total_rows'] = $this->News_model->GetArchive('count', $archive_filters)->count;
		$config['per_page'] = 10;
		$config['num_links'] = 2;
		$config['full_tag_open'] = '<div class="Pagination">';
		$config['full_tag_close'] = '</div>';
		$config['first_tag_open'] = '<span>';
		$config['first_tag_close'] = '</span>';
		$config['last_tag_open'] = '<span>';
		$config['last_tag_close'] = '</span>';
		$config['next_tag_open'] = '<span>';
		$config['next_tag_close'] = '</span>';
		$config['prev_tag_open'] = '<span>';
		$config['prev_tag_close'] = '</span>';
		$config['cur_tag_open'] = '<span class="selected">';
		$config['cur_tag_close'] = '</span>';
		$config['num_tag_open'] = '<span>';
		$config['num_tag_close'] = '</span>';
		if ($data['offset'] > 0)
			$config['uri_segment'] = $this->uri->total_segments();
		$this->pagination->initialize($config);

		/// Get all past articles
		$data['articles'] = $this->News_model->GetArchive('search', $archive_filters, $data['offset'], $config['per_page']);
		/// Get article thumbnails
		$this->load->library('image');
		$this->load->model('slideshow');
		foreach ($data['articles'] as &$article) {
			if(!empty($article['organisation_codename'])){
				//The article is a review, so the archive will not have found a photo. Use the first image from its slideshow
				$photo_id = $this->slideshow->getFirstPhotoIDFromSlideShow($article['organisation_codename']);
			}else{
				$photo_id = $article['photo_id'];
			}
			$article['photo_xhtml'] = $this->image->getThumb($photo_id, 'small', false, array('class' => 'Left'));
		}
		$data['total'] = $config['total_rows'];

		/// Set up the public frame
		$this->main_frame->SetTitle('Archive');
		$this->main_frame->SetContentSimple('news/archive', $data);

		/// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	function rss()
	{
		/// Redirect to new feeds controller
		redirect('/feeds/news');
	}
}
?>
