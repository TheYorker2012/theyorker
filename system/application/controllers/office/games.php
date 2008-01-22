<?php
/**
 *  @file games.php
 *  @author David Garbett (dg516)
 *  Games in Office Controller
 */

class Games extends Controller
{
	/// Constructor
	function Games()
	{
		parent::Controller();
		/// Always need db access
		$this->load->model('games_model');
	}
	
	function index()
	{
		/// use remapping instead?
		$this->glist(0);			
	}
	
	
	function glist($number = 0)
	{

		if (!CheckPermissions('office')) return;

		$this->load->library('xajax');
		$this->xajax->registerFunction(array("toggle_activation", &$this, "_toggle_activation"));
		$this->xajax->registerFunction(array("del_game", &$this, "_del_game"));

		$this->xajax->processRequests();

		$this->pages_model->SetPageCode('office_games_list');
		$data['section_games_list_actions_title'] =
				$this->pages_model->GetPropertyText('section_games_list_actions_title');
		$data['section_games_list_page_info_text'] =
				$this->pages_model->GetPropertyWikiText('section_games_list_page_info_text');
		$data['section_games_list_page_info_title'] =
				$this->pages_model->GetPropertyText('section_games_list_page_info_title');
		

		/// Pagination
		$this->load->library('pagination');
		$config['base_url'] = base_url().'office/games/glist/';
		$config['total_rows'] = $this->games_model->GetCount();
		$config['per_page'] = 50;
		$config['num_links'] = 2;
		$config['uri_segment'] = 4;
		$config['full_tag_open'] = '<div class="Pagination">';
		$config['full_tag_close'] = '</div>';
		$config['first_tag_open'] = '<span>';
		$config['first_tag_close'] = '</span>';
		$config['last_tag_open'] = '<span>';
		$config['last_tag_close'] = '</span>';
		$config['next_tag_open'] = '<span>';
		$config['next_tag_close'] = '</span>';
		$config['prev_tag_open'] = '<span>';
		$config['prev_tag_close'] = '</span>';
		$config['cur_tag_open'] = '<span class="selected">';
		$config['cur_tag_close'] = '</span>';
		$config['num_tag_open'] = '<span>';
		$config['num_tag_close'] = '</span>';
		$this->pagination->initialize($config);

		$data['offset'] = $number;
		$data['per_page'] = $config['per_page'];
		$data['total'] = $config['total_rows'];
				
		$data['games'] = $this->games_model->GetFullList($data['offset'],$config['per_page']);
		
		if($number == 0){
			$data['incomplete_games'] = $this->games_model->Get_Incomplete();
		}else{ $data['incomplete_games'] = 0; }
		
		
		$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));
		
		$this->main_frame->SetContentSimple('office/games/list',$data);
		$this->main_frame->Load();
		

	}	
	
		function _toggle_activation($game_id)
		{
			$result = $this->games_model->Edit_Game_Get($game_id);
			$activation_state = $this->games_model->toggle_activation($game_id);
			$objResponse = new xajaxResponse();
			$objResponse->addAssign(
				"activation_".$game_id,
				"src",
				($activation_state ?
					'/images/prototype/prefs/success.gif' :
					'/images/prototype/news/delete.gif'));
			return $objResponse;
		}
	
		function _del_game($game_id)
		{
			$objResponse = new xajaxResponse();
			if($this->games_model->Del_Game($game_id))
				{
					/** @TODO: delete file */
					$objResponse->addAssign("row_".$game_id,"style.display","NONE");
				}
			return $objResponse;
		}
		
		function Add()
		{
			if(!isset($_FILES['new_game_file_field']))
			{
				redirect('office/games');
			}
			if (!CheckPermissions('office')) return;
			
			///Add file uploading bit ere
			
			$game_id = 1; /// Temp, should be assigned new number
			
			$this->main_frame->AddMessage('success','File Uploaded Successfully. Please complete the rest of the required information below.');
			redirect('office/games/edit/'.$game_id);
		}

		function edit($game_id =-1)
		{
			if ($game_id==-1)
			{
				redirect('office/games');
			}			
			
			if (!CheckPermissions('office')) return;

						
			$this->pages_model->SetPageCode('office_games_edit');
			$this->load->library('image');

			$data['section_games_edit_page_info_title'] = 
					$this->pages_model->GetPropertyText('section_games_edit_page_info_title');
			$data['section_games_edit_page_info_text'] = 
					$this->pages_model->GetPropertyWikiText('section_games_edit_page_info_text');
			
			if (
				isset($_POST['game_title_field']) &&
				isset($_POST['game_width_field']) &&
				isset($_POST['game_height_field']))
			{
				if($this->games_model->Edit_Game_Update(
						$game_id,
						$_POST['game_title_field'],
						$_POST['game_width_field'],
						$_POST['game_height_field'],
						isset($_POST['game_activated_field'])))
				{
					$this->main_frame->AddMessage('success','Changes saved!',FALSE);
				} else {
					$this->main_frame->AddMessage('error','Update failed!',FALSE);
				}
			}
			
			$data['game'] = $this->games_model->Edit_Game_Get($game_id);
			$data['game']['pathname'] = $this->config->item('static_web_address').'/games/'.$data['game']['filename'];
			$data['game']['image'] = $this->image->getImage(
								$data['game']['image_id'],
								'gamethumb',
								array('title' => $data['game']['title']));
			$data['game_id'] = $game_id;
			
			$this->main_frame->SetContentSimple('office/games/edit',$data);
			$this->main_frame->Load();
		}

		function changeimage($game_id)
		{
			if (!CheckPermissions('editor')) return;
			$this->load->library('image_upload');
			$this->image_upload->automatic(
				'/office/games/storechangedimage/'.$game_id, 
				array('gamethumb'),
				false,
				false);
		}
		function storechangedimage($game_id)
		{
			if (!CheckPermissions('office')) return;
			if(!empty($_SESSION['img'])){
				foreach ($_SESSION['img'] as $Image) {
					$image_id='';
					if(empty($Image['list'])){
						//There is no id to use, upload must have failed
						//Clear image session so they can try again
						unset($_SESSION['img']);
						redirect('/office/games/changeimage/'.$game_id);
					}else{
						$this->load->library('image');
						$this->image->delete('image',$this->games_model->Get_Image_Id($game_id));
						$this->games_model->Set_Image_Id($game_id,$Image['list']);
						//redirect back to the edit page where you started
						redirect('/office/games/edit/'.$game_id);
					}
					//Image session no longer needed
					unset($_SESSION['img']);
				}
			}else{
				//session is empty, try getting image again
				redirect('/office/games/changeimage/'.$game_id);
			}
		}

	}
		
?>