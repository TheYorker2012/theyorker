<?php

/**
 *	Roses Tab
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Roses extends Controller
{

	function __construct()
	{
		parent::Controller();
		$this->load->model('news_model');
		$this->load->model('home_model');
		$this->load->model('home_hack_model');
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;

		$data = array();
		$data['liveblog'] = $this->home_hack_model->getArticlesByTags(array('Roses 2009', 'liveblog'), 1);
		$data['others'] = $this->home_hack_model->getArticlesByTags(array('Roses 2009'), 15);

		$sql = 'SELECT * FROM roses_scores ORDER BY event_time ASC';
		$data['events'] = $this->db->query($sql)->result_array();

		$this->pages_model->SetPageCode('homepage_roses');
		$this->main_frame->SetData('menu_tab', 'roses');
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->SetContentSimple('homepages/roses', $data);
		$this->main_frame->Load();
	}
}
?>
