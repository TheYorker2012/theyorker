<?php

class Freshers extends Controller
{

	function __construct()
	{
		parent::Controller();
		$this->load->model('home_hack_model');
	}

	function index()
	{
		if (!CheckPermissions('public')) return;

		$spotlight = $this->home_hack_model->getArticlesByTags(array('freshers', 'spotlight'), 1);
		$this->home_hack_model->ignore($spotlight);
		//$inmyview = $this->home_hack_model->getArticlesByTags(array('in-my-view'), 2);
		//$this->home_hack_model->ignore($inmyview);
		$features = $this->home_hack_model->getArticlesByTags(array('freshers', 'feature'), 6);
		$this->home_hack_model->ignore($features);
		
		$blogs = $this->home_hack_model->getArticlesByTags(array('freshers-blogs'), 6);
		$this->home_hack_model->ignore($blogs);

		$sports = $this->home_hack_model->getArticlesByTags(array('freshers-sports'), 6);
		$this->home_hack_model->ignore($sports);
		
		$lifestyle = $this->home_hack_model->getArticlesByTags(array('freshers-lifestyle'), 6);
		$this->home_hack_model->ignore($lifestyle);
		
		$arts = $this->home_hack_model->getArticlesByTags(array('freshers-arts'), 6);
		$this->home_hack_model->ignore($arts);
		
		$satire = $this->home_hack_model->getArticlesByTags(array('freshers-satire'), 6);
		$this->home_hack_model->ignore($satire);
		
		$politics = $this->home_hack_model->getArticlesByTags(array('freshers-politics'), 6);
		$this->home_hack_model->ignore($politics);
						
		$uninews = $this->home_hack_model->getArticlesByTags(array('freshers-news'), 16);
		//$this->home_hack_model->ignore($uninews);
		
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
			'type'			=>	'article_list',
			'title'			=>	'latest sports',
			'title_link'	=>	'',
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	true,
			'articles'		=>	$sports
		);		
		
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest blogs',
			'title_link'	=>	'',
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$blogs
		);			
		
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest lifestlye',
			'title_link'	=>	'',
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$lifestyle
		);	
				
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest arts',
			'title_link'	=>	'',
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	true,
			'articles'		=>	$arts
		);	
		
		
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest politics',
			'title_link'	=>	'',
			'size'			=>	'1/3',
			'position'		=>	'right',
			'last'			=>	false,
			'articles'		=>	$politics
		);	
		
		$boxes[] = array(
			'type'			=>	'adsense_third',
			'position'		=>	'',
			'last'			=>	false
		);
		

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest satire',
			'title_link'	=>	'',
			'size'			=>	'1/3',
			'position'		=>	'right',
			'last'			=>	true,
			'articles'		=>	$satire
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
		$this->pages_model->SetPageCode('homepage_freshers');
		$this->main_frame->SetData('menu_tab', 'freshers');
		$this->main_frame->SetContentSimple('flexibox/layout', $data);
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->Load();
	}
}
?>
