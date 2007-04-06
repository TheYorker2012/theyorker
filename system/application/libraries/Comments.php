<?php

/**
 * @file libraries/Comments.php
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
	function SetThread($Thread)
	{
		$this->mThread = $Thread;
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
		if (is_numeric($user_rating_value)) {
			$user_rating_value = (int)$user_rating_value;
			$result = $CI->comments_model->SetUserRating($this->mThreadId, $user_rating_value);
			if (FALSE !== $result) {
				$CI->messages->AddMessage('information','Your rating has been set');
			} else {
				$CI->messages->AddMessage('error', 'Invalid user rating');
			}
		} elseif ($user_rating_value == 'no') {
			$CI->comments_model->SetUserRating($this->mThreadId, NULL);
			$CI->messages->AddMessage('information','Your rating has been removed');
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
		$this->SetData('LoginUrl', $login_url);
		$this->SetData('RatingTarget', $CI->uri->uri_string());
		
		parent::Load();
	}
}

/// View for adding comments.
class CommentViewAdd extends FramesView
{
	/// int Thread id.
	protected $mThreadId;
	/// array Thread information.
	protected $mThread = NULL;
	
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
	}
	
	/// Set the thread data from the model.
	/**
	 * @param $Thread array Thread information.
	 */
	function SetThread($Thread)
	{
		$this->mThread = $Thread;
	}
	
	/// Check for comment adding post data.
	/**
		checks for comment adding
		checks for preview
	 */
	function CheckPost()
	{
		$CI = & get_instance();
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
			} else {
				$has_preview = (FALSE === $CI->input->post('CommentAddSubmit'));
				if ($has_preview) {
					//if (is_int($identity)) {
						$preview = $CI->comments_model->GetCommentPreview($comment);
						$success = (NULL !== $preview);
					/*} else {
						$success = FALSE;
					}*/
					if ($success) {
						//$default_identity = $identity;
						$CI->messages->AddMessage('success', 'Preview created');
						// Postprocess the preview
						$preview['owned'] = TRUE;
						if (NULL === $preview['author']) {
							$preview['author'] = 'anonymous coward';
						}
					} else {
						$CI->messages->AddMessage('error', 'Comment preview could not be created');
					}
				} else {
					$success = (/*is_int($identity) &&*/ $CI->comments_model->AddCommentByThreadId($this->mThreadId, $comment));
					if ($success) {
						$CI->messages->AddMessage('success', 'Comment added');
						redirect($CI->comments->GenUri('last'));
					} else {
						$CI->messages->AddMessage('error', 'Comment could not be added');
					}
				}
			}
		}
		if (isset($preview)) {
			$this->SetData('Preview', $preview);
		}
	}
	
	function Load()
	{
		$CI = & get_instance();
		
		$logged_in = $CI->user_auth->isLoggedIn;
		$login_url = site_url('login/main'.$CI->uri->uri_string());
		
		$this->SetData('Thread', $this->mThread);
		$this->SetData('LoggedIn', $logged_in);
		$this->SetData('LoginUrl', $login_url);
		$this->SetData('FormTarget', $CI->uri->uri_string());
		$this->SetData('Identities', $CI->comments_model->GetAvailableIdentities());
		
		$this->SetData('ReportUrlPrefix', '/comments/report/');
		$this->SetData('ReportUrlPostfix', $CI->uri->uri_string());
		
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
	protected $mPageLinkSpan = 3;
	
	/// Default constructor.
	function __construct()
	{
		parent::__construct('templates/list');
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
		
		// comment postprocessing for list
		foreach ($this->mComments as $key => $comment) {
			if (NULL === $comment['author']) {
				$this->mComments[$key]['author'] = 'anonymous coward';
			}
		}
	}
	
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
		$this->SetData('PageUrlPrefix', $CI->comments->GetUriPrefix());
		$this->SetData('PageUrlPostfix', $CI->comments->GetUriPostfix());
		// for subviews (comments)
		$this->SetData('ReportUrlPrefix', '/comments/report/');
		$this->SetData('ReportUrlPostfix', $CI->uri->uri_string());
		
		parent::Load();
	}
}

/// Comments helper.
class Comments
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
		$comments = $CI->comments_model->GetCommentsByThreadId($thread_id,'visible');
		
		// create the views
		$comment_view_thread = new CommentViewThread($thread_id);
		$comment_view_add    = new CommentViewAdd($thread_id);
		$comment_view_list   = new CommentViewList();
		
		// handle any form post data
		$comment_view_add->CheckPost();
		$comment_view_thread->CheckPost();
		
		// send the data to the views
		$comment_view_list->SetComments($comments);
		$comment_view_add->SetThread($thread);
		$comment_view_thread->SetThread($thread);
		
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