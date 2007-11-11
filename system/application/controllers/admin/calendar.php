<?php

class Calendar extends Controller
{
	function __construct()
	{
		parent::Controller();
	}
	
	function recurrences($action = '')
	{
		if ($action === 'generate') {
			if (!CheckPermissions('admin')) return;
			
			$this->load->model('calendar/events_model');
			$result = $this->events_model->EventsGenerateRecurrences(
					strtotime('+2years')
				);
			
			$this->messages->AddMessage('information', $result[0].' occurrrences were created');
			$this->messages->AddMessage('information', $result[1].' events had occurrences generated');
			$this->messages->AddMessage('information', $result[2].' events were busy');
			$this->messages->AddMessage('information', $result[3].' event mutexes were stolen');
			
			$this->main_frame->Load();
		} else {
			show_404();
		}
		
	}
}

?>