<?php

class Howdoi extends Controller {

	/**
	 * @brief Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}
	
    function index()
    {
		$this->load->model('howdoi_model','howdoi');
		$this->load->model('news_model','news');
		$this->pages_model->SetPageCode('howdoi_list');

		$data['sidebar_ask'] = array('title'=>$this->pages_model->GetPropertyText('sidebar_ask_title',TRUE),
						'text'=>$this->pages_model->GetPropertyWikitext('sidebar_ask_text',TRUE));
		$data['section_howdoi'] = array('title'=>$this->pages_model->GetPropertyText('section_howdoi_title',FALSE),
						'text'=>$this->pages_model->GetPropertyWikitext('section_howdoi_text',FALSE));

		$data['categories'] = $this->howdoi->GetContentCategories(10);
		foreach ($data['categories'] as $category_id => $category)
		{
			$data['categories'][$category_id]['articles'] = $this->howdoi->GetCategoryArticleIDs($category_id);
			foreach ($data['categories'][$category_id]['articles'] as $article_id => $category_article)
			{
                        	$data['categories'][$category_id]['articles'][$article_id] = $this->news->GetSimpleArticle($category_article);
			}
		}

		// Set up the public frame
		$this->frame_public->SetContentSimple('howdoi/howdoi', $data);

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
    }
	
    function viewcategories($codename, $id)
    {
		$this->load->model('howdoi_model','howdoi');
		$this->load->model('news_model','news');
		$this->pages_model->SetPageCode('howdoi_view');

		$data['categories'] = $this->howdoi->GetContentCategories(10);
		$view_category_id = -1;
		foreach ($data['categories'] as $category_id => $category)
		{
                	if ($category['codename'] == $codename)
                        	$view_category_id = $category_id;
		}
		// if category exists then load page otherwise error
		if ($view_category_id >= 0)
		{
			// Gets the articles for the categories article ids
			$article_temp = $this->howdoi->GetCategoryArticleIDs($view_category_id);
			foreach ($article_temp as $category_article)
			{
	                        $data['categories'][$view_category_id]['articles'][] = $this->news->GetFullArticle($category_article);
			}

			// Gets the sidebar page_properties
			$data['sidebar_ask'] = array('title'=>$this->pages_model->GetPropertyText('sidebar_ask_title',TRUE),
							'text'=>$this->pages_model->GetPropertyWikitext('sidebar_ask_text',TRUE));

			// Load the public frame view (which will load the content view)
			$this->frame_public->Load();
	                $data['parameters'] = array('category'=>$view_category_id,'codename'=>$codename,'article'=>$id);
	
			// Set up the public frame
			$this->frame_public->SetTitle($this->pages_model->GetTitle(array('category'=>$data['categories'][$view_category_id]['name'])));
			$this->frame_public->SetContentSimple('howdoi/view', $data);
	
			// Load the public frame view (which will load the content view)
			$this->frame_public->Load();
		}
		else
			//needs a new page to show invalid category
			echo 'invalid category';
    }
}
?>
