<?php

/**
 * @file libraries/Comment_views.php
 * @brief Comments helper.
 * @pre loaded(library Frames)
 * @pre loaded(library Messages)
 * @pre loaded(model User_auth)
 *
 * @note Loads model comments_model.
 * @todo Handle many comments by selecting only desired from db.
 */

/// View for thread information and rating.
class CommentViewThread extends FramesView
{
	/// int Thread id.
	protected $mThreadId;
	/// array Thread information.
	protected $mThread = NULL;
	
	/// Default constructor.
	/**
	 * @param $ThreadId int ID of thread (must exist).
	 */
	function __construct($ThreadId)
	{
		/// @pre is_int(@a $ThreadId)
		assert('is_int($ThreadId)');
		parent::__construct('comments/thread');
		$this->mThreadId = $ThreadId;
	}
	
	/// Set the thread data from the model.
	/**
	 * @param $Thread array Thread information.
	 */
	function SetThread(&$Thread)
	{
		$this->mThread = & $Thread;
	}
	
	/// Check for comment adding post data.
	/**
	 * @pre SetThread must have been called.
	 */
	function CheckPost()
	{
		$CI = & get_instance();
		// read rating post data for thread
		$user_rating_value = $CI->input->post('UserRatingValue');
		if (is_numeric($this->mThread['user_rating'])) {
			$this->mThread['user_rating'] = (int)$this->mThread['user_rating'];
		}
		if (is_numeric($user_rating_value)) {
			$user_rating_value = (int)$user_rating_value;
			$verb = 'set';
		} elseif ('no' === $user_rating_value) {
			$user_rating_value = NULL;
			$verb = 'removed';
		} else {
			$user_rating_value = $this->mThread['user_rating'];
		}
		$changed = ($user_rating_value !== $this->mThread['user_rating']);
		if ($changed) {
			$result = $CI->comments_model->SetUserRating($this->mThreadId, $user_rating_value);
			if (FALSE !== $result) {
				$CI->messages->AddMessage('information','Your rating has been '.$verb);
				// Update thread object
				// old value not NULL: remove
				if (NULL !== $this->mThread['user_rating']) {
					--$this->mThread['num_ratings'];
					$this->mThread['total_rating'] -= $this->mThread['user_rating'];
				}
				// new value not NULL: insert
				if (NULL !== $user_rating_value) {
					++$this->mThread['num_ratings'];
					$this->mThread['total_rating'] += $user_rating_value;
				}
				// new value and update average
				$this->mThread['user_rating'] = $user_rating_value;
				if ($this->mThread['num_ratings'] > 0) {
					$this->mThread['average_rating'] = $this->mThread['total_rating']/$this->mThread['num_ratings'];
				} else {
					$this->mThread['average_rating'] = NULL;
				}
			} else {
				$CI->messages->AddMessage('error', 'Invalid user rating');
			}
		}
	}
	
	function Load()
	{
		$CI = & get_instance();
		// for thread info
		
		$logged_in = $CI->user_auth->isLoggedIn;
		$login_url = site_url('login/main'.$CI->uri->uri_string());
		
		$this->SetData('Thread', $this->mThread);
		$this->SetData('LoggedIn', $logged_in);
		$this->SetData('LoginUrl', $login_url.'#comments');
		$this->SetData('RatingTarget', $CI->uri->uri_string().'#comments');
		
		parent::Load();
	}
}

/// View for adding/editing comments.
class CommentViewAdd extends FramesView
{
	/// int Thread id.
	protected $mThreadId;
	/// array Thread information.
	protected $mThread = NULL;
	
	/// array Existing comment.
	protected $mExistingComment = NULL;

	/// Default constructor.
	function __construct($ThreadId)
	{
		parent::__construct('comments/add');
		$this->mThreadId = $ThreadId;
		
		$CI = & get_instance();
		$CI->load->helper('smiley');
		$CI->load->library('table');
		
		// Make the smileys available to the view.
		$image_array = get_clickable_smileys('/images/smileys/');
		$col_array = $CI->table->make_columns($image_array, 8);
		$smiley_table = $CI->table->generate($col_array);
		$this->SetData('SmileyTable', $smiley_table);
		
		$this->SetData('DefaultIdentity', $CI->user_auth->entityId);
		$this->SetData('DefaultAnonymous', FALSE);
		$this->SetData('DefaultContent', '');
		$this->SetData('Preview', NULL);
		$this->SetData('ShowCancelButton', false);
		$this->SetData('AlreadyExists', false);
	}
	
