<?php

class Howdoi extends Controller {

	/**
	 * @brief Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;
		
		$this->load->model('howdoi_model','howdoi_model');
		$this->load->model('news_model','news_model');
		$this->pages_model->SetPageCode('howdoi_list');

		$data['sidebar_ask'] = array('title'=>$this->pages_model->GetPropertyText('sidebar_ask_title',TRUE),
						'text'=>$this->pages_model->GetPropertyWikitext('sidebar_ask_text',TRUE));
		$data['section_howdoi'] = array('title'=>$this->pages_model->GetPropertyText('section_howdoi_title',FALSE),
						'text'=>$this->pages_model->GetPropertyWikitext('section_howdoi_text',FALSE));

		$data['categories'] = $this->howdoi_model->GetContentCategories(10);
		foreach ($data['categories'] as $category_id => $category)
		{
			$data['categories'][$category_id]['articles'] = $this->howdoi_model->GetCategoryArticleIDs($category_id);
			foreach ($data['categories'][$category_id]['articles'] as $article_id => $category_article)
			{
                        	$data['categories'][$category_id]['articles'][$article_id] = $this->news_model->GetSimpleArticle($category_article);
			}
		}

		// Set up the public frame
		$this->main_frame->SetContentSimple('howdoi/howdoi', $data);

		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	function viewcategory($codename, $id)
	{
		if (!CheckPermissions('public')) return;
		
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

	                $data['parameters'] = array('category'=>$view_category_id,'codename'=>$codename,'article'=>$id);
	
			// Set up the public frame
			$this->main_frame->SetTitle($this->pages_model->GetTitle(array('category'=>$data['categories'][$view_category_id]['name'])));
			$this->main_frame->SetContentSimple('howdoi/view', $data);
	
			// Load the public frame view (which will load the content view)
			$this->main_frame->Load();
		}
		else
			//needs a new page to show invalid category
			redirect('/howdoi');
	}

	function addhowdoi()
	{
		if (!CheckPermissions('public')) return;

		$this->pages_model->SetPageCode('howdoi_view');
	
		// Set up the public frame
		$this->main_frame->SetTitle('cheese');
		$this->main_frame->SetContentSimple('howdoi/addhowdoi', $data);

		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}
?>
