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
		
		$help_text = $this->pages_model->GetPropertyWikitext('help');
		$main_text = $this->pages_model->GetPropertyWikitext('main');
		
		$this->main_frame->SetContentSimple('office/moderator/index', array(
			'HelpText' => $help_text,
			'MainText' => $main_text,
		));
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
		
		$this->pages_model->SetPageCode('office_moderator_comments');
		
		$this->load->library('comment_views');
		
		$this->comment_views->SetUri('/office/moderator/comment/'.$Comment.'/');
		
		// create the views
		$comment_view_list   = new CommentViewList();
		
		// get comments + thread
		if ('reported' === $Comment) {
			$comments = $this->comments_model->GetCommentsByThreadId(NULL,'reported');
		} elseif ('deleted' === $Comment) {
			$comments = $this->comments_model->GetCommentsByThreadId(NULL,'all',array(
				'comments.comment_deleted = TRUE',
				'(comments.comment_deleted_entity_id IS NULL OR comments.comment_deleted_entity_id != comments.comment_author_entity_id)',
			));
		} elseif ('good' === $Comment) {
			$comments = $this->comments_model->GetCommentsByThreadId(NULL,'all',array(
				'comments.comment_good=TRUE',
			));
		}
		
		// send the data to the views
		$comment_view_list->SetComments($comments);
		
		// set which page of comments to show
		$comment_view_list->SetIncludedComment($CommentInclude);
		$comment_view_list->SetData('Mode', 'mod');
		
		// Get the filter from the page properties
		$filter_name = $this->pages_model->GetPropertyText(
			'filters['.$Comment.'].name', FALSE, 'Comments');
		$filter_description = $this->pages_model->GetPropertyWikitext(
			'filters['.$Comment.'].description', FALSE, NULL);
		$this->main_frame->SetTitleParameters(array(
			'filter' => $filter_name,
		));
		
		$this->main_frame->SetContentSimple('office/moderator/comments', array(
			'Comments' => $comment_view_list,
			'Title' => $filter_name,
			'Description' => $filter_description,
		));
	
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