	/// Set an existing comment to edit.
	function SetExistingComment($Comment)
	{
		$this->SetData('ShowCancelButton', true);
		$this->mExistingComment = $Comment;
		$this->SetData('DefaultContent', $Comment['wikitext']);
		$this->SetData('AlreadyExists', true);
	}
	
	/// Set the thread data from the model.
	/**
	 * @param $Thread array Thread information.
	 */
	function SetThread(&$Thread)
	{
		$this->mThread = & $Thread;
	}
	
	/// Check for comment adding post data.
	/**
	 *	checks for comment adding
	 *	checks for preview
	 * @note Identity is taken from logged in user.
	 * @return bool Whether to redirect back to referer.
	 */
	function CheckPost()
	{
		$CI = & get_instance();
		
		// If comment exists, initialise preview
		if (NULL !== $this->mExistingComment) {
			// if cancel, cancel now
			if (FALSE !== $CI->input->post('CommentAddCancel')) {
				return true; // redirect
			}
			$preview = $this->mExistingComment;
			$preview['edits'][] = array(
				'edit_time' => time(),
				'by_author' => $this->mExistingComment['owned'],
			);
		} else {
			unset($preview);
		}
		
		// Read comment post data for adder
		$wikitext = $CI->input->post('CommentAddContent');
		if ($wikitext !== FALSE) {
			$this->SetData('DefaultContent', $wikitext);
			$default_anonymous = $CI->input->post('CommentAddAnonymous');
			if ($default_anonymous !== FALSE) $default_anonymous = TRUE;
			$this->SetData('DefaultAnonymous', $default_anonymous);
			/*$identity = $CI->input->post('CommentAddIdentity');
			if (is_numeric($identity)) {
				$identity = (int)$identity;
			}*/
			$comment = array(
				'wikitext' => $wikitext,
				'anonymous' => $default_anonymous,
				//'author_id' => $identity,
			);
			// Check not just whitespace
			if (preg_match('/^[\t\n ]*$/',$wikitext)) {
				$CI->messages->AddMessage('error', 'Your comment did not have any content');
			} elseif (isset($this->mExistingComment) && $wikitext == $this->mExistingComment['wikitext']) {
				$CI->messages->AddMessage('error', 'Your comment has not changed');
			} else {
				$has_preview = (FALSE === $CI->input->post('CommentAddSubmit'));
				if ($has_preview) {
					//if (is_int($identity)) {
						if (isset($preview)) {
							$preview['wikitext'] = $wikitext;
							$preview['xhtml'] = $CI->comments_model->ParseCommentWikitext($wikitext);
						} else {
							$preview = $CI->comments_model->GetCommentPreview($comment);
						}
						$success = (NULL !== $preview);
					/*} else {
						$success = FALSE;
					}*/
					if ($success) {
						//$default_identity = $identity;
						$CI->messages->AddMessage('success', 'Preview created');
					} else {
						$CI->messages->AddMessage('error', 'Comment preview could not be created');
					}
				} else {
					if (!$CI->user_auth->isLoggedIn) {
						$CI->messages->AddMessage('error', 'Comment could not be added. You must be logged in to post comments.');
					} else {
						if (NULL !== $this->mExistingComment) {
							$success = $CI->comments_model->EditCommentContent($this->mExistingComment['comment_id'], $wikitext);
							if ($success) {
								$CI->messages->AddMessage('success', 'Comment edited');
								return true;
							} else {
								// something went wrong, keep track of the data in case shitty browser loses it
								$preview['wikitext'] = $wikitext;
								$preview['xhtml'] = $CI->comments_model->ParseCommentWikitext($wikitext);
								$CI->messages->AddMessage('success', 'Comment could not be edited');
								return false;
							}
						} else {
							$success = (/*is_int($identity) &&*/
								$CI->comments_model->AddCommentByThreadId($this->mThreadId, $comment));
							if ($success) {
								$CI->messages->AddMessage('success', 'Comment added');
								///@TODO: Make it go to #CommentItem{NewCommentID}
								redirect($CI->comment_views->GenUri('last'));
							} else {
								$CI->messages->AddMessage('error', 'Comment could not be added');
							}
						}
					}
				}
			}
		}
		if (isset($preview)) {
			// Postprocess the preview
			$preview['owned'] = false;
			if (NULL === $preview['author']) {
				$preview['author'] = 'Anonymous';
			}
			$preview['post_time'] = $CI->time_format->date('%D %T', $preview['post_time'], true);
			if (NULL !== $preview['deleted_time']) {
				$preview['deleted_time'] = $CI->time_format->date('%D %T', $preview['deleted_time'], true);
			}
			foreach ($preview['edits'] as $edit_id => &$edit) {
				$edit['edit_time'] = $CI->time_format->date('%D %T', $edit['edit_time'], true);
			}
			$preview['comment_order_num'] = '';
			$preview['preview'] = true;
			$preview['no_links'] = true;
			
			$this->SetData('Preview', $preview);
		}
		return false;
	}
	
