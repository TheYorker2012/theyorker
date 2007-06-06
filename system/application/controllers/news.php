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

	function _remap($method)
	{
		if (count($this->News_model->getArticleTypeInformation($method)) > 0) {
			$this->index($method);
		} else {
			$this->$method();
		}
	}

	function index($article_type = 'uninews', $CommentInclude = 0)
	{
		// Load public view
		if (!CheckPermissions('public')) return;

		$type_info = $this->News_model->getArticleTypeInformation($article_type);
		if (count($type_info) == 0) {
			$article_type = 'uninews';
			$type_info = $this->News_model->getArticleTypeInformation($article_type);
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
		$data['latest_heading'] = $this->pages_model->GetPropertyText('latest_heading');
		$data['other_heading'] = $this->pages_model->GetPropertyText('other_heading');
		$data['related_heading'] = $this->pages_model->GetPropertyText('related_heading');
		$data['links_heading'] = $this->pages_model->GetPropertyText('links_heading');

		/// Get the latest article ids from the model.
		$latest_article_ids = $this->News_model->GetLatestId($article_type,8);
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
		$url_article_id = $this->uri->segment(3);
		// Check if an article id was requested, if so check that the type of article it corresponds
		// to is correct for the current news view else ignore it
		if (($url_article_id !== FALSE) && (is_numeric($url_article_id)) && ($this->News_model->IdIsOfType($url_article_id,$article_type))) {
			/// Check if requested article is already one of the IDs returned
			$found_article = array_search($url_article_id, $latest_article_ids);
			if ($found_article !== FALSE) {
				/// If it is, remove it from the list
				unset($latest_article_ids[$found_article]);
			}
			/// Put request article id onto front of array so that it becomes the main article
			$latest_article_ids = array_merge(array($url_article_id),$latest_article_ids);
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
					'text'					=>	$this->pages_model->GetPropertyWikiText('news:no_articles_text',TRUE),
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

		// Get comments
		/*if (is_numeric($main_article['public_thread_id'])) {
			$this->load->library('comments');
			$CommentInclude = $this->uri->segment(4);
			if (FALSE === $CommentInclude) {
				$CommentInclude = NULL;
			}
			$this->comments->SetUri('/news/'.$article_type.'/'.$latest_article_ids[0].'/');
			$data['comments'] = $this->comments->CreateStandard((int)$main_article['public_thread_id'], $CommentInclude);
		}*/

		/// Gather all the data into an array to be passed to the view
		$data['main_article'] = $main_article;
		$data['news_previews'] = $news_previews;
		$data['news_others'] = $news_others;

		// Set up the public frame
		$this->main_frame->SetContentSimple('news/news', $data);

		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	/// News archive section
	function archive()
	{
		if (!CheckPermissions('public')) return;

		/// Pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'news/archive/';
		$config['total_rows'] = $this->News_model->GetArchive('count')->count;
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
		$this->pagination->initialize($config);

		$data['offset'] = $this->uri->segment(3,0);
		if (!is_numeric($data['offset'])) {
			$data['offset'] = 0;
		}

		/// Get all past articles
		$data['articles'] = $this->News_model->GetArchive('search', $data['offset'], $config['per_page']);
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
		$data['rss_email_ed'] = 'no-reply@theyorker.co.uk';
		$data['rss_email_web'] = 'no-reply@theyorker.co.uk';

		/// Get latest article ids
		//$latest_article_ids = $this->News_model->GetLatestId('uninews',9);

		/// Get preview data for articles
		//$data['rss_items'] = array();
		//foreach ($latest_article_ids as $id)
		//{
    	//	array_push($data['rss_items'], $this->News_model->GetSummaryArticle($id));
		//}

		/// Create RSS Feed for all sections
		$data['rss_items'] = $this->News_model->GetArchive('search', 0, 20);

		$this->load->view('news/rss', $data);
	}


    /// test data for use until we can use the database (example national news)
	private static $national_data = array(
		array(
			'link' => 'http://news.bbc.co.uk/go/rss/-/1/hi/uk/6186194.stm',
            'image' => '/images/prototype/news/bbc_news.gif',
            'image_description' => 'Taken from BBC News',
            'headline' => 'Ex-spy death inquiry stepped up',
            'writer' => 'Google',
            'date' => '5th December 2006',
            'subtext' => 'Police step up inquiries into the death of Russian ex-spy Alexander Litvinenko, with officers due to fly to Moscow.'
		),
		array(
			'link' => 'http://news.bbc.co.uk/go/rss/-/1/hi/uk_politics/6186348.stm',
            'image' => '/images/prototype/news/bbc_news.gif',
            'image_description' => 'Taken from BBC News',
            'headline' => 'Olympics audio surveillance row',
            'writer' => 'Google',
            'date' => '5th December 2006',
            'subtext' => 'A police plan to use high-powered microphones to help the Olympics 2012 security is opposed by David Blunkett.'
		),
		array(
			'link' => 'http://news.bbc.co.uk/go/rss/-/1/hi/england/dorset/6186284.stm',
            'image' => '/images/prototype/news/bbc_news.gif',
            'image_description' => 'Taken from BBC News',
            'headline' => 'Missing boy search scaled down',
            'writer' => 'Google',
            'date' => '5th December 2006',
            'subtext' => 'The search for a boy who is missing after the rowing boat he stole with a friend capsized is scaled down overnight.'
		),
	);
}
?>
