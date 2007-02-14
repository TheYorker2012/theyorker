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
			$this->events_model->EventsGenerateRecurrences(
					strtotime('+2years')
				);
			
			$this->main_frame->Load();
		} else {
			show_404();
		}
		
	}
}

?>