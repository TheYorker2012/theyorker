<?php

class Feedback extends Controller {

	function __construct()
	{
		parent::Controller();
		// Load feedback model
		$this->load->model('feedback_model');
	}

	function index()
	{
		if (!CheckPermissions('office')) return;

		$this->load->library('xajax');
		function deleteEntry ($entry_id)
		{
			$CI = &get_instance();
			$CI->load->model('feedback_model');
			$xajax_response = new xajaxResponse();
			if ((is_numeric($entry_id)) && ($CI->feedback_model->DeleteFeedback($entry_id))) {
				$xajax_response->addAssign('new_entries','innerHTML', $CI->feedback_model->GetFeedbackCount());
				$xajax_response->addAssign('deleted_entries','innerHTML', $CI->feedback_model->GetFeedbackCount(1));
				$xajax_response->addAssign('feedback'.$entry_id,'innerHTML', 'DELETED!');
				$xajax_response->addScript('Effect.BlindUp(\'container'.$entry_id.'\');');
			} else {
				$xajax_response->addAlert('Error deleting entry ' . $entry_id . ', reload the page and try again.');
			}
			return $xajax_response;
		}
		$this->xajax->registerFunction('deleteEntry');
		$this->xajax->processRequests();

		$this->pages_model->SetPageCode('admin_feedback');
		$data['entries'] = $this->feedback_model->GetAllFeedback();
		$data['new_entries'] = $this->feedback_model->GetFeedbackCount();
		$data['deleted_entries'] = $this->feedback_model->GetFeedbackCount(1);
		$data['editable'] = true;

		$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));
		$this->main_frame->SetContentSimple('admin/feedback', $data);
		$this->main_frame->Load();
	}

	function deleted ()
	{
		if (!CheckPermissions('admin')) return;

		$this->pages_model->SetPageCode('admin_feedback');
		$data['entries'] = $this->feedback_model->GetAllFeedback(1);
		$data['new_entries'] = $this->feedback_model->GetFeedbackCount();
		$data['deleted_entries'] = $this->feedback_model->GetFeedbackCount(1);
		$data['editable'] = false;

		$this->main_frame->SetContentSimple('admin/feedback', $data);
		$this->main_frame->Load();
	}

}
?>
