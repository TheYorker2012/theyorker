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
	}
	
	/**
	 * @brief ModelTest test page.
	 */
	function index()
	{
		if (!CheckPermissions('public')) return;
		
		$this->load->model('news_model','news');
		$this->load->model('article_model','article');
		$this->load->model('wikicache_model','wiki');
		$this->load->model('requests_model','request');
		
		//Load data from model
		$article = $this->news->GetSummaryArticle(1);
		$id = $this->news->GetLatestId(1,2);
		
		// Set up the public frame
		$this->main_frame->SetTitle('Bio');
		$this->main_frame->SetContentSimple('test/modeltest', $article);

		//Testing data add
//		$this->article->CommitArticle(1,NULL,2,NULL,'Sample Heading','Sample Subheading',
//									'This is subtext', 'the yorker wikitext', 'and some magical blurb');

		//add article wikicache
//		$this->wiki->UpdateWikicache();

		//add factbox wikicache
//		$this->wiki->UpdateFactWikicache();

		//Requests testing
//		$tmp = $this->request->CreateRequest('suggestion','uninews','W00T Bobs fun stuff','Tis about Bob...',1,1171231491);
//		$this->request->AddUserToRequest($tmp,2);
//		$this->request->AcceptRequest(44,2);
//		$this->request->CreateArticleRevision($tmp,2,'Heading','subheading','subtext','Wooot wikitext','teh blurb');
//		$this->request->UpdateArticleRevision(29,2,'Heading has changed','subheading has changed','subtext1','Wooot 2  wikitext','teh blurbage');
//		$this->request->DeclineRequest(44,2);
//		$this->request->RemoveUserFromRequest(44,2);
//		$this->request->UpdateRequestStatus(44,'request',array('editor'=>1,'publish_date'=>1191231491,'title'=>'Bob is Back','description'=>'WooooHoooo'));
//		$this->request->GetArticleRevisions(44);
//		$this->request->GetPulledArticles('uninews');
//		$this->request->GetRequestedArticles('uninews');
//		$this->request->GetSuggestedArticles('uninews');
//		$this->request->GetPublishedArticles('uninews',TRUE);
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}
?>
