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

		SetupMainFrame('public');
	}

	function index()
	{
		$this->load->model('feedback_model','feedback_model');

		$this->feedback_model->AddNewFeedback($_POST['a_pagetitle'], $_POST['a_authorname'], $_POST['a_authoremail'], $_POST['a_rating'], $_POST['a_feedbacktext']);

                $this->main_frame->AddMessage('success','You have sucessfully left feedback, thanks for your thoughts.');
		redirect($_POST['r_redirecturl']);
	}
}
?>
