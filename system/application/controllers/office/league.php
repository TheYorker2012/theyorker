<?php
/*
 * League office manager
 *This edits the contents of a league, leagues edits leagues.
 *@author Owen Jones (oj502@york.ac.uk)
 */
class League extends Controller
{
	function __construct()
	{
		parent::Controller();
		$this->load->model('Leagues_model');
	}
	
	function moveup($league_id, $venue_id)
	{
		if (!CheckPermissions('editor')) return;

		$move_item = $this->Leagues_model->GetVenuePositionInLeague($league_id, $venue_id);
		if(empty($move_item))
		{
			$this->messages->AddMessage('error','Invalid venue, cannot be re-ordered.');
		}else if($this->Leagues_model->DoesLeaguePositionExist($league_id,($move_item['venue_order']-1)) == false)
		{
			$this->messages->AddMessage('error','This venue cannot be ordered any higher.');
		} else {
			$this->Leagues_model->SwapLeagueOrder($move_item['venue_order'], $move_item['venue_order']-1, $league_id);
			$this->messages->AddMessage('success','The venue has been moved up.');
		}
		redirect('/office/league/edit/'.$league_id);
	}
	function movedown($league_id, $venue_id)
	{
		if (!CheckPermissions('editor')) return;

		$move_item = $this->Leagues_model->GetVenuePositionInLeague($league_id, $venue_id);
		if(empty($move_item))
		{
			$this->messages->AddMessage('error','Invalid venue, cannot be re-ordered.');
		}else if($this->Leagues_model->DoesLeaguePositionExist($league_id,($move_item['venue_order']+1)) == false)
		{
			$this->messages->AddMessage('error','This venue cannot be ordered any lower.');
		} else {
			$this->Leagues_model->SwapLeagueOrder($move_item['venue_order'], $move_item['venue_order']+1, $league_id);
			$this->messages->AddMessage('success','The venue has been moved down.');
		}
		redirect('/office/league/edit/'.$league_id);
	}
	
	function index(){
		redirect('/office/leagues');
	}
	function edit($id)
	{
	
		if (!CheckPermissions('editor')) return;
		
		//Get page properties information
		$this->pages_model->SetPageCode('office_league');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$data['suggestion_information'] = $this->pages_model->GetPropertyWikiText('suggestion_information');
		
		//Check for post
		if(!empty($_POST['venue_add'])){
			$max_venues = $_POST['venue_add_max'];
			for ($index=0, $count=0;$index<$max_venues;$index++)
			{
				if(isset($_POST['venue_add_'.$index]))
				{
					//venue exists, so add it.
					$this->Leagues_model->AddToLeague($id, $_POST['venue_add_'.$index]);
					$count++;
				}
			}
			if ($count>1) {
				$this->messages->AddMessage('success',$count.' venues have been added.');
			} else if ($count=1) {
				$this->messages->AddMessage('success','The venue has been added.');
			}
		}
		
		
		//Get information
		$league = $this->Leagues_model->getLeagueInformation($id);
		$data['venues_limit'] = $league['size'];
		
		$data['venues'] = $this->Leagues_model->GetBasicVenuesFromLeague($id);
		
		$data['suggestions'] = $this->Leagues_model->GetLeagueVenueSuggestions($id);
		$data['league_id'] = $id;
		
		$this->main_frame->SetTitleParameters(array(
				'league_name' => $league['name'],
				'section_name' => $league['section_name']
				));
		$this->main_frame->SetContentSimple('office/leagues/league_contents', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
	function delete($league_id, $venue_id)
	{
		if (!CheckPermissions('editor')) return;
		//Get page properties information
		$this->pages_model->SetPageCode('office_league');
		$data['page_information'] = $this->pages_model->GetPropertyWikiText('page_information');
		$venue_position = $this->Leagues_model->GetVenuePositionInLeague($league_id, $venue_id);
		if(empty($venue_position)){
			$this->messages->AddMessage('error','Invalid venue, cannot be deleted.');
			redirect('/office/league/edit/'.$league_id);
		} else {
				//try and delete
				$this->messages->AddMessage('success','The venue has been deleted.');
				$this->Leagues_model->RemoveFromLeague($league_id, $venue_id);
				redirect('/office/league/edit/'.$league_id);
		}
	}
}