<?php
/*************************************************
 * Yorker Homepage Controller
 * Original Authors - Nick Evans and Alex Fargus
 * Modified by Richard Crosby
 ************************************************/
class Home2 extends Controller {

	function Home2()
	{
		parent::Controller();

		//load models
		$this->load->model('News_model');
		$this->load->model('home_model');
		$this->load->model('Links_Model');
		$this->load->model('Article_Model');
		$this->load->model('polls_model');

		//load libraries
		$this->load->library('Homepage_boxes');
		$this->load->library('Polls_view');
	}
	
	function _FacebookHome()
	{
		$this->pages_model->SetPageCode('home_facebook');
		$this->main_frame->SetContentSimple('facebook/home');
		$this->main_frame->Load();
	}
	
	function facebook()
	{
		OutputModes(array('xhtml','fbml'));
		if (!CheckPermissions('public')) return;
		return $this->_FacebookHome();
	}

	function index()
	{

//		$this->output->enable_profiler(TRUE);

		OutputModes(array('xhtml','fbml'));
		if (!CheckPermissions('public')) return;

		if ('fbml' === OutputMode()) {
			return $this->_FacebookHome();
		}

		//load models
		$this->load->model('flickr_model');
		$this->load->model('comments_model');
		$this->load->model('advert_model');
		$this->load->model('home_model');

		//sets up the data to be used in the flexiboxes
		$spotlight 	   = $this->home_model->getArticlesByTags(array('front-page'), 1);
		$uninews   	   = $this->home_model->getArticlesByTags(array('news'),       8);
		$sport     	   = $this->home_model->getArticlesByTags(array('sport'),      8);
		$arts     	   = $this->home_model->getArticlesByTags(array('arts'),       8);
		$comment   	   = $this->home_model->getArticlesByTags(array('comment'),    6);
		$lifestyle       = $this->home_model->getArticlesByTags(array('lifestyle'),  8);
		$comments_config = $this->config->item('comments');
		$comments	   = $this->comments_model->GetLatestComments(10);
		$this->home_model->ignore($spotlight);
		
		//sets up page adverts
		$this->load->library('adverts');
		$advert = $this->advert_model->SelectLatestAdvert();
		
		
		//flexibox view settings	
		$boxes[] = array(
			'type'			=>	'spotlight',
			'articles'		=>	$spotlight
		);

		$boxes[] = array(
			'type'			=>	'adsense_third',
			'last'			=>	true,
			'advert'		=>     $advert			
		);

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'		=>	'Lifestyle',
			'title_link'		=>	'/lifestyle',
			'size'			=>	'2/3',
			'last'			=>	true,
			'articles'		=>	$lifestyle
		);

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'		=>	'News',
			'title_link'		=>	'/news',
			'last'			=>	false,
			'size'			=>	'2/3',
			'articles'		=>	$uninews
		);

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'		=>	'Sports',
			'title_link'		=>	'/sport',
			'size'			=>	'2/3',
			'last'			=>	false,
			'articles'		=>	$sport
		);

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'		=>	'Arts',
			'title_link'		=>	'/arts',
			'size'			=>	'2/3',
			'last'			=>	false,
			'articles'		=>	$arts
		);


		
		$boxes[] = array(
			'type'			=>	'comments_latest',
			'title'		=>	'Latest Comments',
			'title_link'		=>	'',
			'size'			=>	'1/2',
			'last'			=>	true,
			'comments'		=>	$comments,
			'comments_per_page'  => 	'10'
		);

		$boxes[] = array(
			'type'			=>	'article_list',
			'title'		=>	'Comments',
			'title_link'		=>	'/comment',
			'size'			=>	'1/2',
			'last'			=>	false,
			'articles'		=>	$comment
		);
		
		$boxes[] = array(
			'type'			=>	'adsense_half',
			'last'			=>	false
		);

		$boxes[] = array(
			'type'			=>	'advert_half',
			'last'			=>	true
		);

		$data = array(
			'boxes'		=>	$boxes
		);

		
		$this->pages_model->SetPageCode('home_main');
		$this->main_frame->SetData('menu_tab', 'home');
		$this->main_frame->SetContentSimple('flexibox/layout', $data);
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->Load();
	}

}
?>
