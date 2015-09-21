<?php

class Food extends Controller
{
	function __construct()
	{
		parent::Controller();
		$this->load->model('home_hack_model');
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;

		$spotlight = $this->home_hack_model->getArticlesByTags(array('food', 'spotlight'), 1);
		$this->home_hack_model->ignore($spotlight);
		$features = $this->home_hack_model->getArticlesByTags(array('food', 'feature'), 9);
		$this->home_hack_model->ignore($features);
		$broke_student = $this->home_hack_model->getArticlesByTags(array('the-broke-student'));
		$weird_wonderful = $this->home_hack_model->getArticlesByTags(array('weird-and-wonderful'));
		$taste_test = $this->home_hack_model->getArticlesByTags(array('the-taste-test'));

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
			'title'			=>	'the broke student',
			'title_link'	=>	$broke_student[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$broke_student
		);
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'weird and wonderful',
			'title_link'	=>	$weird_wonderful[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$weird_wonderful
		);
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'the taste test',
			'title_link'	=>	$taste_test[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$taste_test
		);
		$boxes[] = array(
			'type'			=>	'adsense_third',
			'position'		=>	'',
			'last'			=>	false
		);


		$data = array(
			'boxes'	=>	$boxes
		);
		$this->pages_model->SetPageCode('homepage_food');
		$this->main_frame->SetData('menu_tab', 'food');
		$this->main_frame->SetContentSimple('flexibox/layout', $data);
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->Load();
	}
}
?>
