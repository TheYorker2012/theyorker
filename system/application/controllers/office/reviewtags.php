<?php
/*
 * Review tags office manager
  *This manages tags and tag groups in the review sections.
 *@author Owen Jones (oj502@york.ac.uk)
 */
class Reviewtags extends Controller
{
	function __construct()
	{
		parent::Controller();
		$this->load->model('Tags_model');
	}
	
	function moveup($id)
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$move_item = $this->Tags_model->GetTag($id);
		if(empty($move_item))
		{
			$this->messages->AddMessage('error','Invalid tag, cannot be re-ordered.');
		}else if($this->Tags_model->DoesOrderPositionExist($move_item['tag_group_id'],($move_item['tag_order']-1)) == false)
		{
			$this->messages->AddMessage('error','This tag cannot be ordered any higher.');
		} else {
			$this->Tags_model->SwapTagOrder($move_item['tag_order'], $move_item['tag_order']-1, $move_item['tag_group_id']);
			$this->messages->AddMessage('success','The tag has been moved up.');
		}
		redirect('/office/reviewtags');
	}
	function movegroupup($id)
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$move_item = $this->Tags_model->GetTagGroup($id);
		if(empty($move_item))
		{
			$this->messages->AddMessage('error','Invalid tag group, cannot be re-ordered.');
		}else if($this->Tags_model->DoesGroupOrderPositionExist($move_item['content_type_id'],($move_item['tag_group_order']-1)) == false)
		{
			$this->messages->AddMessage('error','This tag group cannot be ordered any higher.');
		} else {
			$this->Tags_model->SwapTagGroupOrder($move_item['tag_group_order'], $move_item['tag_group_order']-1, $move_item['content_type_id']);
			$this->messages->AddMessage('success','The tag group has been moved up.');
		}
		redirect('/office/reviewtags');
	}
	function movedown($id)
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$move_item = $this->Tags_model->GetTag($id);
		if(empty($move_item))
		{
			$this->messages->AddMessage('error','Invalid tag, cannot be re-ordered.');
		}else if($this->Tags_model->DoesOrderPositionExist($move_item['tag_group_id'],($move_item['tag_order']+1)) == false)
		{
			$this->messages->AddMessage('error','This tag cannot be ordered any lower.');
		} else {
			$this->Tags_model->SwapTagOrder($move_item['tag_order'], $move_item['tag_order']+1, $move_item['tag_group_id']);
			$this->messages->AddMessage('success','The tag has been moved down.');
		}
		redirect('/office/reviewtags');
	}

	function movegroupdown($id)
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$move_item = $this->Tags_model->GetTagGroup($id);
		if(empty($move_item))
		{
			$this->messages->AddMessage('error','Invalid tag group, cannot be re-ordered.');
		}else if($this->Tags_model->DoesGroupOrderPositionExist($move_item['content_type_id'],($move_item['tag_group_order']+1)) == false)
		{
			$this->messages->AddMessage('error','This tag group cannot be ordered any lower.');
		} else {
			$this->Tags_model->SwapTagGroupOrder($move_item['tag_group_order'], $move_item['tag_group_order']+1, $move_item['content_type_id']);
			$this->messages->AddMessage('success','The tag group has been moved down.');
		}
		redirect('/office/reviewtags');
	}
	
	function index()
	{
		if (!CheckPermissions('editor')) return;
		//check for post
		if(!empty($_POST['tag_add'])) {
			if(empty($_POST['tag_name'])){
				$this->messages->AddMessage('error','The tag cannot be blank.');
				$data['tag_form']=$_POST;
			}else{
				//create tag
				$this->Tags_model->AddTag($_POST['tag_name'],$_POST['tag_group_id']);
				$this->messages->AddMessage('success','New tag created.');
			}
		}
		if(!empty($_POST['tag_group_add'])) {
			if(empty($_POST['tag_group_name'])){
				$this->messages->AddMessage('error','The tag group cannot be blank.');
				$data['tag_group_form']=$_POST;
			}else{
				//create tag
				if(empty($_POST['tag_group_ordered'])){$ordered=0;}else{$ordered=1;}
				$this->Tags_model->AddTagGroup($_POST['tag_group_name'],$_POST['content_type_id'],$ordered);
				$this->messages->AddMessage('success','New tag group created.');
			}
		}
		//Get page properties information
		$this->pages_model->SetPageCode('office_reviewtags');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$data['tags'] = $this->Tags_model->GetAllTags();
		$data['tag_groups'] = $this->Tags_model->GetAllTagGroups();
		$data['group_types'] = $this->Tags_model->GetAllReviewContentTypes();
		
		$this->main_frame->SetContentSimple('office/reviews/tags', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	
	function edit($id)
	{
		//Get page properties information
		if (!CheckPermissions('editor')) return;
		//Check for post
		if(!empty($_POST['tag_edit'])) {
			if(empty($_POST['tag_name'])){
				$this->messages->AddMessage('error','The tag cannot be blank.');
				$data['tag_form']=$_POST;
			}else{
				//create tag
				$this->Tags_model->UpdateTag($id,$_POST['tag_name'],$_POST['tag_group_id']);
				$this->messages->AddMessage('success','Tag updated.');
				redirect('/office/reviewtags');
			}
		} else {
			//Only get this if there is no post
			$data['tag_form'] = $this->Tags_model->GetTag($id);
		}
		//Get this if we have post or not
		$this->pages_model->SetPageCode('office_reviewtags');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$data['tag_groups'] = $this->Tags_model->GetAllTagGroups();
		
		$this->main_frame->SetContentSimple('office/reviews/tag_edit', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	
	function editgroup($id)
	{
		//Get page properties information
		if (!CheckPermissions('editor')) return;
		//Check for post
		if(!empty($_POST['tag_group_edit'])) {
			if(empty($_POST['tag_group_name'])){
				$this->messages->AddMessage('error','The tag group cannot be blank.');
				$data['tag_group_form']=$_POST;
			}else{
				//create tag
				if(empty($_POST['tag_group_ordered'])){$ordered=0;}else{$ordered=1;}
				$this->Tags_model->UpdateTagGroup($id, $_POST['tag_group_name'],$_POST['content_type_id'],$ordered);
				$this->messages->AddMessage('success','Tag group updated.');
				redirect('/office/reviewtags');
			}
		} else {
			//Only get this if there is no post
			$data['tag_group_form'] = $this->Tags_model->GetTagGroup($id);
		}
		//Get this if we have post or not
		$this->pages_model->SetPageCode('office_reviewtags');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$data['group_types'] = $this->Tags_model->GetAllReviewContentTypes();
		
		$this->main_frame->SetContentSimple('office/reviews/tag_group_edit', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	
	function delete($id, $confirm='')
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$this->pages_model->SetPageCode('office_reviewtags');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$tag = $this->Tags_model->GetTag($id);
		if(empty($tag)){
			$this->messages->AddMessage('error','Invalid tag, cannot be deleted.');
			redirect('/office/reviewtags');
		} else if($this->Tags_model->IsTagInUse($id)){
			$this->messages->AddMessage('error','Tag cannot be deleted, it is in use by one or more reviewed organisations.');
			redirect('/office/reviewtags');
		}else {
			if($confirm=='confirm'){
				//try and delete
				$this->messages->AddMessage('success','The tag has been deleted.');
				$this->Tags_model->RemoveTagFromGroup($tag['tag_group_id'], $id);
				redirect('/office/reviewtags');
			}else{
				$data['tag'] = $tag;
				$this->main_frame->SetContentSimple('office/reviews/tag_delete', $data);
				// Load the public frame view (which will load the content view)
				$this->main_frame->Load();
			}
		}
	}
	function deletegroup($id, $confirm='')
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$this->pages_model->SetPageCode('office_reviewtags');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$tag_group = $this->Tags_model->GetTagGroup($id);
		
		$group_empty = $this->Tags_model->IsTagGroupEmpty($id);
		if(empty($tag_group)){
			$this->messages->AddMessage('error','Invalid tag group, cannot be deleted.');
			redirect('/office/reviewtags');
		} else if($group_empty==false){
			$this->messages->AddMessage('error','This tag group cannot be deleted, it is not empty.');
			redirect('/office/reviewtags');
		}else {
			if($confirm=='confirm'){
				//try and delete
				$this->messages->AddMessage('success','The tag_group has been deleted.');
				$this->Tags_model->RemoveTagGroup($tag_group['content_type_id'], $id);
				redirect('/office/reviewtags');
			}else{
				$data['tag_group'] = $tag_group;
				$this->main_frame->SetContentSimple('office/reviews/tag_group_delete', $data);
				// Load the public frame view (which will load the content view)
				$this->main_frame->Load();
			}
		}
	}
}
