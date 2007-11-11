<?php

/**
 * @file controllers/office/moderator.php
 * @brief Comment moderation control panel.
 * @author James Hogan (jh559@york.ac.uk)
 */

/// Moderator controller.
class Moderator extends controller
{
	/// Default function
	/**
	 * Show a moderators control panel.
	 * @todo Use pages model.
	 */
	function index()
	{
		if (!CheckPermissions('moderator')) return;
		$this->pages_model->SetPageCode('office_moderator_index');
		$this->main_frame->SetContentSimple('office/moderator/index');
		$this->main_frame->Load();
	}
	
	/// Show information about comments.
	/**
	 * @param $Comment
	 *	- ('reported') Shows only reported, non-good, non-deleted comments.
	 *	- 'deleted' Shows only deleted comments.
	 *	- int Comment ID.
	 * @todo Use pages model.
	 */
	function comment($Comment = 'reported', $CommentInclude = 1)
	{
		$valids = array('reported','deleted','good');
		if (!in_array($Comment, $valids)) {
			show_404();
		}
		if (!CheckPermissions('moderator')) return;
		
		$this->load->library('comments');
		
		$this->comments->SetUri('/office/moderator/comment/'.$Comment);
		
		// create the views
		$comment_view_list   = new CommentViewList();
		
		// get comments + thread
		if ('reported' === $Comment) {
			$comments = $this->comments_model->GetCommentsByThreadId(NULL,'reported');
		} elseif ('deleted' === $Comment) {
			$comments = $this->comments_model->GetCommentsByThreadId(NULL,'all',array('comments.comment_deleted=TRUE'));
		} elseif ('good' === $Comment) {
			$comments = $this->comments_model->GetCommentsByThreadId(NULL,'all',array('comments.comment_good=TRUE'));
		}
		
		// send the data to the views
		$comment_view_list->SetComments($comments);
		
		// set which page of comments to show
		$comment_view_list->SetIncludedComment($CommentInclude);
		$comment_view_list->SetData('Mode', 'mod');
		
		$this->main_frame->SetContent($comment_view_list);
	
		// Load view
		$this->main_frame->Load();
	}
	
	/// Show information about threads.
	/**
	 * @param $Thread
	 * @todo Use pages model.
	 */
	function thread($Thread = NULL)
	{
		if (!CheckPermissions('moderator')) return;
		$this->main_frame->Load();
	}
}

?>