	function Load()
	{
		$CI = & get_instance();
		
		$logged_in = $CI->user_auth->isLoggedIn;
		$login_url = site_url('login/main'.$CI->uri->uri_string());
		
		$this->SetData('Thread', $this->mThread);
		$this->SetData('LoggedIn', $logged_in);
		$this->SetData('LoginUrl', $login_url.'#comments');
		$this->SetData('FormTarget', $CI->uri->uri_string().'#comment_preview');
		$this->SetData('Identities', $CI->comments_model->GetAvailableIdentities());
		
		$this->SetData('ReportUrlPrefix', '/comments/report/');
		$this->SetData('ReportUrlPostfix', $CI->uri->uri_string());
		
		// New comment
		if (NULL === $this->mExistingComment) {
			$warning_xml = $CI->pages_model->GetPropertyWikitext('policy_warning_add', '_comments');
		}
		else {
			// Editing own comment
			if ($this->mExistingComment['owned']) {
				$warning_xml = $CI->pages_model->GetPropertyWikitext('policy_warning_edit', '_comments');
			}
			// Editing another's comment
			else {
				$warning_xml = $CI->pages_model->GetPropertyWikitext('policy_warning_moderator', '_comments');
			}
		}
		
		$this->SetData('WarningMessageXml', $warning_xml);
		
		parent::Load();
	}
}

/// View for thread information and rating.
class CommentViewList extends FramesView
{
	/// int Comment number to show the page of.
	protected $mIncludedComment = 0;
	/// array Comment list.
	protected $mComments = array();
	
	/// int Maximum number of comments per page.
	protected $mMaxPerPage = 20;
	protected $mPageLinkSpan = 2;
	
	/// Default constructor.
	function __construct()
	{
		parent::__construct('templates/list');
		
		$config = get_instance()->config->item('comments');
		$this->SetData('Mode', ($config['edit']['moderator'] && PermissionsSubset('moderator', GetUserLevel())) ? 'mod' : null);
	}
	
	/// Set the number of a comment to show the page of.
	function SetIncludedComment($Included)
	{
		$this->mIncludedComment = $Included;
	}
	
	/// Set the maximum number of comments per page.
	/**
	 * @param $MaxPerPage int Maximum comments displayed per page.
	 */
	function SetMaxPerPage($MaxPerPage = 0)
	{
		assert('is_int($MaxPerPage)');
		$this->mMaxPerPage = $MaxPerPage;
	}
	
	/// Set the numer of pages to link to either side of the current page.
	/**
	 * @param $PageLinkSpan int Page links either side of current page.
	 */
	function SetPageLinkSpan($PageLinkSpan = 0)
	{
		assert('is_int($MaxPerPage)');
		$this->mPageLinkSpan = $PageLinkSpan;
	}
	
	/// Comment data from comments model.
	function SetComments($Comments)
	{
		$this->mComments = $Comments;
		$CI = & get_instance();
		
		// comment postprocessing for list
		$comment_order = 1;
		foreach ($this->mComments as $key => &$comment) {
			if (NULL === $comment['author']) {
				$comment['author'] = 'Anonymous';
			}
			$comment['post_time'] = $CI->time_format->date('%D %T', $comment['post_time'], true);
			if (NULL !== $comment['deleted_time']) {
				$comment['deleted_time'] = $CI->time_format->date('%D %T', $comment['deleted_time'], true);
			}
			foreach ($comment['edits'] as $edit_id => &$edit) {
				$edit['edit_time'] = $CI->time_format->date('%D %T', $edit['edit_time'], true);
			}
			$comment['comment_order_num'] = $comment_order++;
		}
	}
	
	/// Get the number of comments.
	function GetNumComments()
	{
		return count($this->mComments);
	}
	
	/// Find whether the comments array is empty.
	function EmptyComments()
	{
		return empty($this->mComments);
	}
	
