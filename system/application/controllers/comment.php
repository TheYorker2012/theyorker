<?php

class Comment extends Controller
{
	function __construct()
	{
		parent::Controller();
		$this->load->model('home_hack_model2', 'home_hack_model');
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;

		$spotlight = $this->home_hack_model->getArticlesByTags(array('comment', 'spotlight'), 1);
		$this->home_hack_model->ignore($spotlight);
		$features = $this->home_hack_model->getArticlesByTags(array('comment', 'feature'), 9);
		$this->home_hack_model->ignore($features);
		$blog = $this->home_hack_model->getArticlesByTags(array('comment-blog'));
		$politics = $this->home_hack_model->getArticlesByTags(array('comment-politics'));
		$double_take = $this->home_hack_model->getArticlesByTags(array('double-take'));

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
			'title'			=>	'Blogs',
			'title_link'	=>	$blog[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$blog
		);
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'Politics',
			'title_link'	=>	$politics[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$politics
		);
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'Double Take',
			'title_link'	=>	$double_take[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$double_take
		);
		$boxes[] = array(
			'type'			=>	'adsense_third',
			'position'		=>	'',
			'last'			=>	false
		);


		$data = array(
			'boxes'	=>	$boxes
		);
		$this->pages_model->SetPageCode('homepage_comment');
		$this->main_frame->SetData('menu_tab', 'comment');
		$this->main_frame->SetContentSimple('flexibox/layout', $data);
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->Load();
	}
}
?>
