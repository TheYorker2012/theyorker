<?php
/*
 * Office article type manager
 *@author Owen Jones (oj502@york.ac.uk)
 */
class Specials extends Controller
{
	function __construct()
	{
		parent::Controller();
		$this->load->model('Article_model');
		$this->load->model('News_model');
	}
	
	function index()
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$this->pages_model->SetPageCode('office_specials');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		
		if(!empty($_POST['specials_edit']))
		{
			$new_id = $_POST['special_article'];
			$codename = $_POST['article_type'];
			$old_id = $this->News_model->GetLatestFeaturedId($codename);
			if(!empty($new_id)){
				$new_article = $this->News_model->GetSimpleArticle($new_id);
				if(empty($new_article)){
					$this->messages->AddMessage('error','Invalid article, cannot be made special.');
				}else{
					//Get old id
					//remove special from old id, and make new one special.
					$this->messages->AddMessage('success','Updated special article of section "'.$codename.'".');
					if($old_id==$new_id){
						$this->messages->AddMessage('information','No change has been made to section "'.$codename.'".');
					}else{
						$this->Article_model->SetArticleFeatured($new_id, 1);
						$this->Article_model->SetArticleFeatured($old_id, 0);
					}
				}
			} else {
				if(empty($old_id)){
					$this->messages->AddMessage('information','No change has been made to section "'.$codename.'".');
				}else{
					$this->messages->AddMessage('success','Removed special article of section "'.$codename.'".');
					//remove special article
					$this->Article_model->SetArticleFeatured($old_id, 0);
				}
				
			}
		}
				
		$main_article_types = $this->Article_model->getMainArticleTypes();
		$index = 0;
		foreach ($main_article_types as $section){
			$id = $this->News_model->GetLatestFeaturedId($main_article_types[$index]['codename']);
			if(!empty($id)){
				$main_article_types[$index]['featured_article'] = $this->News_model->GetSimpleArticle($id);
			}
			$index++;
		}
		$data['main_articles'] = $main_article_types;
		$this->main_frame->SetContentSimple('office/specials/specials', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	
	function edit($id)
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$this->pages_model->SetPageCode('office_specials_edit');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$num_of_articles_to_get = (int)$this->pages_model->GetPropertyText('num_articles_to_show');//Max number articles to pick from
		
		//Get information about the article type
		$article_type = $this->Article_model->getSubArticleType($id);
		//Use codename to get list of articles to possible change the special to.
		$articles = array();
		$article_ids = $this->News_model->GetLatestId($article_type['codename'],$num_of_articles_to_get, false);
		foreach($article_ids as $article_id){
			$article = $this->News_model->GetSimpleArticle($article_id);
			$article['id'] = $article_id;
			$articles[] = $article;
		}
		
		//Find out the current featured article (if there is one)
		$current_special_id = $this->News_model->GetLatestFeaturedId($article_type['codename']);
		
		//Load Data into data to go to view
		$data['articles'] = $articles;
		$data['article_type'] = $article_type;
		$data['current_special_id'] = $current_special_id;
		$this->main_frame->SetContentSimple('office/specials/edit', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}