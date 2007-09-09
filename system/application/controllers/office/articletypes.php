<?php
/*
 * Office article type manager
 *@author Owen Jones (oj502@york.ac.uk)
 */
class Articletypes extends Controller
{
	function __construct()
	{
		parent::Controller();
		$this->load->model('Article_model');
		$this->load->model('News_model');
	}
	
	private function _CreateCodeName($long_name)
	{
		//strip non alpha-numerical symbols
		$new_string = strtolower(preg_replace("/[^a-zA-Z0-9s]/", "", $long_name));
		//replace spaces with an underscore
		return str_replace(" ", "", $new_string);
	}
	
	function index()
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$this->pages_model->SetPageCode('office_articletypes');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		
		if(!empty($_POST))
		{
			//Check for post editing/deleting/updating
			if(!empty($_POST['article_type_add'])){
				$codename = $this->_CreateCodeName($_POST['article_type_name']);
				
				if($codename==""){
					//error
					$this->messages->AddMessage('error','Article type name must contain some standard letters.');
					$data['article_type_form']=$_POST;
				}else if($this->Article_model->doesCodenameExist($codename)){
					//error
					$this->messages->AddMessage('error','This name is already taken, or there is one is too simular to this.');
					$data['article_type_form']=$_POST;
				}else if($this->Article_model->isArticleTypeAParent($_POST['article_type_parent'])==false){
					//error
					$this->messages->AddMessage('error','The selected parent article type, cannot have children.');
				}else{
					//create article type
					//@NOTE no image or order support at the moment!
					$this->Article_model->insertArticleSubType($codename,$_POST['article_type_name'],$_POST['article_type_parent'],NULL,$_POST['article_type_archive'],$_POST['article_type_blurb']);
					$this->messages->AddMessage('success','New article type created.');
				}
			}
			if(!empty($_POST['article_type_edit'])){
				//make sure name change isnt taken
				//make codename
				//make sure parent exists and can have children
			}
			if(!empty($_POST['article_type_delete'])){
			}
		}
		//Load sub article data for table
		$data['sub_articles'] = $this->Article_model->getAllSubArticleTypes();
		$data['main_articles'] = $this->Article_model->getMainArticleTypes();
		
		$this->main_frame->SetContentSimple('office/articles/sub_article_types', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	function edit($id)
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$this->pages_model->SetPageCode('office_articletypes_edit');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		
		$this->main_frame->SetContentSimple('office/articles/sub_article_edit', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	function delete($id)
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$this->pages_model->SetPageCode('office_articletypes_del');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		
		//Check there is no articles by this subgroup
		//Check has no children
		$this->main_frame->SetContentSimple('office/articles/sub_article_delete', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}