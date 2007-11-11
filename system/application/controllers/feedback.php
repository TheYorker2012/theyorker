<?php
/**
 * This controller deals with the input from feedback forms.
 *
 * @author Richard Ingle (ri504)
 *
 */

class Feedback extends Controller {

	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		if (!CheckPermissions('public', FALSE)) return;
		
		$this->load->model('feedback_model','feedback_model');

		$redirect_path = $this->input->post('r_redirecturl', '');
		$page_title = $this->input->post('a_pagetitle');
		$author_name = $this->input->post('a_authorname');
		$author_email = $this->input->post('a_authoremail');
		$rating = $this->input->post('a_rating');
		$feedback_text = $this->input->post('a_feedbacktext');
		
		if (FALSE !== $feedback_text) {
			$this->feedback_model->AddNewFeedback($page_title, 
				$author_name, $author_email, 
				$rating, $feedback_text);
	
			$this->main_frame->AddMessage('success',
				'You have sucessfully left feedback, thanks for your thoughts.');
		} else {
			$this->main_frame->AddMessage('error',
				'To leave feedback use the feedback form at the bottom of each page.');
		}
		
		if ($redirect_path === '/')
			$redirect_path = '';
		redirect($redirect_path);
	}
}
?>
