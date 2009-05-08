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

		$score_york = 0;
		$score_lancs = 0;

		foreach ($data['events'] as $events) {
			if (empty($events['event_score_time'])) continue;
			if ($events['event_york_score'] > $events['event_lancaster_score']) {
				$score_york += $events['event_points'];
			} else if ($events['event_york_score'] < $events['event_lancaster_score']) {
				$score_lancs += $events['event_points'];
			} else {
				$score_york += $events['event_points'] / 2;
				$score_lancs += $events['event_points'] / 2;
			}
		}

		$data['score_york'] = $score_york;
		$data['score_lancs'] = $score_lancs;

		$this->pages_model->SetPageCode('homepage_roses');
		$this->main_frame->SetData('menu_tab', 'roses');
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->SetContentSimple('homepages/roses', $data);
		$this->main_frame->Load();
	}
}
?>
