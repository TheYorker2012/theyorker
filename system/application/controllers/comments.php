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
	 * @param $ThreadId int Id of thread comment belongs to.
	 * @param $CommentId int Id of comment to report.
	 *
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
		
		redirect(implode('/', array_slice($this->uri->rsegment_array(), 4)));
	}
	
	/// Delete the specified comment.
	/**
	 * @param $CommentId int Id of comment to delete.
	 */
	function delete($CommentId = NULL)
	{
		if (!is_numeric($CommentId)) {
			return show_404();
		}
		$CommentId = (int)$CommentId;
		if ($CommentId < 1) {
			return show_404();
		}
		
		if (!CheckPermissions('moderator')) return;
		
		$this->load->model('comments_model');
		$result = $this->comments_model->DeleteComment($CommentId);
		if ($result > 0) {
			$this->messages->AddMessage('success', 'Comment deleted');
		} else {
			$this->messages->AddMessage('error', 'Comment could not be deleted');
		}
		
		redirect(implode('/', array_slice($this->uri->rsegment_array(), 3)));
	}
	
	/// Undelete the specified comment.
	/**
	 * @param $CommentId int Id of comment to undelete.
	 */
	function undelete($CommentId = NULL)
	{
		if (!is_numeric($CommentId)) {
			return show_404();
		}
		$CommentId = (int)$CommentId;
		if ($CommentId < 1) {
			return show_404();
		}
		
		if (!CheckPermissions('moderator')) return;
		
		$this->load->model('comments_model');
		$result = $this->comments_model->DeleteComment($CommentId, FALSE);
		if ($result > 0) {
			$this->messages->AddMessage('success', 'Comment undeleted');
		} else {
			$this->messages->AddMessage('error', 'Comment could not be undeleted');
		}
		
		redirect(implode('/', array_slice($this->uri->rsegment_array(), 3)));
	}
	
	/// Flag the specified comment as good.
	/**
	 * @param $CommentId int Id of comment to flag as good.
	 */
	function flaggood($CommentId = NULL)
	{
		if (!is_numeric($CommentId)) {
			return show_404();
		}
		$CommentId = (int)$CommentId;
		if ($CommentId < 1) {
			return show_404();
		}
		
		if (!CheckPermissions('moderator')) return;
		
		$this->load->model('comments_model');
		$result = $this->comments_model->GoodenComment($CommentId);
		if ($result > 0) {
			$this->messages->AddMessage('success', 'Comment flagged good');
		} else {
			$this->messages->AddMessage('error', 'Comment could not be flagged as good');
		}
		
		redirect(implode('/', array_slice($this->uri->rsegment_array(), 3)));
	}
	
	/// Unflag the specified comment as good.
	/**
	 * @param $CommentId int Id of comment to unflag as good.
	 */
	function unflaggood($CommentId = NULL)
	{
		if (!is_numeric($CommentId)) {
			return show_404();
		}
		$CommentId = (int)$CommentId;
		if ($CommentId < 1) {
			return show_404();
		}
		
		if (!CheckPermissions('moderator')) return;
		
		$this->load->model('comments_model');
		$result = $this->comments_model->GoodenComment($CommentId, FALSE);
		if ($result > 0) {
			$this->messages->AddMessage('success', 'Comment unflagged good');
		} else {
			$this->messages->AddMessage('error', 'Comment could not be unflagged as good');
		}
		
		redirect(implode('/', array_slice($this->uri->rsegment_array(), 3)));
	}
}

?>