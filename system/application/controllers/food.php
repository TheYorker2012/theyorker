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

		$page_codename = 'food';

		$this->pages_model->SetPageCode('homepage_' . $page_codename);

		// Obtain banner for homepage
		//$data['banner'] = $this->Home_Model->GetBannerImageForHomepage($homepage_article_type);

		$data = array();
		$data['spotlight'] = $this->home_hack_model->getArticlesByTags(array('food', 'spotlight'), 1);
		$data['features'] = array(
			'title'		=>	'features',
			'articles'	=>	$this->home_hack_model->getArticlesByTags(array('food', 'feature'), 9)
		);
		$data['sections'] = array(
			array(
				'title'		=>	'the broke student',
				'articles'	=>	$this->home_hack_model->getArticlesByTags(array('the-broke-student'))
			),
                        array(
                                'title'         =>      'weird and wonderful',
                                'articles'      =>      $this->home_hack_model->
getArticlesByTags(array('weird-and-wonderful'))
                        ),
			array(
				'title'		=>	'the taste test',
				'articles'	=>	$this->home_hack_model->getArticlesByTags(array('the-taste-test'))
			)
		);
		

		$this->main_frame->SetData('menu_tab', $page_codename);
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->SetContentSimple('homepages/'.$page_codename, $data);
		$this->main_frame->Load();
	}
}
?>
