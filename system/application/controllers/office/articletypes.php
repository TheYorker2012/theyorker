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
	
	function moveup($id)
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$move_item = $this->Article_model->getSubArticleType($id);
		if(empty($move_item))
		{
			$this->messages->AddMessage('error','Invalid sub article type, cannot be re-ordered.');
		}else if($this->Article_model->DoesOrderPositionExist($move_item['parent_id'],($move_item['section_order']-1)) == false)
		{
			$this->messages->AddMessage('error','This article type cannot be ordered any higher.');
		} else {
			$this->Article_model->SwapCategoryOrder($move_item['section_order'], $move_item['section_order']-1, $move_item['parent_id']);
			$this->messages->AddMessage('success','The article type has been moved up.');
		}
		redirect('/office/articletypes');
	}
	function movedown($id)
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$move_item = $this->Article_model->getSubArticleType($id);
		if(empty($move_item))
		{
			$this->messages->AddMessage('error','Invalid sub article type, cannot be re-ordered.');
		}else if($this->Article_model->DoesOrderPositionExist($move_item['parent_id'],($move_item['section_order']+1)) == false)
		{
			$this->messages->AddMessage('error','This article type cannot be ordered any lower.');
		} else {
			$this->Article_model->SwapCategoryOrder($move_item['section_order'], $move_item['section_order']+1, $move_item['parent_id']);
			$this->messages->AddMessage('success','The article type has been moved down.');
		}
		redirect('/office/articletypes');
	}
	
	function index()
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$this->pages_model->SetPageCode('office_articletypes');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$data['sub_articles'] = $this->Article_model->getAllSubArticleTypes();
		
		$this->main_frame->SetContentSimple('office/articles/sub_article_types', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	function create($skip_image='')
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		if (!empty($_SESSION['img'])||$skip_image=='skip'||!empty($_POST['article_type_add'])) {//Is there an image, or are we skipping getting one?
			//Check for post
			if(!empty($_POST['article_type_add'])) {
				$codename = $this->_CreateCodeName($_POST['article_type_name']);
				if($codename==""){
					$this->messages->AddMessage('error','Article type name must contain some standard letters.');
					$data['article_type_form']=$_POST;
				}else if($this->Article_model->doesCodenameExist($codename)){
					$this->messages->AddMessage('error','This name is already taken, or there is one is too simular to this.');
					$data['article_type_form']=$_POST;
				}else if($this->Article_model->isArticleTypeAParent($_POST['article_type_parent'])==false){
					$this->messages->AddMessage('error','The selected parent article type, cannot have children.');
					$data['article_type_form']=$_POST;
				}else{
					//create article type
					$image_id=$_POST['article_type_image_id'];
					if(empty($_POST['article_type_archive'])){$archive=0;}else{$archive=1;}
					$this->Article_model->insertArticleSubType($codename,$_POST['article_type_name'],$_POST['article_type_parent'],$image_id,$archive,$_POST['article_type_blurb']);
					$this->messages->AddMessage('success','New article type created.');
					redirect('/office/articletypes');
				}
			}
			//Image preview
			if($skip_image=='skip'){
				$data['image_preview']="<b>No image selected.</b>";
				$data['article_type_form']['article_type_image_id'] = "";
			}else{
				if(!empty($_SESSION['img'])){
					//There seems to be an image session, try to get id.
					foreach ($_SESSION['img'] as $Image) {
						$image_id='';
						if(empty($Image['list'])){
							//There is no id to use, upload must have failed
							//Clear image session so they can try again
							unset($_SESSION['img']);
							$data['image_preview']="<b>No image selected.</b>";
							$data['article_type_form']['article_type_image_id'] = "";
						}else{
							//Success image id caught, so preview
							$data['image_preview']= '<img src="/image/puffer/'.$Image['list'].'" alt="Preview" title="Preview">';
							$data['article_type_form']['article_type_image_id'] = $Image['list'];
						}
						//Image session no longer needed
						unset($_SESSION['img']);
					}
				}
			}
			$this->pages_model->SetPageCode('office_articletypes');
			$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
			$data['main_articles'] = $this->Article_model->getMainArticleTypes();
			
			$this->main_frame->SetContentSimple('office/articles/sub_article_create', $data);
			$this->main_frame->Load();
		}else{
			$this->load->library('image_upload');
			$this->image_upload->automatic('/office/articles/create', array('puffer'), false, false);
		}
	}
	function edit($id)
	{
		//Get page properties information
		if (!CheckPermissions('editor')) return;
		//Check for post
		if(!empty($_POST['article_type_edit'])){
			$codename = $this->_CreateCodeName($_POST['article_type_name']);
			if($codename==""){
				$this->messages->AddMessage('error','Article type name must contain some standard letters.');
				$data['article_type_form']=$_POST;
			}else if($this->Article_model->doesCodenameExist($codename) && $codename != $this->Article_model->getArticleTypeCodename($_POST['article_type_id'])){
				$this->messages->AddMessage('error','This name is already taken, or there is one is too simular to this.');
				$data['article_type_form']=$_POST;
			}else if($this->Article_model->isArticleTypeAParent($_POST['article_type_parent'])==false){
				$this->messages->AddMessage('error','The selected parent article type, cannot have children.');
			}else{
				//@NOTE no image support at the moment!
				//create article type
				$image_id = NULL;
				$this->Article_model->updateArticleSubType($_POST['article_type_id'],$codename,$_POST['article_type_name'],$_POST['article_type_parent'],$image_id,$_POST['article_type_archive'],$_POST['article_type_blurb']);
				$this->messages->AddMessage('success','Article type updated.');
				redirect('/office/articletypes');
			}
		}
		
		$articletype = $this->Article_model->getSubArticleType($id);
		if(empty($articletype['image_id']))
		{
			$data['image']="No image available.";
		}else{
			$data['image']='<img src="/image/'.$articletype['image_type_codename'].'/'.$articletype['image_id'].'" alt="'.$articletype['image_title'].'" title="'.$articletype['image_title'].'">';
		}
		//Only get this if there is no post
		if(empty($_POST['article_type_edit'])){
			$data['article_type_form'] = array (
				'article_type_id' => $id,
				'article_type_name' => $articletype['name'],
				'article_type_parent' => $articletype['parent_id'],
				'article_type_archive' => $articletype['archive'],
				'article_type_blurb' => $articletype['blurb']
			);
		}
		//Get this if we have post or not
		$data['main_articles'] = $this->Article_model->getMainArticleTypes();
		$this->pages_model->SetPageCode('office_articletypes_edit');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		
		$this->main_frame->SetContentSimple('office/articles/sub_article_edit', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	function delete($id, $confirm='')
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$this->pages_model->SetPageCode('office_articletypes_del');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$article_type = $this->Article_model->getSubArticleType($id);
		$article_in_this_type = $this->News_model->GetLatestId($article_type['codename'],1);
		if(empty($article_type)){
			$this->messages->AddMessage('error','Invalid article type, cannot be deleted.');
			redirect('/office/articletypes');
		} else if($article_type['has_children']){
			$this->messages->AddMessage('error','This article type cannot be deleted, it has children.');
			redirect('/office/articletypes');
		}else if (!empty($article_in_this_type)){//check for articles in this group
			$this->messages->AddMessage('error','This article type cannot be deleted, it has articles in it.');
			redirect('/office/articletypes');
		} else {
			if($confirm=='confirm'){
				//try and delete
				$this->messages->AddMessage('success','The sub article type has been deleted.');
				$this->Article_model->DeleteCategory($id, $article_type['parent_id']);
				redirect('/office/articletypes');
			}else{
				//Check there is no articles by this subgroup
				$data['article_type'] = $article_type;
				$data['article_type']['id'] = $id;
				$data['parent_article_type'] = $this->Article_model->getSubArticleType($article_type['parent_id']);
				$this->main_frame->SetContentSimple('office/articles/sub_article_delete', $data);
				// Load the public frame view (which will load the content view)
				$this->main_frame->Load();
			}
		}
	}
}