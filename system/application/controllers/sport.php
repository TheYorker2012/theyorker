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
		$data['featured_puffer_title'] = $this->pages_model->GetPropertyText('featured_puffer_title',TRUE);
		
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
		$articles_summarys = array();
		foreach ($more_article_types as $an_article){
			//Get article id's for that article type up to limit of $more_articles_num
			$articles_ids[$article_index] = $this->News_model->GetLatestId($an_article['codename'],$more_articles_num);
				//for the new article type found get a simple article for each of the ids found.
				for ($index = 0; $index <= ($more_articles_num-1) && $index < count($articles_ids[$article_index]); $index++) {
					//check the article hasnt already been used as a main article
					$found_article = array_search($articles_ids[$article_index][$index], $main_article_ids);
					if($found_article === FALSE){
						$articles_summarys[$article_index][] = $this->News_model->GetSimpleArticle($articles_ids[$article_index][$index], "Left");
					}
				}
			$article_index++;
		}
		
		/////////////Get information for side puffers @Note this is temp, hoping to have links to sport sections on the right hand side, this is a placeholder!!
		//use article types already found by more articles
		$data['puffers'] = array();
		$index = 0;
		foreach ($more_article_types as $puffer) {
			$data['puffers'][$index] = $puffer;
			$data['puffers'][$index]['image'] = '/image/'.$puffer['image_codename'].'/'.$puffer['image'];
			$index++;
		}
		
		//////////////Information for special/featured puffer
		//Get article ID
		$featured_puffer_id = $this->News_model->GetLatestFeaturedId('sport');
		
		//get and article summery for the article id. Using subheader, so much have at least the summery version
		if(!empty($featured_puffer_id)){
			$data['show_featured_puffer'] = true;
			$data['featured_puffer'] = $this->News_model->GetSummaryArticle($featured_puffer_id);
		}else{
			$data['show_featured_puffer'] = false;
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
