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
		$data['sub_articles'] = $this->Article_model->getAllSubArticleTypes();
		$data['main_articles'] = $this->Article_model->getMainArticleTypes();
		
		if(empty($_POST) || !empty($_POST['article_type_add']))
		{
			if(!empty($_POST['article_type_add'])){
				$codename = $this->_CreateCodeName($_POST['article_type_name']);
				if($codename==""){
					$this->messages->AddMessage('error','Article type name must contain some standard letters.');
					$data['article_type_form']=$_POST;
				}else if($this->Article_model->doesCodenameExist($codename)){
					$this->messages->AddMessage('error','This name is already taken, or there is one is too simular to this.');
					$data['article_type_form']=$_POST;
				}else if($this->Article_model->isArticleTypeAParent($_POST['article_type_parent'])==false){
					$this->messages->AddMessage('error','The selected parent article type, cannot have children.');
				}else{
					//@NOTE no image or order support at the moment!
					//create article type
					$this->Article_model->insertArticleSubType($codename,$_POST['article_type_name'],$_POST['article_type_parent'],NULL,$_POST['article_type_archive'],$_POST['article_type_blurb']);
					$this->messages->AddMessage('success','New article type created.');
				}
			}
			$this->main_frame->SetContentSimple('office/articles/sub_article_types', $data);
			
		} else if(!empty($_POST['article_type_edit'])){
			$codename = $this->_CreateCodeName($_POST['article_type_name']);
			if($codename==""){
				$this->messages->AddMessage('error','Article type name must contain some standard letters.');
				$data['article_type_form']=$_POST;
				redirect('/office/articletypes/edit/'.$_POST['article_type_id']);
			}else if($this->Article_model->doesCodenameExist($codename) && $codename != $this->Article_model->getArticleTypeCodename($_POST['article_type_id'])){
				$this->messages->AddMessage('error','This name is already taken, or there is one is too simular to this"'.$codename.'" "'.$this->News_model->getArticleTypeCodename($_POST['article_type_id']).'".');
				$data['article_type_form']=$_POST;
				redirect('/office/articletypes/edit/'.$_POST['article_type_id']);
			}else if($this->Article_model->isArticleTypeAParent($_POST['article_type_parent'])==false){
				$this->messages->AddMessage('error','The selected parent article type, cannot have children.');
				redirect('/office/articletypes/edit/'.$_POST['article_type_id']);
			}else{
				//@NOTE no image or order support at the moment!
				//create article type
				$this->Article_model->updateArticleSubType($_POST['article_type_id'],$codename,$_POST['article_type_name'],$_POST['article_type_parent'],NULL,$_POST['article_type_archive'],$_POST['article_type_blurb']);
				$this->messages->AddMessage('success','Article type updated.');
				$this->main_frame->SetContentSimple('office/articles/sub_article_types', $data);
			}
		} else if(!empty($_POST['article_type_delete'])){
			
		}
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	
	function edit($id)
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$this->pages_model->SetPageCode('office_articletypes_edit');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		
		$data['main_articles'] = $this->Article_model->getMainArticleTypes();
		$articletype = $this->Article_model->getSubArticleType($id);
		$data['article_type_form'] = array (
			'article_type_id' => $id,
			'article_type_name' => $articletype['name'],
			'article_type_parent' => $articletype['parent_id'],
			'article_type_archive' => $articletype['archive'],
			'article_type_blurb' => $articletype['blurb']
		);
		
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