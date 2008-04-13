<?php
/**
 *	@brief		The Yorker - Article Manager
 *	@version	2.0
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Article extends Controller
{

	function __construct()
	{
		parent::Controller();
		$this->load->model('articlemanager_model');
		//$this->load->model('news_model');
		//$this->load->model('article_model');
		//$this->load->model('requests_model');
		//$this->load->model('photos_model');
	}

	/**
	 *	@brief Decide whether to create a new article, display a current article or error
	 */
	function _remap($article_id = NULL)
	{
		if ((is_numeric($article_id)) && ($article_id == 0)) {
			// Create a new article
			$this->_createArticle();
		} elseif ((is_numeric($article_id)) && ($this->articlemanager_model->isArticle($article_id))) {
			// Show requested article
			$this->_showInterface($article_id);
		} else {
			// Couldn't find article
			show_404();
		}
	}

	function _createArticle()
	{
		if (!CheckPermissions('office')) return;
		// @TODO: Create article permission
		$article_id = $this->articlemanager_model->createArticle($this->user_auth->entityId);
		redirect('/office/article/' . $article_id);
	}

	function _showInterface($article_id)
	{
		if (!CheckPermissions('office')) return;
		// @TODO: View article permission

		$data = array();
		$this->pages_model->SetPageCode('office_news_article');

		// Can user edit article?
		// Editors
		// Assigned editor
		// Assigned & Accepted writers
		$data['readonly'] = FALSE;

		$this->main_frame->SetTitleParameters(
			array('title' => (empty($data['article_headline']) ? ((empty($data['request_title'])) ? 'New Article' : $data['request_title'] ) : $data['article_headline']))
		);
		$this->main_frame->IncludeCss('/stylesheets/article_manager.css');
		$this->main_frame->IncludeJs('/javascript/article_manager.js');
		$this->main_frame->SetContentSimple('office/news/article_manager', $data);
		$this->main_frame->Load();
	}

}

?>
