<?php

class Uninews extends Controller
{

	function __construct()
	{
		parent::Controller();
		$this->load->model('home_hack_model2', 'home_hack_model');
	}

	function index()
	{
		if (!CheckPermissions('public')) return;

		$spotlight = $this->home_hack_model->getArticlesByTags(array('news', 'spotlight'), 1);
		$this->home_hack_model->ignore($spotlight);
		//$inmyview = $this->home_hack_model->getArticlesByTags(array('in-my-view'), 2);
		//$this->home_hack_model->ignore($inmyview);
		$features = $this->home_hack_model->getArticlesByTags(array('news', 'feature'), 6);
		$this->home_hack_model->ignore($features);
		$blogs = $this->home_hack_model->getArticlesByTags(array('news', 'blog'), 6);
		$this->home_hack_model->ignore($blogs);
		$uninews = $this->home_hack_model->getArticlesByTags(array('news'), 16);

		$boxes = array();

		$boxes[] = array(
			'type'			=>	'spotlight',
			'articles'		=>	$spotlight
		);
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest features',
			'title_link'	=>	'',
			'size'			=>	'1/3',
			'position'		=>	'right',
			'last'			=>	true,
			'articles'		=>	$features
		);
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest news',
			'title_link'	=>	'',
			'size'			=>	'2/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$uninews
		);
		$boxes[] = array(
			'type'			=>	'adsense_third',
			'position'		=>	'',
			'last'			=>	true
		);
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest comment',
			'title_link'	=>	'',
			'size'			=>	'1/3',
			'position'		=>	'right',
			'last'			=>	true,
			'articles'		=>	$blogs
		);
		/*
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'in my view',
			'title_link'	=>	'/news/' . $inmyview[0]['id'],
			'title_image'	=>	'/images/version2/banners/in_my_view2.png',
			'size'			=>	'2/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$inmyview
		);
		*/

		
		$data = array(
			'boxes'	=>	$boxes
		);
		$this->pages_model->SetPageCode('homepage_news');
		$this->main_frame->SetData('menu_tab', 'news');
		$this->main_frame->SetContentSimple('flexibox/layout', $data);
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->Load();
	}
}
?>
