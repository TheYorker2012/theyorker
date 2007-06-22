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
		
		$this->load->model('comments_model');
		
		$redirect_to = implode('/', array_slice($this->uri->rsegment_array(), 4));
		
		if ($this->input->post('comment_report_confirm', FALSE) !== FALSE) {
			// The user has confirmed, report the comment.
			$result = $this->comments_model->ReportCommentInThread((int)$CommentId, (int)$ThreadId);
			if ($result) {
				$this->messages->AddMessage('success', 'Comment has been reported.');
			} else {
				$this->messages->AddMessage('error', 'Comment could not be reported.');
			}
			redirect($redirect_to);
			
		} elseif ($this->input->post('comment_report_cancel', FALSE) !== FALSE) {
			// The user has cancelled, return to the previous page.
			$this->messages->AddMessage('information', 'Comment has not been reported.');
			redirect($redirect_to);
			
		} else {
			// The user has not confirmed or cancelled, ask for confirmation.
			$this->load->library('comment_views');
			$conditions = array('comments.comment_id = '.(int)$CommentId);
			$comments = $this->comments_model->GetCommentsByThreadId((int)$ThreadId, 'visible', $conditions);
			if (empty($comments)) {
				// The comment isn't visible or doesn't exist.
				$this->messages->AddMessage('error', 'The specified comment could not be found.');
				redirect($redirect_to);
			} else {
				// Ask the user for confirmation.
				// Mark no_report on the comments so no report link
				foreach ($comments as $key => $comment) {
					$comments[$key]['no_report'] = true;
				}
				$this->pages_model->SetPageCode('comment_report');
				
				$data = array();
				$data['maintext'] = $this->pages_model->GetPropertyWikitext('main');
				$data['culprit'] = new CommentViewList();
				$data['target'] = $this->uri->uri_string().'#comments';
				
				$data['culprit']->SetComments($comments);
				
				$this->main_frame->SetContentSimple('comments/report', $data);
				$this->main_frame->Load();
			}
		}
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