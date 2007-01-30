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
		
		//Load data from model
		$article = $this->news->GetSummaryArticle(1);
		$id = $this->news->GetLatestId(1,2);
		
		// Set up the public frame
		$this->frame_public->SetTitle($id[1]);
		$this->frame_public->SetContentSimple('test/modeltest', $article);

		//Testing data add
//		$this->article->CommitArticle(1,NULL,2,'CURRENT_TIMESTAMP','Sample Heading','Sample Subheading',
//									'This is subtext', 'the yorker wikitext', 'and some magical blurb');
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}
?>
