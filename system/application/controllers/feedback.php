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

		$rating_converstion = array( '1' => 'What\'s this for?',
									 '2' => 'Good idea - but what does it do?',
									 '3' => 'Useful.. I guess.',
									 '4' => 'Great idea, and easy to use!',
									 '5' => 'Amazing!!' );

		if (array_key_exists($rating,$rating_converstion)) {
			$rating = $rating_converstion[$rating];
		} else {
			$rating = 'None';
		}

		if (FALSE !== $feedback_text) {
			$this->feedback_model->AddNewFeedback($page_title,
				$author_name, $author_email,
				$rating, $feedback_text);

				$to = $this->pages_model->GetPropertyText('feedback_email', true);
				$from = (strpos($author_email, '@') ? $author_email : 'noreply@theyorker.co.uk');
				$from = 'From: '.$from."\r\n".'Reply-To:'.$from."\r\n";
				$subject = "The Yorker: Site Feedback";
				$message =
'Someone has been kind enough to write in the little box at the bottom of the page.

Page Title: '.$page_title.'
Name: '.$author_name.'
Email: '.$author_email.'
Rating: '.$rating.'

'.$feedback_text.'

Thanks,

The Yorker Team';

			@$send_mail = mail($to,$subject,$message,$from);
			if ($send_mail) {
				$this->main_frame->AddMessage('success',
					'You have successfully left feedback, thanks for your thoughts.');
			} else {
				$this->main_frame->AddMessage('success',
					'You have successfully left feedback, thanks for your thoughts. However there was a problem sending this feedback by e-mail, so we might take a while to respond.');
			}

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
