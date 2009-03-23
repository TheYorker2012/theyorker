<?php
/*
 * Arts homepage controller
 *@author Owen Jones (oj502@york.ac.uk)
 */
class Arts extends Controller
{
	function __construct()
	{
		parent::Controller();
		$this->load->model('News_model');
		$this->load->model('Home_Model');
		$this->load->model('Home_Hack_Model');
	}
	
	private function getNumberOfType($articles,$type_codename)
	{
		$count=0;
		foreach ($articles as $article)
		{
			if($article['article_type']==$type_codename){$count++;}
		}
		return $count;
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;

		$homepage_article_type = 'arts';
		// Get page properties information
		$this->pages_model->SetPageCode('homepage_'.$homepage_article_type);
		$main_articles_num = (int)$this->pages_model->GetPropertyText('max_num_main_articles');
		$more_articles_num = (int)$this->pages_model->GetPropertyText('max_num_more_articles');

		// Obtain banner for homepage
		$data['banner'] = $this->Home_Model->GetBannerImageForHomepage($homepage_article_type);

		// Get main article
		$featured_article_id = $this->News_model->GetLatestFeaturedId($homepage_article_type);
		if ($featured_article_id === NULL) {
			// No featured article, use the most recent
			$latest_article_id = $this->News_model->GetLatestId($homepage_article_type, 1);
			$featured_article_id = $latest_article_id[0];
		}
		$data['main_articles'] = $this->Home_Hack_Model->getArticleTitles(array($featured_article_id), '%W, %D %M %Y');

		// Get sub-types for article section
		$data['section_articles'] = array();
		$sub_types = $this->News_model->getSubArticleTypes($homepage_article_type);
		foreach ($sub_types as $type) {
			// Make sure we don't get duplicate articles
			$offset = $this->getNumberOfType($data['main_articles'], $type['codename']);
			$article_ids = $this->News_model->GetLatestId($type['codename'], $more_articles_num, $offset);
			$data['section_articles'][strtolower($type['name'])] = $this->Home_Hack_Model->getArticleTitles($article_ids, '%W, %D %M %Y');
		}

		$this->main_frame->SetData('menu_tab', 'arts');
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->SetContentSimple('homepages/'.$homepage_article_type, $data);
		$this->main_frame->Load();
	}
}
?>
