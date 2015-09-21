<?php

class Varsity10 extends Controller
{
	function __construct()
	{
		parent::Controller();
		$this->load->model('home_hack_model');
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;

		$spotlight = $this->home_hack_model->getArticlesByTags(array('varsity10', 'spotlight'), 1);
		$this->home_hack_model->ignore($spotlight);
		$features = $this->home_hack_model->getArticlesByTags(array('varsity10', 'feature'), 9);
		$this->home_hack_model->ignore($features);
		$varsity_live = $this->home_hack_model->getArticlesByTags(array('varsity-live'));
		$varsity_match = $this->home_hack_model->getArticlesByTags(array('varsity-match'));
		$varsity_podcast = $this->home_hack_model->getArticlesByTags(array('varsity-podcast'));

		$boxes = array();

		$boxes[] = array(
			'type'			=>	'spotlight',
			'articles'		=>	$spotlight
		);
		
		
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest features',
			'title_link'	=>	$features[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'right',
			'last'			=>	true,
			'articles'		=>	$features
		);
		

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'match reports',
			'title_link'	=>	$varsity_match[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$varsity_match
		);
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'podcast',
			'title_link'	=>	$varsity_podcast[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$varsity_podcast
		);
		$boxes[] = array(
			'type'			=>	'adsense_third',
			'position'		=>	'',
			'last'			=>	false
		);

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'live blog',
			'title_link'	=>	$varsity_live[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'right',
			'last'			=>	false,
			'articles'		=>	$varsity_live
		);
		
		$data = array(
			'boxes'	=>	$boxes
		);
		$this->pages_model->SetPageCode('homepage_varsity10');
		$this->main_frame->SetData('menu_tab', 'varsity10');
		$this->main_frame->SetContentSimple('flexibox/layout', $data);
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->Load();
	}
}
?>
