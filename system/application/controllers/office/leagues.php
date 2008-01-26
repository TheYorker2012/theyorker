<?php
/*
 * Leagues office manager
  *This edits leagues, and there order in a section. League edits the contents of a league
 *@author Owen Jones (oj502@york.ac.uk)
 */
class Leagues extends Controller
{
	function __construct()
	{
		parent::Controller();
		$this->load->model('Leagues_model');
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
		$move_item = $this->Leagues_model->getLeagueInformation($id);
		if(empty($move_item))
		{
			$this->messages->AddMessage('error','Invalid league, cannot be re-ordered.');
		}else if($this->Leagues_model->DoesOrderPositionExist($move_item['section_id'],($move_item['order']-1)) == false)
		{
			$this->messages->AddMessage('error','This league cannot be ordered any higher.');
		} else {
			$this->Leagues_model->SwapCategoryOrder($move_item['order'], $move_item['order']-1, $move_item['section_id']);
			$this->messages->AddMessage('success','The league has been moved up.');
		}
		redirect('/office/leagues');
	}
	function movedown($id)
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$move_item = $this->Leagues_model->getLeagueInformation($id);
		if(empty($move_item))
		{
			$this->messages->AddMessage('error','Invalid league, cannot be re-ordered.');
		}else if($this->Leagues_model->DoesOrderPositionExist($move_item['section_id'],($move_item['order']+1)) == false)
		{
			$this->messages->AddMessage('error','This league cannot be ordered any lower.');
		} else {
			$this->Leagues_model->SwapCategoryOrder($move_item['order'], $move_item['order']+1, $move_item['section_id']);
			$this->messages->AddMessage('success','The league has been moved down.');
		}
		redirect('/office/leagues');
	}
	
	function index()
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$this->pages_model->SetPageCode('office_leagues');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$data['leagues'] = $this->Leagues_model->getAllLeagues();
		
		$this->main_frame->SetContentSimple('office/leagues/leagues', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	function create($skip_image='')
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		if (!empty($_SESSION['img'])||$skip_image=='skip'||!empty($_POST['league_add'])) {//Is there an image, or are we skipping getting one?
			//Check for post
			if(!empty($_POST['league_add'])) {
				$codename = $this->_CreateCodeName($_POST['league_name']);
				if($codename==""){
					$this->messages->AddMessage('error','League name must contain some standard letters.');
					$data['league_form']=$_POST;
				}else if($this->Leagues_model->doesCodenameExist($codename) && $codename != $this->Leagues_model->getLeagueCodename($_POST['league_id'])){
					$this->messages->AddMessage('error','This name is already taken, or there is one is too simular to this.');
					$data['league_form']=$_POST;
				}else{
					//create article type
					$image_id=$_POST['league_image_id'];
					$this->Leagues_model->insertLeague($codename,$_POST['league_name'],$_POST['league_type'],$image_id,$_POST['league_size'],0);
					$this->messages->AddMessage('success','New league created.');
					redirect('/office/leagues');
				}
			}
			//Image preview
			if($skip_image=='skip'){
				$data['image_preview']="<b>No image selected.</b>";
				$data['league_form']['league_image_id'] = "";
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
							$data['league_form']['league_image_id'] = "";
						}else{
							//Success image id caught, so preview
							$data['image_preview']= '<img src="/image/puffer/'.$Image['list'].'" alt="Preview" title="Preview">';
							$data['league_form']['league_image_id'] = $Image['list'];
						}
						//Image session no longer needed
						unset($_SESSION['img']);
					}
				}
			}
			$this->pages_model->SetPageCode('office_leagues');
			$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
			$data['league_types'] = $this->Leagues_model->getLeagueContentTypes();
			
			$this->main_frame->SetContentSimple('office/leagues/league_create', $data);
			$this->main_frame->Load();
		}else{
			$this->load->library('image_upload');
			$this->image_upload->automatic('/office/leagues/create', array('puffer'), false, false);
		}
	}
	function edit($id)
	{
		//Get page properties information
		if (!CheckPermissions('editor')) return;
		//Check for post
		if(!empty($_POST['league_edit'])){
			$codename = $this->_CreateCodeName($_POST['league_name']);
			if($codename==""){
				$this->messages->AddMessage('error','League name must contain some standard letters.');
				$data['league_form']=$_POST;
			}else if($this->Leagues_model->doesCodenameExist($codename) && $codename != $this->Leagues_model->getLeagueCodename($_POST['league_id'])){
				$this->messages->AddMessage('error','This name is already taken, or there is one is too simular to this.');
				$data['league_form']=$_POST;
			}else{
				//create league
				$this->Leagues_model->updateLeague($id,$codename,$_POST['league_name'],$_POST['league_type'],$_POST['league_size'],0);
				$this->messages->AddMessage('success','League updated.');
				redirect('/office/leagues');
			}
		}
		
		$league = $this->Leagues_model->getLeagueInformation($id);
		if(empty($league['image_id']))
		{
			$data['has_image']=false;
			$data['image']="No image available.";
		}else{
			$data['has_image']=true;
			$data['image']='<img src="/image/'.$league['image_type'].'/'.$league['image_id'].'" alt="Image Preview" title="Image Preview">';
		}
		//Only get this if there is no post
		if(empty($_POST['league_edit'])){
			$data['league_form'] = array (
				'league_id' => $id,
				'league_name' => $league['name'],
				'league_type' => $league['section_id'],
				'league_size' => $league['size']
			);
		}
		//Get this if we have post or not
		$data['league_types'] = $this->Leagues_model->getLeagueContentTypes();
		$this->pages_model->SetPageCode('office_leagues');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		
		$this->main_frame->SetContentSimple('office/leagues/league_edit', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	//Use image cropper to change an existing puffer image
	function changeimage($id)
	{
		//Get page properties information
		if (!CheckPermissions('editor')) return;
		$this->load->library('image_upload');
		$this->image_upload->automatic('/office/leagues/storechangeimage/'.$id, array('puffer'), false, false);
	}
	
	//Store the id of from the image cropper to change an existing puffer image
	function storechangeimage($id)
	{
		//Get page properties information
		if (!CheckPermissions('editor')) return;
		if(!empty($_SESSION['img'])){
			//There seems to be an image session, try to get id.
			foreach ($_SESSION['img'] as $Image) {
				$image_id='';
				if(empty($Image['list'])){
					//There is no id to use, upload must have failed
					//Clear image session so they can try again
					unset($_SESSION['img']);
					redirect('/office/leagues/changeimage/'.$id);
				}else{
					//Success image id caught, so store
					$this->Leagues_model->updateLeagueImage($id,$Image['list']);
					//redirect back to the edit page where you started
					redirect('/office/leagues/edit/'.$id);
				}
				//Image session no longer needed
				unset($_SESSION['img']);
			}
		}else{
			//session is empty, try getting image again
			redirect('/office/leagues/changeimage/'.$id);
		}
	}
	
	function deleteimage($id)
	{
		//Dont need image id as we are blanking it
		$this->Leagues_model->updateLeagueImage($id,'');
		//redirect back to the edit page where you started
		redirect('/office/leagues/edit/'.$id);
	}
	
	function delete($id, $confirm='')
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$this->pages_model->SetPageCode('office_leagues');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$league = $this->Leagues_model->getLeagueInformation($id);
		$league_size = $this->Leagues_model->GetCurrentSizeOfLeague($id);
		$league_autogenerated = $this->Leagues_model->IsLeagueAutoGenerated($id);
		if(empty($league)){
			$this->messages->AddMessage('error','Invalid league, cannot be deleted.');
			redirect('/office/leagues');
		} else if($league_size > 0){
			$this->messages->AddMessage('error','This league cannot be deleted, it is not empty.');
			redirect('/office/leagues');
		}else if($league_autogenerated == 1){
			$this->messages->AddMessage('error','This league cannot be deleted, it is autogenerated.');
			redirect('/office/leagues');
		}else {
			if($confirm=='confirm'){
				//try and delete
				$this->messages->AddMessage('success','The league has been deleted.');
				$this->Leagues_model->DeleteCategory($id, $league['section_id']);
				redirect('/office/leagues');
			}else{
				//Check there is no articles by this subgroup
				$data['league'] = $league;
				$this->main_frame->SetContentSimple('office/leagues/league_delete', $data);
				// Load the public frame view (which will load the content view)
				$this->main_frame->Load();
			}
		}
	}
}
