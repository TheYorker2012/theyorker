<?php
/*
 * Sport homepage controller
 *@author Owen Jones (oj502@york.ac.uk)
 */
class Sport extends Controller
{
	function __construct()
	{
		parent::Controller();
		$this->load->model('News_model');
		$this->load->model('Home_Model');
		$this->load->library('Homepage_boxes');
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;
		
		//Get page properties information
		$this->pages_model->SetPageCode('sport');
		$data['latest_heading'] = $this->pages_model->GetPropertyText('latest_heading');
		$data['more_heading'] = $this->pages_model->GetPropertyText('more_heading');
		$data['links_heading'] = $this->pages_model->GetPropertyText('links_heading');
		
		$main_articles_num = (int)$this->pages_model->GetPropertyText('max_num_main_sport_articles');//Max number of main articles to show
		$more_articles_num = (int)$this->pages_model->GetPropertyText('max_num_more_sport_articles');//Max number of more articles to show
		
		//Obtain banner for homepage
		$data['banner'] = $this->Home_Model->GetBannerImage('sportbanner');
		
		//////////////Information for main article(s)
		//Get article ids for the main section
		$main_article_ids = $this->News_model->GetLatestId('sport',3);
		//First article has summery, rest are simple articles
		$main_article_summarys[0] = $this->News_model->GetSummaryArticle($main_article_ids[0], "Left", '%W, %D %M %Y', "medium");
		for ($index = 1; $index <= ($main_articles_num-1) && $index < count($main_article_ids); $index++) {
			array_push($main_article_summarys, $this->News_model->GetSimpleArticle($main_article_ids[$index], "Left"));
		}
		
		//////////////Information for more article list(s)
		//Get list of article types
		$more_article_types = $this->News_model->getSubArticleTypes('sport');
		//////For each article type get list of simple articles to the limit of $more_articles_num
		$article_index = 0;
		foreach ($more_article_types as $an_article){
			//Get article id's for that article type up to limit of $more_articles_num
			$articles_ids[$article_index] = $this->News_model->GetLatestId($an_article['codename'],$more_articles_num);
				//for the new article type found get a simple article for each of the ids found.
				for ($index = 0; $index <= ($more_articles_num-1) && $index < count($articles_ids[$article_index]); $index++) {
					$articles_summarys[$article_index][] = $this->News_model->GetSimpleArticle($articles_ids[$article_index][$index], "Left");
				}
			$article_index++;
		}
		
		//Move article information into send data
		$data['main_sport'] = $main_article_summarys;//list of main sport summary
		$data['sport_lists'] = $articles_summarys;//array of sport article lists
		$data['show_sports'] = $more_article_types;//list of sport article types
		
		// Set up the public frame
		$this->main_frame->SetExtraCss('/stylesheets/home.css');
		$this->main_frame->SetContentSimple('sport/index', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}
?>
