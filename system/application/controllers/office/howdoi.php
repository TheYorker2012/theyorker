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
		
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('questions');

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');
		$data['status_count'] = array('awaiting_publication'=>0,
						'set_for_publication'=>0,
						'published'=>0
						);

		//do shabazz
		$data['questionheader'] = $this->article_model->GetArticleHeader(13);
		$data['categories'] = $this->howdoi_model->GetContentCategories(10);
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
		
		$this->pages_model->SetPageCode('office_howdoi_suggestions');

		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('suggestions');
		
		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

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
		
		$this->pages_model->SetPageCode('office_howdoi_categories');
		
		//Get navigation bar and tell it the current page
		$this->_SetupNavbar();
		$this->main_frame->SetPage('categories');

		// Insert main text from pages information
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('main_text');

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

}
?>
