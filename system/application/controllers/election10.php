<?php

class Election10 extends Controller
{
	function __construct()
	{
		parent::Controller();
		$this->load->model('home_hack_model');
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;

		$spotlight = $this->home_hack_model->getArticlesByTags(array('election10', 'spotlight'), 1);
		$this->home_hack_model->ignore($spotlight);
		$features = $this->home_hack_model->getArticlesByTags(array('election10', 'feature'), 9);
		$this->home_hack_model->ignore($features);
		$election_news = $this->home_hack_model->getArticlesByTags(array('election-news'));
		$election_comment = $this->home_hack_model->getArticlesByTags(array('election-comment'));
				
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
			'title'			=>	'election news',
			'title_link'	=>	$election_news[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$election_news
		);
		
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'election comments',
			'title_link'	=>	$election_comment[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$election_comment
		);
		/*
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'the taste test',
			'title_link'	=>	$taste_test[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$taste_test
		);
		*/
		$boxes[] = array(
			'type'			=>	'adsense_third',
			'position'		=>	'',
			'last'			=>	false
		);


		$data = array(
			'boxes'	=>	$boxes
		);
		$this->pages_model->SetPageCode('homepage_election10');
		$this->main_frame->SetData('menu_tab', 'election10');
		$this->main_frame->SetContentSimple('flexibox/layout', $data);
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->Load();
	}
}
?>
