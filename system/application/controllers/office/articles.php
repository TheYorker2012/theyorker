<?php
/**
 *	Yorker Office - Article Manager
 *
 *	@author Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Articles extends Controller
{
	function __construct()
	{
		parent::Controller();

		$this->load->model('news_model');
		$this->load->model('article_model');
		$this->load->model('requests_model');
		$this->load->model('photos_model');
	}

	function live()
	{
		if (!CheckPermissions('office')) return;

		/// Get changeable page content
		$this->pages_model->SetPageCode('office_scheduled_and_live');

		$data['articlelist'] = $this->news_model->getScheduledAndLive();

		/// Set up the main frame
		$this->main_frame->SetData('menu_tab', 'articles');
		$this->main_frame->SetContentSimple('office/news/scheduled_and_live', $data);
		$this->main_frame->Load();
	}

	function index ()
	{
		if (!CheckPermissions('office')) return;

		/// Get changeable page content
		$this->pages_model->SetPageCode('office_content_schedule');

		$data['articlelist'] = $this->news_model->getContentSchedule();

		/// Set up the main frame
		$this->main_frame->SetData('menu_tab', 'articles');
		$this->main_frame->SetContentSimple('office/news/content_schedule', $data);
		$this->main_frame->Load();
	}

}

?>
