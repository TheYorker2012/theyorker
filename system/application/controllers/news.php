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
		# If theres a function for the given method that isn't protected, use it.
		elseif (method_exists($this, $method) && substr($method,0,1) != '_') {
			call_user_func_array(array(&$this, $method), array_slice($args, 1));
		}
		# Otherwise not found
		else {
			show_404();
		}
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
		
		/// Get the latest article ids from the model.
		// jh559 19th Jan 08: Moved this to happen as soon as possible
		// this is so if a redirect needs to take place less time is wasted.
		$latest_article_ids = $this->News_model->GetLatestId($article_type,8);
		if ($article_id === NULL && isset($latest_article_ids[0]) && is_numeric($latest_article_ids[0])) {
			// Redirect to the first article so that google doesn't index the blank url.
			/// @todo Handle blogs redirection properly
			if ($article_type != 'blogs') {
				redirect('news/'.$article_type.'/'.$latest_article_ids[0]);
			}
		}
		
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
					'fact_boxes'			=>	array()
				);
			} else {
		    	$main_article = $this->News_model->GetFullArticle($latest_article_ids[0]);
				/// Check if article requested doesn't exist
				if ($main_article === NULL) {
					redirect('/news/'.$article_type);
				}
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
		foreach ($data['articles'] as &$article) {
			$article['photo_xhtml'] = $this->image->getThumb($article['photo_id'], 'small', false, array('class' => 'Left'));
		}
		$data['total'] = $config['total_rows'];

		/// Set up the public frame
		$this->main_frame->SetTitle('Archive');
		$this->main_frame->SetContentSimple('news/archive', $data);

		/// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	/// RSS Feed Generation
	function rss()
	{
		header('Content-type: application/rss+xml');
		$data['rss_title'] = 'News';
		$data['rss_link'] = 'http://www.theyorker.co.uk/news/';
		$data['rss_desc'] = 'All the news you need to know about from University of York\'s Campus!';
		$data['rss_category'] = 'News';
		$data['rss_pubdate'] = date('r');
		$data['rss_lastbuild'] = date('r');
		$data['rss_image'] = 'http://www.theyorker.co.uk/images/prototype/news/rss-uninews.jpg';
		$data['rss_width'] = '126';
		$data['rss_height'] = '126';
		$data['rss_email_ed'] = 'no-reply@theyorker.co.uk (The Yorker)';
		$data['rss_email_web'] = 'webmaster@theyorker.co.uk (Webmaster)';

		/// Create RSS Feed for all sections
		$data['rss_items'] = $this->News_model->GetArchive('search', array(), 0, 20);

		$this->load->view('news/rss', $data);
	}
}
?>
