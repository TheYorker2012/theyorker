<?php

/// Office How Do I Pages.
/**
 * @author Nick Evans (nse500@cs.york.ac.uk)
 * @author Richard Ingle (ri504@cs.york.ac.uk)
 */
class Howdoi extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();

		$this->load->helper('text');
		$this->load->helper('wikilink');
	}

	/// Set up the navigation bar
	private function _SetupNavbar()
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('suggestions', 'Suggestions',
				'/office/howdoi/suggestions');
		$navbar->AddItem('categories', 'Categories',
				'/office/howdoi/categories');
		$navbar->AddItem('questions', 'Questions',
				'/office/howdoi');
	}

	/// index page.
	function index()
	{
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('office_howdoi_questions');
		$this->load->model('howdoi_model','howdoi_model');
		$this->load->model('news_model','news_model');
		$this->load->model('article_model','article_model');
		$this->load->model('requests_model','requests_model');
		
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('questions');

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
		$data['status_count'] = array('suggestions'=>0,
						'requests'=>0,
						'unpublished'=>0,
						'published'=>0,
						'pulled'=>0
						);

		//do shabazz
		$howdoi_type_id = $this->howdoi_model->GetHowdoiTypeID();

		$data['categories'] = $this->howdoi_model->GetContentCategories($howdoi_type_id);
		/*
		foreach ($data['categories'] as $category_id => &$category)
		{
			$category['questions'] = $this->howdoi_model->GetOfficeCategoryArticleIDs($category_id);
			foreach ($category['questions'] as $article_content_id => &$category_article)
			{
				$category_article['heading'] = $this->article_model->GetArticleHeader($article_content_id);
				$category_article['revision'] = $this->article_model->GetArticleRevisions($article_content_id, FALSE, 1);
				//question status
				//0=published, 1=awaiting publication, 2=set for release (waiting for publish date)
				$publish_time = strtotime($category_article['heading']['publish_date']);
				if (is_null($category_article['heading']['live_content']))
				{
					$data['status_count']['awaiting_publication'] = $data['status_count']['awaiting_publication'] + 1;
					$category_article['heading']['status'] = 1;
				}
				else if ($publish_time > time())
				{
					$data['status_count']['set_for_publication'] = $data['status_count']['set_for_publication'] + 1;
					$category_article['heading']['status'] = 2;
				}
				else
				{
					$data['status_count']['published'] = $data['status_count']['published'] + 1;
					$category_article['heading']['status'] = 0;
				}
			}
		}
		*/

		$data['categories'][$howdoi_type_id] = array(
					'codename'=>'unassigned',
					'name'=>'Unassigned',
					'suggestions'=>$this->requests_model->GetSuggestedArticles($howdoi_type_id)
					);
		foreach ($data['categories'] as $category_id => $category)
		{
			//suggestions
			$data['categories'][$category_id]['suggestions'] = $this->requests_model->GetSuggestedArticles($category_id);
			$data['status_count']['suggestions'] = $data['status_count']['suggestions'] + count($data['categories'][$category_id]['suggestions']);
			//requests
			$data['categories'][$category_id]['requests'] = $this->requests_model->GetRequestedArticles($category_id);
			$data['status_count']['requests'] = $data['status_count']['requests'] + count($data['categories'][$category_id]['requests']);
			//published
			$data['categories'][$category_id]['published'] = $this->requests_model->GetPublishedArticles($category_id);
			foreach ($data['categories'][$category_id]['published'] as $key => $published_article)
			{
				$publish_time = strtotime($published_article['publish']);
				if ($publish_time < time())
				{
					$data['status_count']['unpublished'] = $data['status_count']['unpublished'] + 1;
					$data['categories'][$category_id]['published'][$key]['ispublished'] = 0;
				}
				else
				{
					$data['status_count']['published'] = $data['status_count']['published'] + 1;
					$data['categories'][$category_id]['published'][$key]['ispublished'] = 1;
				}
			}
		}

		// Set up the view
		$the_view = $this->frames->view('office/howdoi/office_howdoi_questions', $data);
		
		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function suggestions()
	{
		if (!CheckPermissions('office')) return;

		$this->load->model('howdoi_model','howdoi_model');
		$this->load->model('requests_model','requests_model');
		$howdoi_type_id = $this->howdoi_model->GetHowdoiTypeID();
		
		$this->pages_model->SetPageCode('office_howdoi_suggestion');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('suggestions');
		
		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		$data['categoryparentid'] = $howdoi_type_id;
		$data['categories'] = $this->howdoi_model->GetContentCategories($howdoi_type_id);
		$data['categories'][$howdoi_type_id] = array(
					'codename'=>'unassigned',
					'name'=>'Unassigned',
					'suggestions'=>$this->requests_model->GetSuggestedArticles($howdoi_type_id)
					);
		$data['counts']['suggestions'] = 0;
		foreach ($data['categories'] as $category_id => $category)
		{
			$data['categories'][$category_id]['suggestions'] = $this->requests_model->GetSuggestedArticles($category_id);
			$data['counts']['suggestions'] = $data['counts']['suggestions'] + count($data['categories'][$category_id]['suggestions']);
		}

		// Set up the view
		$the_view = $this->frames->view('office/howdoi/office_howdoi_suggestions', $data);

		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}
	

	function categories()
	{
		if (!CheckPermissions('office')) return;

		$this->load->model('howdoi_model','howdoi_model');

		$this->pages_model->SetPageCode('office_howdoi_categories');
		
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('categories');

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
		
		//do shabazz
		$howdoi_type_id = $this->howdoi_model->GetHowdoiTypeID();

                $data['categories'] = $this->howdoi_model->GetCategoryNames($howdoi_type_id);

		// Set up the view
		$the_view = $this->frames->view('office/howdoi/office_howdoi_categories', $data);

		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}
	

	function editquestion()
	{
		if (!CheckPermissions('office')) return;
		
		$this->pages_model->SetPageCode('office_howdoi_edit_question');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('questions');

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

		// Set up the view
		$the_view = $this->frames->view('office/howdoi/office_howdoi_edit_question', $data);

		// Set up the public frame
		$this->main_frame->SetContent($the_view);

		// Load the public frame view
		$this->main_frame->Load();
	}

	function categorymodify()
	{
		if (!CheckPermissions('office')) return;

		$this->load->model('howdoi_model','howdoi_model');
		$howdoi_type_id = $this->howdoi_model->GetHowdoiTypeID();

		if (isset($_POST['r_submit_edit']))
		{
			// NEW PAGE CATEGORY NEEDED
			$this->pages_model->SetPageCode('office_howdoi_edit_category');
			
			//Get navigation bar and tell it the current page
			$this->_SetupNavbar();
			$this->main_frame->SetPage('categories');
	
			// Insert main text from pages information
			$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
			
			//do shabazz
			$data['category'] = $this->howdoi_model->GetContentCategory($_POST['r_categoryid']);
			$data['category']['id'] = $_POST['r_categoryid'];
	
			// Set up the view
			$this->main_frame->SetTitleParameters(array('category'=>$data['category']['name']));
			$the_view = $this->frames->view('office/howdoi/office_howdoi_edit_category', $data);
	
			// Set up the public frame
			$this->main_frame->SetContent($the_view);
	
			// Load the public frame view
			$this->main_frame->Load();
		}
		else if (isset($_POST['r_submit_delete']))
		{
			$this->howdoi_model->DeleteCategory($_POST['r_categoryid']);
                	$this->main_frame->AddMessage('success','Category deleted.');
			redirect($_POST['r_redirecturl']);
		}
		else if (isset($_POST['r_submit_save']))
		{
			$this->howdoi_model->UpdateCategory($_POST['r_categoryid'],
						$_POST['a_name'],
						$_POST['a_codename'],
						$_POST['a_blurb']
						);
                	$this->main_frame->AddMessage('success',$_POST['a_name'].' has been updated.');
			redirect($_POST['r_redirecturl']);
		}
		else if (isset($_POST['r_submit_add']))
		{
			$this->howdoi_model->AddNewCategory($_POST['a_categoryname']);
                	$this->main_frame->AddMessage('success',$_POST['a_categoryname'].' has been added to the category list.');
			redirect($_POST['r_redirecturl']);
		}
		else if (isset($_POST['r_submit_up']))
		{
			$this->howdoi_model->SwapCategoryOrder($_POST['r_sectionorder'], $_POST['r_sectionorder'] - 1);
                	$this->main_frame->AddMessage('success','Category moved up.');
			redirect($_POST['r_redirecturl']);
		}
		else if (isset($_POST['r_submit_down']))
		{
			$this->howdoi_model->SwapCategoryOrder($_POST['r_sectionorder'], $_POST['r_sectionorder'] + 1);
                	$this->main_frame->AddMessage('success','Category moved down.');
			redirect($_POST['r_redirecturl']);
		}
	}

	function suggestionmodify()
	{
		if (!CheckPermissions('office')) return;

		$this->load->model('howdoi_model','howdoi_model');
		$this->load->model('requests_model','requests_model');
		$howdoi_type_id = $this->howdoi_model->GetHowdoiTypeID();
		
		if (isset($_POST['r_submit_ask']))
		{
                	$article_header_id = $this->requests_model->CreateRequest(
						'suggestion', 
						$_POST['a_category'],
						$_POST['a_question'], 
						$_POST['a_description'], 
						$this->user_auth->entityId
						);
	                $this->main_frame->AddMessage('success','Suggestion Created.');
			redirect($_POST['r_redirecturl']);
		}
	}
}
?>
