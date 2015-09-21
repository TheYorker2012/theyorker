<?php
/*************************************************
 * Yorker Sports Page Controller
 * Original Authors - Chris Travis
 * Modified by Richard Crosby
 ************************************************/
class Sport extends Controller
{

	function index()
	{
		if (!CheckPermissions('public')) return;

		//load models
		$this->load->model('home_hack_model2', 'home_hack_model');


		//sets up the data to be used in the flexiboxes
		$spotlight = $this->home_hack_model->getArticlesByTags(array('sport', 'spotlight'), 1);
		$features = $this->home_hack_model->getArticlesByTags(array('sport', 'feature'), 6);
		$blogs = $this->home_hack_model->getArticlesByTags(array('sport', 'blog'), 6);
		$football = $this->home_hack_model->getArticlesByTags(array('football'), 6);
		$rugby = $this->home_hack_model->getArticlesByTags(array('rugby'), 3);
		$cricket = $this->home_hack_model->getArticlesByTags(array('cricket'), 3);
		$sport = $this->home_hack_model->getArticlesByTags(array('sport'), 8);

		$this->home_hack_model->ignore($spotlight);
		$this->home_hack_model->ignore($features);
		$this->home_hack_model->ignore($blogs);
		$this->home_hack_model->ignore($football);
		$this->home_hack_model->ignore($rugby);
		$this->home_hack_model->ignore($netball);
		$this->home_hack_model->ignore($lacrosse);
		$this->home_hack_model->ignore($hockey);


		//flexibox view settings	
		$boxes[] = array(
			'type'			=>	'spotlight',
			'articles'		=>	$spotlight
		);

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'		=>	'Features',
			'title_link'		=>	'',
			'size'			=>	'1/3',
			'position'		=>	'right',
			'last'			=>	true,
			'articles'		=>	$features
		);

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'		=>	'Football',
			'title_link'		=>	'',
			'size'			=>	'2/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$football
		);

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'		=>	'Sports Comments',
			'title_link'		=>	'',
			'size'			=>	'1/3',
			'position'		=>	'right',
			'last'			=>	true,
			'articles'		=>	$blogs
		);

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'		=>	'Rugby',
			'title_link'		=>	'',
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$rugby
		);

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'		=>	'Cricket',
			'title_link'		=>	'',
			'size'			=>	'1/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$cricket
		);

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'		=>	'Other Sport',
			'title_link'		=>	'',
			'size'			=>	'2/3',
			'position'		=>	'',
			'last'			=>	false,
			'articles'		=>	$sport
		);

		$boxes[] = array(
			'type'			=>	'adsense_half',
			'position'		=>	'',
			'last'			=>	true
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