	/// Load the view
	function Load()
	{
		// Validate the included comment.
		if (!is_numeric($this->mIncludedComment)) {
			if ($this->mIncludedComment == 'last') {
				$this->mIncludedComment = count($this->mComments);
			} else {
				$this->mIncludedComment = 1;
			}
		}

		$CI = & get_instance();

		$this->SetData('Items', $this->mComments);
		$this->SetData('InnerView', 'comments/comment');
		$this->SetData('InnerItemName', 'Comment');
		$this->SetData('MaxPerPage', $this->mMaxPerPage);
		$this->SetData('PageLinkSpan', $this->mPageLinkSpan);
		$this->SetData('IncludedIndex', $this->mIncludedComment);
		$this->SetData('PageUrlPrefix', $CI->comment_views->GetUriPrefix());
		$this->SetData('PageUrlPostfix', $CI->comment_views->GetUriPostfix() .'#comments');
		// for subviews (comments)
		foreach (array(	'Report'	=> 'report',
						'Edit'		=> 'edit',
						'Delete'	=> 'delete',
						'Undelete'	=> 'undelete',
						'Good'		=> 'flaggood',
						'Ungood'	=> 'unflaggood') as $key => $value) {
			$this->SetData($key.'UrlPrefix', '/comments/'.$value.'/');
			$this->SetData($key.'UrlPostfix', $CI->uri->uri_string());
		}
		
		parent::Load();
	}
}

/// Comments helper.
class Comment_views
{
	/// string Prefix to uri (before included comment number).
	protected $mUriPrefix;
	/// string Postfix to uri (after included comment number).
	protected $mUriPostfix;

	/// Default constructor.
	function __construct()
	{
		$CI = & get_instance();
		$CI->load->model('comments_model');
		$CI->load->library('time_format');
	}
	
	/// Create the standard comment layout.
	/**
	 * @param $ThreadId int,thread_array ID of thread or array information.
	 * @param $CommentInclude int Number of a comment to include.
	 * @return FramesView,NULL View class or NULL if unsuccessful
	 */
	function CreateStandard($ThreadId, $CommentInclude = NULL, $MaxPerPage = 20)
	{
		// get comments + thread
		$CI = & get_instance();
		if (is_int($ThreadId)) {
			$thread = $CI->comments_model->GetThreadById($ThreadId);
		} else {
			$thread = $ThreadId;
		}
		if (NULL === $thread) {
			return NULL;
		}
		$thread_id = (int)$thread['thread_id'];
		
		// create the views
		$comment_view_thread = new CommentViewThread($thread_id);
		$comment_view_add    = new CommentViewAdd($thread_id);
		$comment_view_list   = new CommentViewList();
		
		// send the data to the views
		$comment_view_add->SetThread($thread);
		$comment_view_thread->SetThread($thread);
		
		// handle any form post data
		$comment_view_add->CheckPost();
		$comment_view_thread->CheckPost();
		
		$comments = $CI->comments_model->GetCommentsByThreadId($thread_id,'all');
		$comment_view_list->SetComments($comments);
		
		// set which page of comments to show
		$comment_view_list->SetMaxPerPage($MaxPerPage);
		$comment_view_list->SetIncludedComment($CommentInclude);

		// overall layout
		$data = array(
			'CommentThread' => & $comment_view_thread,
			'CommentAdd'    => & $comment_view_add,
			'CommentList'   => & $comment_view_list,
		);

		return new FramesView('comments/standard', $data);
	}

	function GetLatestComments($MaxComments = 10, $MaxPerPage = 20)
	{
		$CI = & get_instance();
		$comments = $CI->comments_model->GetLatestComments($MaxComments);
		$data = array(
			'comments'			=>	$comments,
			'comments_per_page'	=>	$MaxPerPage
		);
		return new FramesView('comments/latest_box', $data);
	}

	/// Generate a uri for a given included comment number.
	function GenUri($IncludeComment)
	{
		return $this->mUriPrefix.$IncludeComment.$this->mUriPostfix;
	}
	
	/// Get the text before the included comment number.
	function GetUriPrefix()
	{
		return $this->mUriPrefix;
	}
	
	/// Get the text after the included comment number.
	function GetUriPostfix()
	{
		return $this->mUriPostfix;
	}
	
	/// Set the uri text before and after the included comment number.
	function SetUri($Prefix, $Postfix = '')
	{
		$this->mUriPrefix = $Prefix;
		$this->mUriPostfix = $Postfix;
	}
}

?>