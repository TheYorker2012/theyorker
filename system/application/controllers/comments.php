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
		$property_arguments = array(
			'verb' => 'report',
			'verbed' => 'reported',
		);
		if (!$this->_ConfirmCommentAction($CommentId, $redirect_to, $property_arguments)) return;
		
		// The user has confirmed, report the comment.
		$result = $this->comments_model->ReportCommentInThread((int)$CommentId, (int)$ThreadId);
		if ($result) {
			$this->messages->AddMessage('success', $this->pages_model->GetPropertyWikitext('msg:success', '_comments', false, $property_arguments));
		} else {
			$this->messages->AddMessage('error', $this->pages_model->GetPropertyWikitext('msg:fail', '_comments', false, $property_arguments));
		}
		redirect($redirect_to);
	}
	
	/// Confirms a comment action with the user and returns true when ready.
	function _ConfirmCommentAction($CommentId, $redirect_to, $property_arguments)
	{
		$this->load->model('comments_model');
		
		if (isset($_POST['comment_confirm_confirm'])) {
			return true;
			
		} elseif (isset($_POST['comment_confirm_cancel'])) {
			// The user has cancelled, return to the previous page.
			$this->messages->AddMessage('information', $this->pages_model->GetPropertyWikitext('msg:cancel', '_comments', false, $property_arguments));
			redirect($redirect_to);
			
		} else {
			// The user has not confirmed or cancelled, ask for confirmation.
			$this->load->library('comment_views');
			$comment = $this->comments_model->GetCommentByCommentId((int)$CommentId, 'visible');
			if (NULL === $comment) {
				// The comment isn't visible or doesn't exist.
				$this->messages->AddMessage('error', $this->pages_model->GetPropertyWikitext('msg:notfound', '_comments', false, $property_arguments));
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
	function _DecideEditPrivilages($CommentId, $redirect_to, &$comment, $property_arguments)
	{
		if (!is_numeric($CommentId)) {
			show_404();
			return false;
		}
		if (!CheckPermissions('student')) return false;
		
		$this->load->model('comments_model');
		
		$has_permission = false;
		
		// The user has not edited or cancelled, ask for confirmation.
		$this->load->library('comment_views');
		$comment = $this->comments_model->GetCommentByCommentId((int)$CommentId, 'visible');
		if (NULL === $comment) {
			// The comment isn't visible or doesn't exist.
			$this->messages->AddMessage('error', $this->pages_model->GetPropertyWikitext('msg:notfound', '_comments', false, $property_arguments));
			redirect($redirect_to);
		} elseif (!$comment['owned']) {
			// The comment doesn't belong to this user, go to the office
			if (!CheckPermissions('moderator')) return false;
			$has_permission = true;
		} else {
			$has_permission = true;
		}
		
		if (!$has_permission) {
			// The user doesn't have permission, return to the previous page.
			$this->messages->AddMessage('error', $this->pages_model->GetPropertyWikitext('msg:permission', '_comments', false, $property_arguments));
			redirect($redirect_to);
		}
		
		return $has_permission;
	}
	
	/// Edit the specified comment.
	function edit($CommentId = NULL)
	{
		$redirect_to = implode('/', array_slice($this->uri->rsegment_array(), 3));
		$property_arguments = array(
			'verb' => 'edit',
			'verbed' => 'edited',
		);
		if (!$this->_DecideEditPrivilages($CommentId, $redirect_to, $comment, $property_arguments)) return;
		
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
		$property_arguments = array(
			'verb' => 'delete',
			'verbed' => 'deleted',
		);
		if (!$this->_DecideEditPrivilages($CommentId, $redirect_to, $comment, $property_arguments)) return;
		// Confirm with the user
		$this->pages_model->SetPageCode('comment_delete');
		if (!$this->_ConfirmCommentAction($CommentId, $redirect_to, $property_arguments)) return;
		
		$this->load->model('comments_model');
		$result = $this->comments_model->DeleteComment((int)$CommentId);
		if ($result > 0) {
			$this->messages->AddMessage('success', $this->pages_model->GetPropertyWikitext('msg:success', '_comments', false, $property_arguments));
		} else {
			$this->messages->AddMessage('error', $this->pages_model->GetPropertyWikitext('msg:fail', '_comments', false, $property_arguments));
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
		$result = $this->comments_model->DeleteComment((int)$CommentId, FALSE);
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
	
	/// Redirect to the page with a given comment.
	/**
	 * @param $CommentId int Id of comment to display page of.
	 */
	function thread($CommentId = NULL)
	{
		if (!CheckPermissions('public')) return;
		
		$debug = false;
		
		$config = $this->config->item('comments');
		
		$comment = NULL;
		if (is_numeric($CommentId)) {
			$this->load->model('comments_model');
			
			// Find thread information for this comment.
			$comment = $this->comments_model->GetCommentByCommentId((int)$CommentId, 'all');
		}
		
		if (NULL === $comment) {
			// The comment doesn't exist or isn't a valid id.
			$this->load->library('custom_pages');
			$this->main_frame->SetContent(new CustomPageView('comment_notfound','error'));
			$this->main_frame->Load();
			$CommentId = NULL;
		}
		else {
			// This array describes how to construct the URL's to a comment
			// if its thread is in particular areas of the site.
			$standard_anchor = 'CommentItem'.$CommentId;
			$thread_source_data = array(
				// Public article threads.
				array(
					'table'  => 'articles',
					'column' => 'article_public_comment_thread_id',
					'joins'  => array(
						array(
							'table' => 'content_types',
							'on'    => 'content_type_id = article_content_type_id',
						),
					),
					'uri'    => array(
						'news',
						array('field'   => 'content_type_codename'),
						array('field'   => 'article_id'),
						array('special' => 'page_id'),
						'anchor' => $standard_anchor,
					),
				),
				// Private article threads.
				array(
					'table'  => 'articles',
					'column' => 'article_private_comment_thread_id',
					'joins'  => array(
					),
					'uri'    => array(
						'office',
						'news',
						array('field'   => 'article_id'),
						array('special' => 'page_id'),
						'anchor' => $standard_anchor,
					),
				),
				// Public review threads.
				array(
					'table'  => 'review_contexts',
					'column' => 'review_context_comment_thread_id',
					'joins'  => array(
						array(
							'table' => 'content_types',
							'on'    => 'content_type_id = review_context_content_type_id',
						),
						array(
							'table' => 'organisations',
							'on'    => 'organisation_entity_id = review_context_organisation_entity_id',
						),
					),
					'uri'    => array(
						'reviews',
						array('field'   => 'content_type_codename'),
						array('field'   => 'organisation_directory_entry_name'),
						array('special' => 'page_id'),
						'anchor' => $standard_anchor,
					),
				),
				// Private review threads.
				array(
					'table'  => 'review_contexts',
					'column' => 'review_context_office_comment_thread_id',
					'joins'  => array(
						array(
							'table' => 'content_types',
							'on'    => 'content_type_id = review_context_content_type_id',
						),
						array(
							'table' => 'organisations',
							'on'    => 'organisation_entity_id = review_context_organisation_entity_id',
						),
					),
					'uri'    => array(
						'office',
						'reviews',
						array('field'   => 'organisation_directory_entry_name'),
						array('field'   => 'content_type_codename'),
						'comments',
						'view',
						array('special' => 'page_id'),
						'anchor' => $standard_anchor,
					),
				),
			);
			// Go through each thread type trying to match with this comment.
			foreach ($thread_source_data as $data) {
				$selects = array();
				foreach ($data['uri'] as $segment) {
					if (is_array($segment)) {
						if (isset($segment['field'])) {
							$selects[] = $segment['field'];
						}
					}
				}
				
				$bind = array();
				$sql = '
					SELECT
						'.implode(', ', $selects).'
					FROM '.$data['table'];
				foreach ($data['joins'] as $join) {
					$sql .= '
					INNER JOIN '.$join['table'].' ON '.$join['on'];
				}
				$sql .= '
					WHERE
						'.$data['column'].' = ?
					';
				$bind[] = (int)$comment['thread_id'];
				if ($debug) {
					var_dump($sql, $bind);
				}
				$query = $this->db->query($sql, $bind);
				$results = $query->result_array();
				
				$links = array();
				foreach ($results as $result) {
					// Construct the uri segments mechanically from the data.
					$segments = array();
					foreach ($data['uri'] as $key => $uriSegment) {
						if (is_int($key)) {
							if (is_string($uriSegment)) {
								$segments[] = $uriSegment;
							}
							elseif (is_array($uriSegment)) {
								if (isset($uriSegment['field'])) {
									$segments[] = $result[$uriSegment['field']];
								}
								elseif (isset($uriSegment['special'])) {
									switch ($uriSegment['special']) {
									case 'page_id':
										// Find the local comment id by counting
										// the number of comments of same thread
										// which were posted before this one.
										$query = $this->db->query(
											'SELECT	COUNT(*) AS count'.
											' FROM	comments'.
											' WHERE	comment_comment_thread_id = ?'.
											' AND	comment_post_time < FROM_UNIXTIME(?)',
											array(
												(int)$comment['thread_id'],
												(int)$comment['post_time'],
											)
										);
										// Adjust to get the first comment of
										// the page. This makes the url for a
										// page of comments unique.
										$segments[] = floor($query->row()->count / $config['max_per_page']) * $config['max_per_page'] + 1;
										break;
									}
								}
							}
						}
					}
					
					// Construct the link
					$link = implode('/', $segments);
					if (isset($data['uri']['anchor'])) {
						$link .= '#'.$data['uri']['anchor'];
					}
					$links[] = $link;
					
					// Don't bother generating more than one for now.
					// If a thread is displayed in more than one location,
					// this could generate multiple links and could be displayed
					// as a list of links.
					// It wouldn't be difficult to extract the titles in a
					// similar way the uris are determined mechanically above.
					break;
				}
				
				if (count($links)) {
					if (!$debug) {
						redirect($links[0]);
					}
					else {
						var_dump($links);
					}
					return;
				}
			}
			
			// The comment was found, but its thread location couldn't be determined.
			$this->load->library('custom_pages');
			$this->main_frame->SetContent(new CustomPageView('comment_unthreaded','error'));
			$this->main_frame->Load();
		}
	}
}

?>