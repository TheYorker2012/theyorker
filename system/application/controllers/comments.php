<?php

/**
 * @file controllers/comments.php
 * @brief Comment tools such as reporting.
 * @author James Hogan (jh559@cs.york.ac.uk)
 */

/// Comments public controller.
class Comments extends Controller
{
	/// Report a comment as offensive/spam.
	/**
	 * Like the login controllers, this function can have segments appended
	 *	which get redirected to after completion.
	 *
	 * First show a human tester to throw off bots, then report.
	 *
	 * @todo Do human test.
	 */
	function report($CommentId)
	{
		$this->load->model('comments_model');
		$this->load->library('messages');
		
		if (is_numeric($CommentId)) {
			$result = $this->comments_model->ReportCommentInThread((int)$CommentId, 1);
			if ($result) {
				$this->messages->AddMessage('success', 'Comment has been reported.');
			} else {
				$this->messages->AddMessage('error', 'Comment could not be reported.');
			}
		}
		
		// From login controller (should be put in helper)
		$FirstSegment = 4;
		$segments = $this->uri->rsegment_array();
		while ($FirstSegment > 1) {
			array_shift($segments);
			--$FirstSegment;
		}
		redirect(implode('/',$segments));
	}
}

?>