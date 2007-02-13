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
		$navbar->AddItem('requests', 'Requests',
				'/office/howdoi/requests');
		$navbar->AddItem('published', 'Published',
				'/office/howdoi/published');
		$navbar->AddItem('categories', 'Categories',
				'/office/howdoi/categories');
	}
	
	function index()
	{
		if (!CheckPermissions('office')) return;

		if ($this->user_auth->officeType == 'Low')
			self::published();
		if ($this->user_auth->officeType == 'High')
			self::suggestions();
		if ($this->user_auth->officeType == 'Admin')
			self::categories();

	}

	function suggestions()
	{
		self::getdata('suggestions');
	}

	function requests()
	{
		self::getdata('requests');
	}

	function published()
	{
		self::getdata('published');
	}

	/// index page.
	function getdata($page)
	{
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('office_howdoi_questions');
		$this->load->model('howdoi_model','howdoi_model');
		$this->load->model('news_model','news_model');
		$this->load->model('article_model','article_model');
		$this->load->model('requests_model','requests_model');
		
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		if ($page == 'suggestions')
			$this->main_frame->SetPage('suggestions');
		if ($page == 'requests')
			$this->main_frame->SetPage('requests');
		if ($page == 'published')
			$this->main_frame->SetPage('published');

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
		if ($page == 'suggestions')
			$the_view = $this->frames->view('office/howdoi/office_howdoi_suggestions', $data);
		if ($page == 'requests')
			$the_view = $this->frames->view('office/howdoi/office_howdoi_requests', $data);
		if ($page == 'published')
			$the_view = $this->frames->view('office/howdoi/office_howdoi_published', $data);
		
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
		//if the article has been pulled then make it go to the article is not a how do i type message.
		if ($data['article']['header']['pulled'] == 1)
		{
                	$correct_content_type = FALSE;
		}
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
						$data['parameters']['revision_id'] = $data['article']['displayrevision']['id'];
					}
					else
					{
						if (isset($data['article']['revisions'][0]))
						{
							$data['article']['displayrevision'] = $this->article_model->GetRevisionContent($article_id, $data['article']['revisions'][0]['id']);
							$data['parameters']['revision_id'] = $data['article']['displayrevision']['id'];
						}
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
		$this->load->model('article_model','article_model');
		$howdoi_type_id = $this->howdoi_model->GetHowdoiTypeID();

		if (isset($_POST['r_submit_save']))
		{
			$revision_id = $this->requests_model->CreateArticleRevision(
				$_POST['r_articleid'],
				$this->user_auth->entityId,
				$_POST['a_question'],
				'',
				'',
				$_POST['a_answer'],
				''
				)
				;
	                $this->main_frame->AddMessage('success','New revision created for article.');
			redirect('/office/howdoi/editquestion/'.$_POST['r_articleid'].'/'.$revision_id.'/');
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
				$this->requests_model->UpdateRequestStatus(
					$_POST['r_articleid'],
					'publish',
					array('content_id'=>$_POST['r_revisionid'],
						'publish_date'=>date('y-m-d H:i:s'),
						'editor'=>$this->user_auth->entityId)
					);
		                $this->main_frame->AddMessage('success','Question had been published.');
				redirect($_POST['r_redirecturl']);
			}
			else if (isset($_POST['r_submit_publishon']))
			{
				$publish = strtotime($_POST['a_publishdate']);
				$publishformat = date('F jS Y', $publish).' at '.date('g.i A', $publish);
				$this->requests_model->UpdateRequestStatus(
					$_POST['r_articleid'],
					'publish',
					array('content_id'=>$_POST['r_revisionid'],
						'publish_date'=>$_POST['a_publishdate'],
						'editor'=>$this->user_auth->entityId)
					);
		                $this->main_frame->AddMessage('success','Question had been set for publication on '.$publishformat.'.');
				redirect($_POST['r_redirecturl']);
			}
			else if (isset($_POST['r_submit_pull']))
			{
				$this->article_model->PullArticle($_POST['r_articleid']);
		                $this->main_frame->AddMessage('success','Question has been pulled from publication.');
				redirect('/office/howdoi');
			}
			else if (isset($_POST['r_submit_category']))
			{
				$this->requests_model->UpdateContentType($_POST['r_articleid'], $_POST['a_category']);
		                $this->main_frame->AddMessage('success','Questions category has been updated.');
				redirect($_POST['r_redirecturl']);
			}
		}
	}
}
?>
