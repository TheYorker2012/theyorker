<?php

class Uninews extends Controller
{

	private $displayedArticleIDs;

	function __construct()
	{
		parent::Controller();
		$this->load->model('home_hack_model');
	}

	function _getIDs ($articles)
	{
		if (!empty($articles)) {
			foreach ($articles as $a) {
				$this->displayedArticleIDs[] = $a['id'];
			}
		}
	}

	function index()
	{
		if (!CheckPermissions('public')) return;

		$page_codename = 'news';

		$this->pages_model->SetPageCode('homepage_' . $page_codename);

		$this->displayedArticleIDs = array();
		$spotlight = $this->home_hack_model->getArticlesByTags(array('news', 'spotlight'), 1, $this->displayedArticleIDs);
		$this->_getIDs($spotlight);
		$features = $this->home_hack_model->getArticlesByTags(array('news', 'feature'), 5, $this->displayedArticleIDs);
		$this->_getIDs($features);
		$blogs = $this->home_hack_model->getArticlesByTags(array('news', 'blog'), 5, $this->displayedArticleIDs);
		$this->_getIDs($blogs);
		$section_news = $this->home_hack_model->getArticlesByTags(array('news'), 14, $this->displayedArticleIDs);
		$this->_getIDs($section_news);

		$data = array();
		$data['spotlight'] = $spotlight;
		$data['features'] = array(
			'title'		=>	'features',
			'articles'	=>	$features
		);
		$data['blogs'] = array(
			'title'		=>	'comment',
			'articles'	=>	$blogs
		);
		$data['sections'] = array(
			array(
				'title'		=>	'news',
				'articles'	=>	$section_news
			)
		);

		$this->main_frame->SetData('menu_tab', $page_codename);
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->SetContentSimple('homepages/'.$page_codename, $data);
		$this->main_frame->Load();
	}
}
?>
