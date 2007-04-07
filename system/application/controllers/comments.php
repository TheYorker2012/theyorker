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
	function report($ThreadId = NULL, $CommentId = NULL)
	{
		if (!is_numeric($CommentId) || !is_numeric($ThreadId)) {
			return show_404();
		}
		$this->load->model('comments_model');
		$this->load->library('messages');
		
		$result = $this->comments_model->ReportCommentInThread((int)$CommentId, (int)$ThreadId);
		if ($result) {
			$this->messages->AddMessage('success', 'Comment has been reported.');
		} else {
			$this->messages->AddMessage('error', 'Comment could not be reported.');
		}
		
		// From login controller (should be put in helper)
		$FirstSegment = 5;
		$segments = $this->uri->rsegment_array();
		while ($FirstSegment > 1) {
			array_shift($segments);
			--$FirstSegment;
		}
		redirect(implode('/',$segments));
	}
}

?>