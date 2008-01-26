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
		if (!CheckPermissions('public')) return;
		
		$redirect_to = implode('/', array_slice($this->uri->rsegment_array(), 4));
		
		// Confirm the reporting with the user.
		$this->pages_model->SetPageCode('comment_report');
		if (!$this->_ConfirmCommentAction($CommentId, $redirect_to)) return;
		
		// The user has confirmed, report the comment.
		$result = $this->comments_model->ReportCommentInThread((int)$CommentId, (int)$ThreadId);
		if ($result) {
			$this->messages->AddMessage('success', 'Comment has been reported.');
		} else {
			$this->messages->AddMessage('error', 'Comment could not be reported.');
		}
		redirect($redirect_to);
	}
	
	/// Confirms a comment action with the user and returns true when ready.
	function _ConfirmCommentAction($CommentId, $redirect_to)
	{
		$this->load->model('comments_model');
		
		if (isset($_POST['comment_confirm_confirm'])) {
			return true;
			
		} elseif (isset($_POST['comment_confirm_cancel'])) {
			// The user has cancelled, return to the previous page.
			$this->messages->AddMessage('information', 'Comment has not been reported.');
			redirect($redirect_to);
			
		} else {
			// The user has not confirmed or cancelled, ask for confirmation.
			$this->load->library('comment_views');
			$comment = $this->comments_model->GetCommentByCommentId((int)$CommentId, 'visible');
			if (NULL === $comment) {
				// The comment isn't visible or doesn't exist.
				$this->messages->AddMessage('error', 'The specified comment could not be found.');
				redirect($redirect_to);
			} else {
				// Ask the user for confirmation.
				// Mark no_report on the comments so no report link
				$comment['no_links'] = true;
				
				$data = array();
				$data['MainText'] = $this->pages_model->GetPropertyWikitext('main');
				$data['Action']   = $this->pages_model->GetPropertyText('action');
				$data['Culprit']  = new CommentViewList();
				$data['Target']   = $this->uri->uri_string().'#comments';
				
				$data['Culprit']->SetComments(array($comment));
				
				$this->main_frame->SetContentSimple('comments/confirm', $data);
				$this->main_frame->Load();
			}
		}
		return false;
	}
	
	/// Decide on whether the user should be able to edit a comment.
	/**
	 * This function allows an owner of a comment or a moderator to edit.
	 */
	function _DecideEditPrivilages($CommentId, $redirect_to, &$comment)
	{
		if (!is_numeric($CommentId)) {
			return show_404();
		}
		if (!CheckPermissions('student')) return;
		
		$this->load->model('comments_model');
		
		$has_permission = false;
		
		// The user has not edited or cancelled, ask for confirmation.
		$this->load->library('comment_views');
		$comment = $this->comments_model->GetCommentByCommentId((int)$CommentId, 'visible');
		if (NULL === $comment) {
			// The comment isn't visible or doesn't exist.
			$this->messages->AddMessage('error', 'The specified comment could not be found.');
			redirect($redirect_to);
		} elseif (!$comment['owned']) {
			// The comment doesn't belong to this user, go to the office
			if (!CheckPermissions('moderator')) return;
			$has_permission = true;
		} else {
			$has_permission = true;
		}
		
		if (!$has_permission) {
			// The user doesn't have permission, return to the previous page.
			$this->messages->AddMessage('error', 'You do not have permission to edit the specified comment.');
			redirect($redirect_to);
		}
		
		return $has_permission;
	}
	
	/// Edit the specified comment.
	function edit($CommentId = NULL)
	{
		$redirect_to = implode('/', array_slice($this->uri->rsegment_array(), 3));
		if (!$this->_DecideEditPrivilages($CommentId, $redirect_to, $comment)) return;
		
		$this->load->library('comment_views');
		$main_view = new CommentViewAdd($comment['thread_id']);
		
		// Do the main processing if any post data
		$main_view->SetExistingComment($comment);
		if ($main_view->CheckPost()) {
			redirect($redirect_to);
		}
		// Display the page.
		$this->pages_model->SetPageCode('comment_edit');
		$this->main_frame->SetContent($main_view);
		$this->main_frame->Load();
	}
	
	/// Delete the specified comment.
	/**
	 * @param $CommentId int Id of comment to delete.
	 */
	function delete($CommentId = NULL)
	{
		$redirect_to = implode('/', array_slice($this->uri->rsegment_array(), 3));
		// Ensure we have privilages
		if (!$this->_DecideEditPrivilages($CommentId, $redirect_to, $comment)) return;
		// Confirm with the user
		$this->pages_model->SetPageCode('comment_delete');
		if (!$this->_ConfirmCommentAction($CommentId, $redirect_to)) return;
		
		$this->load->model('comments_model');
		$result = $this->comments_model->DeleteComment($CommentId);
		if ($result > 0) {
			$this->messages->AddMessage('success', 'Comment deleted');
		} else {
			$this->messages->AddMessage('error', 'Comment could not be deleted');
		}
		
		redirect($redirect_to);
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