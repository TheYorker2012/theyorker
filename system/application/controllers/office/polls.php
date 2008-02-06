<?php
/**
 *  @file polls.php
 *  @author Richard Ingle (ri504)
 *  Polls in Office Controller
 */

class Polls extends Controller
{
	/// Constructor
	function Polls()
	{
		parent::Controller();
		/// Always need db access
		$this->load->model('polls_model');
	}

	/// Set up the navigation bar
	private function _SetupNavbar()
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('polls', 'Polls',
				'/office/polls/');
		$navbar->AddItem('add', 'Add',
				'/office/polls/add/');
		$navbar->AddItem('current', 'Set Current',
				'/office/polls/current/');
	}

	/// Set up the navigation bar
	private function _SetupEditNavbar($poll_id)
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('details', 'Details',
				'/office/polls/edit/'.$poll_id);
		$navbar->AddItem('choices', 'Choices',
				'/office/polls/choices/'.$poll_id);
	}
	
	function index()
	{
		if (!CheckPermissions('office')) return;
		
		$this->_SetupNavbar();
		$this->main_frame->SetPage('polls');
		$this->pages_model->SetPageCode('office_polls_list');
		
		$data = array(
			'poll_list' => $this->polls_model->GetListOfPolls());
		
		$this->main_frame->SetContentSimple('office/polls/poll_list',$data);
		$this->main_frame->Load();
	}
	
	function add()
	{
		if (!CheckPermissions('office')) return;
		
		$this->_SetupNavbar();
		$this->main_frame->SetPage('add');
		$this->pages_model->SetPageCode('office_polls_add');
		
		if (isset($_POST['submit_add_poll'])) {
			if (trim($this->input->post('poll_question')) != '') {
				$this->polls_model->AddNewPoll($this->input->post('poll_question'));
				$this->messages->AddMessage('success', 'The poll has been successfully added.');
			}
			else {
				$this->messages->AddMessage('error', 'You must enter a question for the poll.');
			}
		}
		
		$data = array(
			);
		
		$this->main_frame->SetContentSimple('office/polls/add',$data);
		$this->main_frame->Load();
	}
	
	function current()
	{
		if (!CheckPermissions('office')) return;
		
		if (isset($_POST['r_submit_set_current'])) {
			$this->polls_model->SetPollDisplayed($this->input->post('a_poll_list'));
			$this->messages->AddMessage('success', 'The poll has been made the displayed poll.');
		}
		else if (isset($_POST['r_submit_set_no_current'])) {
			$this->polls_model->SetNoPollDisplayed();
			$this->messages->AddMessage('success', 'No poll\'s are now displayed.');
		}
		
		$this->_SetupNavbar();
		$this->main_frame->SetPage('current');
		$this->pages_model->SetPageCode('office_polls_current');
		
		$data = array(
			'running_poll_list' => $this->polls_model->GetListOfRunningPolls());
		
		$this->main_frame->SetContentSimple('office/polls/current',$data);
		$this->main_frame->Load();
	}
	
	function edit($poll_id)
	{
		if (!CheckPermissions('office')) return;
		
		if (isset($_POST['submit_edit_delete'])) {
			$this->polls_model->DeletePoll($poll_id);
			$this->messages->AddMessage('success', 'The poll has been successfully deleted.');
			redirect('/office/polls');
		}
		elseif (isset($_POST['submit_edit_set_running'])) {
			$this->polls_model->SetPollRunning($poll_id);
			$this->messages->AddMessage('success', 'The poll has been set to running.');
		}
		elseif (isset($_POST['submit_edit_set_not_running'])) {
			$this->polls_model->SetPollNotRunning($poll_id);
			$this->messages->AddMessage('success', 'The poll has been set to not running.');
		}
		
		$this->_SetupEditNavbar($poll_id);
		$this->main_frame->SetPage('details');
		$this->pages_model->SetPageCode('office_polls_details');
		$this->load->library('Polls_view');
		
		$data = array(
			'poll_id' => $poll_id,
			'poll_info' => $this->polls_model->GetPollDetails($poll_id),
			'poll_choice_count' => $this->polls_model->GetPollChoiceCount($poll_id),
			'poll_choice_data' => $this->polls_model->GetPollChoiceVotes($poll_id)
			);
		
		// Set up the public frame
		$this->main_frame->SetTitleParameters(array(
			'name' => $data['poll_info']['question']
		));
		$this->main_frame->SetContentSimple('office/polls/edit',$data);
		$this->main_frame->Load();
	}
	
	function choices($poll_id)
	{
		if (!CheckPermissions('office')) return;
		
		if (isset($_POST['submit_edit_choices'])) {
			if (trim($this->input->post('poll_new_choice_name')) != '') {
				$this->polls_model->AddNewChoice($poll_id, $this->input->post('poll_new_choice_name'));
			}
			if ($this->input->post('poll_choice_name'))
			{
				foreach ($this->input->post('poll_choice_name') as $key => $choice)
				{
					$this->polls_model->UpdatePollChoice($key, $choice);
				}
			}
			if ($this->input->post('poll_choice_delete'))
			{
				foreach ($this->input->post('poll_choice_delete') as $key => $choice)
				{
					$this->polls_model->DeletePollChoice($key, $choice);
				}
			}
			$this->messages->AddMessage('success', 'The poll choices has been successfully updated.');
		}
		
		$this->_SetupEditNavbar($poll_id);
		$this->main_frame->SetPage('choices');
		$this->pages_model->SetPageCode('office_polls_choices');
		
		$data = array(
			'poll_id' => $poll_id,
			'poll_choices' => $this->polls_model->GetPollChoices($poll_id)
			);
		
		// Set up the public frame
		$poll_info = $this->polls_model->GetPollDetails($poll_id);
		$this->main_frame->SetTitleParameters(array(
			'name' => $poll_info['question']
		));
		$this->main_frame->SetContentSimple('office/polls/choices',$data);
		$this->main_frame->Load();
	}
}
		
?>