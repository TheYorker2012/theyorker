<?php

/**
 * @brief model test controller.
 * @author Alex Fargus(agf501@cs.york.ac.uk)
 */
class ModelTest extends Controller {
	
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
	
		// Load the public frame
		$this->load->library('frame_public');
	}
	
	/**
	 * @brief ModelTest test page.
	 */
	function index()
	{
		$this->load->model('news_model','news');
		$this->load->model('article_model','article');
		$this->load->model('wikicache_model','wiki');
		
		//Load data from model
		$article = $this->news->GetSummaryArticle(1);
		$id = $this->news->GetLatestId(1,2);
		
		// Set up the public frame
		$this->frame_public->SetTitle('Bio');
		$this->frame_public->SetContentSimple('test/modeltest', $article);

		//Testing data add
//		$this->article->CommitArticle(1,NULL,2,NULL,'Sample Heading','Sample Subheading',
//									'This is subtext', 'the yorker wikitext', 'and some magical blurb');

		//add article wikicache
//		$this->wiki->UpdateWikicache();
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}
?>
