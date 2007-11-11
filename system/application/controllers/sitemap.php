<?php

/**
 * This is the controller for the sitemap.
 *
 * @author Nick Evans
 */

class Sitemap extends Controller {

	/**
	 * @brief Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();
		// Load news model
		$this->load->model('News_model');
	}

	/// Sitemap Generation
	function index()
	{
		header('Content-type: application/xml');
		/// Get latest article ids
		$all_articles = $this->News_model->getNewsArticlesSitemap();

		/// Get preview data for articles
		$data['url_items'] = array();

		array_push($data['url_items'], array('loc' => 'http://www.theyorker.co.uk/',
											 'lastmod' => date('Y-m-d'),
											 'changefreq' => 'always',
											 'priority' => 1));

		foreach ($all_articles as $article)
		{
		array_push($data['url_items'], array('loc' => 'http://www.theyorker.co.uk/news/'.$article['content_type_codename'].'/'.$article['article_id'],
											 'lastmod' => date('Y-m-d',strtotime($article['updated_date'])),
											 'changefreq' => 'never',
											 'priority' => 0.5));
		}

		$this->load->view('sitemap/sitemap', $data);
	}

}