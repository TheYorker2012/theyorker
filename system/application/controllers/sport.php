<?php

/**
 *	Sport home page
 *	@author	Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Sport extends Controller
{

	function __construct()
	{
		parent::Controller();
		$this->load->model('home_hack_model');
	}

	function index()
	{
		if (!CheckPermissions('public')) return;

		$spotlight = $this->home_hack_model->getArticlesByTags(array('sport', 'spotlight'), 1);
		$this->home_hack_model->ignore($spotlight);
		$features = $this->home_hack_model->getArticlesByTags(array('sport', 'feature'), 6);
		$this->home_hack_model->ignore($features);
		$blogs = $this->home_hack_model->getArticlesByTags(array('sport', 'blog'), 6);
		$this->home_hack_model->ignore($blogs);

		$football = $this->home_hack_model->getArticlesByTags(array('football'), 3);
		$this->home_hack_model->ignore($football);
		$rugby = $this->home_hack_model->getArticlesByTags(array('rugby'), 3);
		$this->home_hack_model->ignore($rugby);
		$netball = $this->home_hack_model->getArticlesByTags(array('netball'), 3);
		$this->home_hack_model->ignore($netball);
		$lacrosse = $this->home_hack_model->getArticlesByTags(array('lacrosse'), 3);
		$this->home_hack_model->ignore($lacrosse);
		$hockey = $this->home_hack_model->getArticlesByTags(array('hockey'), 3);
		$this->home_hack_model->ignore($hockey);

		$sport = $this->home_hack_model->getArticlesByTags(array('sport'), 8);

		$boxes = array();

		$boxes[] = array(
			'type'			=>	'spotlight',
			'articles'		=>	$spotlight
		);
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest features',
			'title_link'	=>	'/news/' . $features[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'right',
			'last'			=>	true,
			'articles'		=>	$features
		);
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest sport',
			'title_link'	=>	'/news/' . $sport[0]['id'],
			'size'			=>	'2/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$sport
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
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest football',
			'title_link'	=>	'/news/' . $football[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$football
		);
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest rugby',
			'title_link'	=>	'/news/' . $rugby[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$rugby
		);
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest netball',
			'title_link'	=>	'/news/' . $netball[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$netball
		);
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest lacrosse',
			'title_link'	=>	'/news/' . $lacrosse[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$lacrosse
		);
		$boxes[] = array(
			'type'			=>	'article_list',
			'title'			=>	'latest hockey',
			'title_link'	=>	'/news/' . $hockey[0]['id'],
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$hockey
		);


		$data = array(
			'boxes'	=>	$boxes
		);
		$this->pages_model->SetPageCode('homepage_sport');
		$this->main_frame->SetData('menu_tab', 'sport');
		$this->main_frame->SetContentSimple('flexibox/layout', $data);
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->Load();
	}
}
?>
