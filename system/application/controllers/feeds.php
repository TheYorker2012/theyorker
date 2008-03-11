<?php

/**
 *	@brief		Controller providing all the RSS feeds
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 *	@note		If feed redirection is enabled for stats generation then each feed will need setting up externally!
 */

class Feeds extends Controller {

	function __construct()
	{
		parent::Controller();
		$this->load->model('news_model');
	}

	function _remap($type = NULL, $output = FALSE)
	{
		if ($type === NULL) {
			/// No feed requested so show a list of all those available
			$this->index();
		} elseif (method_exists($this, '_feed_' . $type)) {
			/// Special feed requested
			if ($this->config->item('rss_feed_stats') && (!$output)) {
				redirect('http://feeds.feedburner.com/theyorker-' . $type);
			} else {
				call_user_func_array(array(&$this, '_feed_' . $type), array());
			}
		} elseif (count($this->news_model->getArticleTypeInformation($type)) > 0) {
			/// Article type feed
			if ($this->config->item('rss_feed_stats') && (!$output)) {
				redirect('http://feeds.feedburner.com/theyorker-' . $type);
			} else {
				call_user_func_array(array(&$this, '_articletype'), array($type));
			}
		} else {
			show_404();
		}
	}

	function index()
	{
		if (!CheckPermissions('public')) return;

		$data = array();
		$this->pages_model->SetPageCode('feeds');
		$data['whatis_title'] = $this->pages_model->GetPropertyText('whatis_title');
		$data['whatis_content'] = $this->pages_model->GetPropertyWikitext('whatis_content');
		$data['feeds_title'] = $this->pages_model->GetPropertyText('feeds_title');

		/// Get all available feeds
		$this->load->model('feeds_model');
		$data['feeds'] = array_merge(
			array(
				array('All Articles', 'news', array()),
				array('Comments', 'comments', array()),
				array('Podcasts', 'podcasts', array())
			),
			$this->feeds_model->getArticleTypeFeeds()
		);

		/// Set up the public frame
		$this->main_frame->SetContentSimple('feeds/index', $data);
		$this->main_frame->Load();
	}

	function _feed_podcasts()
	{
		header('Content-type: application/rss+xml');
		$data = $this->_standardfeed();
		$data['rss_title'] = 'Artscast';
		$data['rss_desc'] = 'The Yorker\'s Weekly Artscasts.';
		$data['rss_category'] = 'Arts';
		$data['rss_itunes_summary'] = 'Yorker Arts';
		$data['rss_link'] = 'http://www.theyorker.co.uk/arts/';
		$data['rss_itunes_categories'] = array('Arts','Music');
		$data['itunes_image'] = 'http://www.theyorker.co.uk/images/prototype/news/rss-itunes.jpg';
		$data['itunes_author'] = 'The Yorker Arts Team';
		$data['itunes_owner'] = 'The Yorker - Artscast';
		$data['itunes_owner_email'] = 'podcasts@theyorker.co.uk';
		$this->load->model('podcasts_model');
		$data['rss_items'] = $this->podcasts_model->GetPodcastList();
		if(isset($data['rss_items'][0])){$data['rss_pubdate'] = $data['rss_items'][0]['date'];}
		foreach ($data['rss_items'] as &$item)
		{
			$item['type']='audio/mpeg';
		}		
		$this->load->view('feeds/podcasts', $data);
	}

	function _feed_comments()
	{
		header('Content-type: application/rss+xml');
		$data = $this->_standardfeed();
		$data['rss_title'] = 'Comments';
		$data['rss_desc'] = 'User comments on articles published on The Yorker';
		$data['comments_per_page'] = 20;
		$this->load->model('comments_model');
		/// Create RSS feed for latest comments
		$data['rss_items'] = $this->comments_model->GetLatestComments(30);
		$this->load->view('feeds/rss_comments', $data);
	}

	function _feed_news()
	{
		header('Content-type: application/rss+xml');
		$data = $this->_standardfeed();
		/// Create RSS feed for all sections
		$data['rss_items'] = $this->news_model->GetArchive('search', array(), 0, 30);
		$this->load->view('feeds/rss_articles', $data);
	}

	function _articletype($type)
	{
		header('Content-type: application/rss+xml');
		$type_info = $this->news_model->getArticleTypeInformation($type);
		$data = $this->_standardfeed();
		$filter = array('section', $type_info['id']);
		$data['rss_title'] = $type_info['name'];
		/// Create RSS feed for particular type
		$data['rss_items'] = $this->news_model->GetArchive('search', array($filter), 0, 30);
		$this->load->view('feeds/rss_articles', $data);
	}

	function _standardfeed()
	{
		return array(
			'rss_title'		=>	'Campus News',
			'rss_link'		=>	'http://' . $_SERVER['SERVER_NAME'] . $this->uri->uri_string(),
			'rss_desc'		=>	'All the news you need to know about from University of York\'s Campus!',
			'rss_category'	=>	'News',
			'rss_pubdate'	=>	date('r'),
			'rss_lastbuild'	=>	date('r'),
			'rss_image'		=>	'http://' . $_SERVER['SERVER_NAME'] . '/images/prototype/news/rss-uninews.jpg',
			'rss_width'		=>	'126',
			'rss_height'	=>	'126',
			'rss_email_ed'	=>	$this->config->item('editor_email_address') . ' (Editor)',
			'rss_email_web'	=>	$this->config->item('webmaster_email_address') . ' (Webmaster)',
			'rss_email_no'	=>	$this->config->item('no_reply_email_address')
		);
	}
}
?>
