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
			//unpublished
			$data['categories'][$category_id]['unpublished'] = $this->requests_model->GetPublishedArticles($category_id, FALSE);
			$data['status_count']['unpublished'] = $data['status_count']['unpublished'] + count($data['categories'][$category_id]['unpublished']);
			//published
			$data['categories'][$category_id]['published'] = $this->requests_model->GetPublishedArticles($category_id, TRUE);
			$data['status_count']['published'] = $data['status_count']['published'] + count($data['categories'][$category_id]['published']);
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

	function questionedit($article_id, $revision_id)
	{
		if (!CheckPermissions('office')) return;

		$this->load->model('howdoi_model','howdoi_model');
		$this->load->model('requests_model','requests_model');
		$this->load->model('article_model','article_model');
		$howdoi_type_id = $this->howdoi_model->GetHowdoiTypeID();
		
		$this->pages_model->SetPageCode('office_howdoi_edit_question');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('questions');

		// Insert main text from pages information

		$data['parameters']['article_id'] = $article_id;
		$data['parameters']['revision_id'] = $revision_id;

		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

                $data['categories'] = $this->howdoi_model->GetCategoryNames($howdoi_type_id);
		$data['categories'][$howdoi_type_id] = array(
					'codename'=>'unassigned',
					'name'=>'Unassigned',
					);
		$data['article']['header'] = $this->article_model->GetArticleHeader($article_id);
		$correct_content_type = FALSE;
		foreach ($data['categories'] as $category_id => $category)
		{
                	if ($data['article']['header']['content_type'] == $category_id)
			$correct_content_type = TRUE;
		}
		//echo $correct_content_type;
		if ($correct_content_type == TRUE)
		{
			$data['article']['revisions'] = $this->requests_model->GetArticleRevisions($article_id);
			$data['article']['displayrevision'] = FALSE;
			//suggestions have no contents associated with them
			if ($data['article']['header']['suggestion_accepted'] == 1)
			{
				if ($revision_id == -1) //pick default revision
				{
					if ($data['article']['header']['live_content'] != FALSE) //request
					{
						$data['article']['displayrevision'] = $this->article_model->GetRevisionContent($article_id, $data['article']['header']['live_content']);
					}
					else
					{
						if (isset($data['article']['revisions'][0]))
							$data['article']['displayrevision'] = $this->article_model->GetRevisionContent($article_id, $data['article']['revisions'][0]['id']);
					}
				}
				else //pick selected revision
				{
					$data['article']['displayrevision'] = $this->article_model->GetRevisionContent($article_id, $revision_id);
					if ($data['article']['displayrevision'] == FALSE)
					{
	                			$this->main_frame->AddMessage('error','Specified revision doesn\'t exist for this question. Default selected.');
	                			redirect('/office/howdoi/editquestion/'.$article_id.'/');
	    				}
				}
			}

			$data['user']['id'] = $this->user_auth->entityId;
			$data['user']['officetype'] = $this->user_auth->officeType;
	
			// Set up the view
			$the_view = $this->frames->view('office/howdoi/office_howdoi_edit_question', $data);

			// Set up the public frame
			$this->main_frame->SetContent($the_view);
	
			// Load the public frame view
			$this->main_frame->Load();
		}
		else
		{
                	$this->main_frame->AddMessage('error','Specified article is not a How Do I question.');
                	redirect('/office/howdoi/');
		}
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
						$this->user_auth->entityId,
						NULL);
	                $this->main_frame->AddMessage('success','Suggestion Created.');
			redirect($_POST['r_redirecturl']);
		}
	}
	
	function questionmodify()
	{
		if (!CheckPermissions('office')) return;

		$this->load->model('howdoi_model','howdoi_model');
		$this->load->model('requests_model','requests_model');
		$howdoi_type_id = $this->howdoi_model->GetHowdoiTypeID();

		if (isset($_POST['r_submit_save']))
		{
			echo '<pre>';
			echo 'save';
			echo print_r($_POST);
			echo '</pre>';
		}
		//for forms that are editor only
		if ($this->user_auth->officeType != 'Low')
		{
			if (isset($_POST['r_submit_modify']))
			{
				$this->requests_model->UpdateSuggestion(
					$_POST['r_articleid'], 
					array('title'=>$_POST['a_title'],
						'description'=>$_POST['a_description'],
						'content_type'=>$_POST['a_category'])
					);
		                $this->main_frame->AddMessage('success',$_POST['r_status'].' has been modified.');
				redirect($_POST['r_redirecturl']);
			}
			else if (isset($_POST['r_submit_accept']))
			{
				$this->requests_model->UpdateRequestStatus(
					$_POST['r_articleid'],
					'request',
					array('editor'=>$this->user_auth->entityId,
						'publish_date'=>$_POST['a_deadline'],
						'title'=>$_POST['a_title'],
						'description'=>$_POST['a_description'],
						'content_type'=>$_POST['a_category'])
					);
		                $this->main_frame->AddMessage('success','Suggestion has been updated to a request.');
				redirect($_POST['r_redirecturl']);
			}
			else if (isset($_POST['r_submit_reject']))
			{
				$this->requests_model->RejectSuggestion($_POST['r_articleid']);
		                $this->main_frame->AddMessage('success','Suggestion has been rejected.');
				redirect('/office/howdoi');
			}
			else if (isset($_POST['r_submit_publishnow']))
			{
				echo '<pre>';
				echo 'publish now';
				echo print_r($_POST);
				echo '</pre>';
			}
			else if (isset($_POST['r_submit_publishon']))
			{
				echo '<pre>';
				echo 'publish on';
				echo print_r($_POST);
				echo '</pre>';
			}
			else if (isset($_POST['r_submit_pull']))
			{
				echo '<pre>';
				echo 'pull';
				echo print_r($_POST);
				echo '</pre>';
			}
		}
	}
}
?>
