<?php

/**
 * @file models/comments_model.php
 * @brief Comments + threads model.
 * @author James Hogan (jh559@york.ac.uk)
 */

/// Comments + threads model.
class Comments_model extends model
{
	/// Default constructor
	function __construct()
	{
		parent::model();
	}
	
	/// Recreate the triggers.
	function CreateTriggers()
	{
		$sql_commands = array(
			'DROP TRIGGER comment_ratings_insert',
			'CREATE TRIGGER comment_ratings_insert AFTER INSERT ON comment_ratings
				FOR EACH ROW BEGIN
					UPDATE comment_threads SET
						comment_threads.comment_thread_total_rating
							= comment_threads.comment_thread_total_rating + NEW.comment_rating_value,
						comment_threads.comment_thread_num_ratings
							= comment_threads.comment_thread_num_ratings + 1
						WHERE comment_threads.comment_thread_id = NEW.comment_rating_comment_thread_id;
				END',
			
			'DROP TRIGGER comment_ratings_update',
			'CREATE TRIGGER comment_ratings_update BEFORE UPDATE ON comment_ratings
				FOR EACH ROW BEGIN
					IF OLD.comment_rating_comment_thread_id = NEW.comment_rating_comment_thread_id THEN
						UPDATE comment_threads SET
							comment_threads.comment_thread_total_rating
								= comment_threads.comment_thread_total_rating
								+ (NEW.comment_rating_value - OLD.comment_rating_value)
							WHERE comment_threads.comment_thread_id = OLD.comment_rating_comment_thread_id;
					ELSE
						UPDATE comment_threads SET
							comment_threads.comment_thread_total_rating
								= comment_threads.comment_thread_total_rating - OLD.comment_rating_value,
							comment_threads.comment_thread_num_ratings
								= comment_threads.comment_thread_num_ratings - 1
							WHERE comment_threads.comment_thread_id = OLD.comment_rating_comment_thread_id;
						UPDATE comment_threads SET
							comment_threads.comment_thread_total_rating
								= comment_threads.comment_thread_total_rating + NEW.comment_rating_value,
							comment_threads.comment_thread_num_ratings
								= comment_threads.comment_thread_num_ratings + 1
							WHERE comment_threads.comment_thread_id = NEW.comment_rating_comment_thread_id;
					END IF;
				END',
			
			'DROP TRIGGER comment_ratings_delete',
			'CREATE TRIGGER comment_ratings_delete BEFORE DELETE ON comment_ratings
				FOR EACH ROW BEGIN
					UPDATE comment_threads SET
						comment_threads.comment_thread_total_rating
							= comment_threads.comment_thread_total_rating - OLD.comment_rating_value,
						comment_threads.comment_thread_num_ratings
							= comment_threads.comment_thread_num_ratings - 1
						WHERE comment_threads.comment_thread_id = OLD.comment_rating_comment_thread_id;
				END',
		);

		foreach ($sql_commands as $sql) $this->db->query($sql);
	}
	
	/// Create threads for objects.
	/**
	 * @param $Properties array[key=>value] Array of thread properties:
	 *	- 'allow_ratings' bool=FALSE Whether to allow users to rate the thread.
	 *	- 'allow_comments' bool=TRUE Whether to allow the user to post comments.
	 *	- 'allow_anonymous_comments' bool=TRUE Whether to allow anonymous comments.
	 * @param $Table string Name of table.
	 * @param $Keys string,array[string] [array of] primary key field name[s].
	 * @param $Field string Name of field.
	 * @param $Condition string SQL expression in addition to the field being NULL.
	 *
	 * For each row of @a $Table satisfying @a $Condition where the field
	 *	@a $Field is NULL, create a new thread with the properties in
	 *	@a $Properties and set the field @a $Field to the new thread.
	 * 
	 */
	function CreateThreads($Properties, $Table, $Keys, $Field, $Conditions = array())
	{
		$properties = array(
			'comment_thread_allow_ratings' => FALSE,
			'comment_thread_allow_comments' => TRUE,
			'comment_thread_allow_anonymous_comments' => TRUE,
		);
		
		// Ensure $Keys is an array.
		if (is_string($Keys)) {
			$Keys = array($Keys);
		}
		// Ensure $Conditions is an array.
		if (is_string($Conditions)) {
			$Conditions = array($Conditions);
		}
		
		// Validate properties
		foreach ($Properties as $key => $value) {
			if (array_key_exists('comment_thread_'.$key, $properties)) {
				$properties['comment_thread_'.$key] = $value;
			}
		}
		
		// Get the rows that need updating
		$this->db->select($Keys);
		$this->db->where($Table.'.'.$Field.' IS NULL');
		foreach ($Conditions as $condition) {
			$this->db->where($condition);
		}
		$query = $this->db->get($Table);
		
		// Go through the rows creating threads.
		$failed_inserts = array();
		$failed_updates = array();
		$failed_deletes = array();
		foreach ($query->result_array() as $row) {
			// insert
			$this->db->insert('comment_threads', $properties);
			if ($this->db->affected_rows() > 0) {
				$new_thread_id = $this->db->insert_id();
				// update setting field to new thread id where keys match $row
				$this->db->update($Table, array($Field => $new_thread_id), $row);
				if ($this->db->affected_rows() === 0) {
					$failed_updates[] = $row;
					$this->db->delete('comment_threads', array('comment_thread_id' => $new_thread_id));
					$failed_deletes[] = $new_thread_id;
				}
			} else {
				$failed_inserts[] = $row;
			}
		}
		return array(
			'failed_inserts' => $failed_inserts,
			'failed_updates' => $failed_updates,
			'failed_deletes' => $failed_deletes,
		);
	}
	
	/// Create a new thread and link to a table.
	/**
	 * @param $Properties array[key=>value] Array of thread properties:
	 *	- 'allow_ratings' bool=FALSE Whether to allow users to rate the thread.
	 *	- 'allow_comments' bool=TRUE Whether to allow the user to post comments.
	 *	- 'allow_anonymous_comments' bool=TRUE Whether to allow anonymous comments.
	 * @param $Table string Name of table.
	 * @param $Keys array[string => string] array of primary key values.
	 * @param $Field string Name of field.
	 * @return bool Whether the comment thread was created.
	 *	Note that this will be false if a comment thread already exists.
	 *
	 * For each row of @a $Table satisfying @a $Keys, the thread is set to a new thread.
	 */
	function CreateThread($Properties, $Table, $Keys, $Field)
	{
		$properties = array(
			'comment_thread_allow_ratings' => FALSE,
			'comment_thread_allow_comments' => TRUE,
			'comment_thread_allow_anonymous_comments' => TRUE,
		);

		// Validate properties
		foreach ($Properties as $key => $value) {
			if (array_key_exists('comment_thread_'.$key, $properties)) {
				$properties['comment_thread_'.$key] = $value;
			}
		}
		
		// insert
		$this->db->insert('comment_threads', $properties);
		if ($this->db->affected_rows() > 0) {
			$new_thread_id = $this->db->insert_id();
			$this->db->where($Field.' IS NULL');
			$this->db->where($Keys);
			$this->db->limit(1);
			$this->db->update($Table, array($Field => $new_thread_id));
			if ($this->db->affected_rows() === 0) {
				$this->db->delete('comment_threads', array('comment_thread_id' => $new_thread_id));
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			return FALSE;
		}
	}
	
	/// Get the fields which need selecting from a thread.
	/**
	 * @return string Sql fields needed for thread.
	 */
	function GetThreadSelectFields($thread_alias = 'comment_threads', $rating_alias = 'comment_ratings')
	{
		return $thread_alias.'.comment_thread_id AS thread_id,
			'.$thread_alias.'.comment_thread_allow_ratings AS allow_ratings,
			'.$thread_alias.'.comment_thread_allow_comments AS allow_comments,
			'.$thread_alias.'.comment_thread_allow_anonymous_comments AS allow_anonymous_comments,
			'.$thread_alias.'.comment_thread_num_ratings AS num_ratings,
			'.$thread_alias.'.comment_thread_total_rating AS total_rating,
			IF ('.$thread_alias.'.comment_thread_num_ratings > 0,
				'.$thread_alias.'.comment_thread_total_rating / '.$thread_alias.'.comment_thread_num_ratings,
				NULL) AS average_rating,
			'.$rating_alias.'.comment_rating_value AS user_rating';
	}
	
	/// Get the joins needed for the thread select.
	/**
	 * @return string Sql joins needed for thread.
	 */
	function GetThreadSelectJoins($thread_alias = 'comment_threads', $rating_alias = 'comment_ratings')
	{
		$user_id = ($this->user_auth->isLoggedIn ? $this->user_auth->entityId : NULL);
		return 'LEFT JOIN comment_ratings as '.$rating_alias.'
			ON '.$rating_alias.'.comment_rating_comment_thread_id = '.$thread_alias.'.comment_thread_id
			AND '.$rating_alias.'.comment_rating_author_entity_id = '.$this->db->escape($user_id);
	}
	
	/// Get thread information from the thread id.
	/**
	 * @param $ThreadId int ID of the thread.
	 * @return thread_array,NULL Thread information
	 *	- 'allow_ratings'
	 *	- 'allow_comments'
	 *	- 'allow_anonymous_comments'
	 *	- 'num_ratings'
	 *	- 'average_rating'
	 */
	function GetThreadById($ThreadId)
	{
		assert('is_int($ThreadId)');
		$sql = '
		SELECT '.$this->GetThreadSelectFields('thread').'
		FROM comment_threads as thread
		'.$this->GetThreadSelectJoins('thread').'
		WHERE thread.comment_thread_id = '.$ThreadId;
		
		$query = $this->db->query($sql);
		$thread = $query->result_array();
		return (0 === count($thread)
				? NULL
				: $thread[0]);
	}
	
	/// Get thread information by linking from another table.
	/**
	 * @param $Table string Name of other table.
	 * @param $FieldThreadId string Name of the field linking to the thread.
	 * @param $Keys associative array Values of key fields to identify record.
	 * @return thread_array,NULL Thread information
	 *	- 'thread_id'
	 *	- 'allow_ratings'
	 *	- 'allow_comments'
	 *	- 'allow_anonymous_comments'
	 *	- 'num_ratings'
	 *	- 'average_rating'
	 */
	function GetThreadByLinkTable($Table, $FieldThreadId, $Keys)
	{
		/// @pre @a $Keys shouldn't be empty.
		assert('!empty($Keys)');
		
		$user_id = ($this->user_auth->isLoggedIn ? $this->user_auth->entityId : NULL);
		$table_matches = array();
		foreach ($Keys as $key => $value) {
			$table_matches[] = 'tab.'.$key.' = '.$this->db->escape($value);
		}
		$sql = '
		SELECT '.$this->GetThreadSelectFields('thread').'
		FROM '.$Table.' as tab
		INNER JOIN comment_threads AS thread
			ON thread.comment_thread_id = tab.'.$FieldThreadId.'
		'.$this->GetThreadSelectJoins('thread').'
		WHERE '.implode(' AND ', $table_matches);
		
		$query = $this->db->query($sql);
		$thread = $query->result_array();
		return (0 === count($thread)
				? NULL
				: $thread[0]);
	}
	
	/// Set the user rating.
	/**
	 * @param $ThreadId int ID of the thread.
	 * @param $Value int,NULL Value or NULL to indicate removal.
	 * @param $EntityId int,NULL Id of user.
	 * @return int,FALSE Number of affected rows or FALSE for failure.
	 */
	function SetUserRating($ThreadId, $Value, $EntityId = NULL)
	{
		// Validate entity id
		if (!$this->user_auth->isLoggedIn) {
			return FALSE;
		}
		if (NULL === $EntityId) {
			$EntityId = $this->user_auth->entityId;
		} elseif (is_numeric($EntityId)) {
			$EntityId = (int)$EntityId;
		} else {
			return FALSE;
		}
		$identities = $this->GetAvailableIdentities();
		if (!array_key_exists($EntityId, $identities)) {
			return FALSE;
		}
		
		// Validate value + create query
		if (NULL === $Value) {
			$sql = '
			DELETE FROM comment_ratings
			WHERE comment_rating_comment_thread_id = "'.$ThreadId.'"
			AND   comment_rating_author_entity_id = "'.$EntityId.'"';
		} elseif (is_numeric($Value)) {
			$Value = (int)$Value;
			if (!$this->ValidateRatingValue($Value)) {
				return FALSE;
			}
			$sql = '
			INSERT INTO comment_ratings SET
				comment_rating_comment_thread_id = "'.$ThreadId.'",
				comment_rating_author_entity_id = "'.$EntityId.'",
				comment_rating_value = "'.$Value.'"
			ON DUPLICATE KEY UPDATE
				comment_rating_value = "'.$Value.'"';
		} else {
			return FALSE;
		}
		// Run the query
		$this->db->query($sql);
		return $this->db->affected_rows();
	}
	
	/// Get a comment by comment id
	/**
	 * @param $CommentId  int    Comment identifier.
	 * @param $Subset     string Same subsets as GetCommentByThreadId
	 * @param $Conditions array[string] Extra SQL conditions.
	 * @return Single comment like GetCommentByThreadId
	 */
	function GetCommentByCommentId($CommentId, $Subset = 'visible', $Conditions = array())
	{
		$Conditions[] = 'comments.comment_id = '.$this->db->escape($CommentId);
		$comments = $this->GetCommentsByThreadId(NULL, $Subset, $Conditions);
		
		if (empty($comments)) {
			return NULL;
		} else {
			return $comments[0];
		}
	}

	/// Return the latest comments
	/**
	 *	@param	$NumToGet	int	Number of comments to return
	 *	@author	Chris Travis (cdt502 - ctravis@gmail.com)
	 */
	function GetLatestComments($NumToGet = 10)
	{
		$sql = 'SELECT	comments.comment_id,
						comments.comment_anonymous,
						users.user_firstname,
						users.user_surname,
						articles.article_id,
						content_types.content_type_codename,
						article_contents.article_content_heading,
					(	SELECT		COUNT(*)
						FROM		comments AS article_comments
						WHERE		article_comments.comment_comment_thread_id = articles.article_public_comment_thread_id
						AND			article_comments.comment_post_time <= comments.comment_post_time
					)	AS article_comment_count
				FROM	articles,
						comments,
						users,
						article_contents,
						content_types
				WHERE	comments.comment_deleted = 0
				AND		comments.comment_comment_thread_id = articles.article_public_comment_thread_id
				AND		articles.article_publish_date < CURRENT_TIMESTAMP
				AND		articles.article_pulled = 0
				AND		articles.article_deleted = 0
				AND		articles.article_live_content_id IS NOT NULL
				AND		articles.article_live_content_id = article_contents.article_content_id
				AND		articles.article_content_type_id = content_types.content_type_id
				AND		comments.comment_author_entity_id = users.user_entity_id
				ORDER BY comments.comment_post_time DESC
				LIMIT	0, ?';
		$query = $this->db->query($sql, array($NumToGet));
		return $query->result_array();
	}

	/// Get comments by the thread's id
	/**
	 * @param $ThreadId int    Thread identifier.
	 * @param $Subset   string Subset identifier.
	 *	- 'visible'  Publicly visible comments.
	 *	- 'all'      All comments.
	 *	- 'reported' Reported comments.
	 * @param $Conditions array[string] Extra SQL conditions.
	 * @return array[comment_array] Array of comments, each with:
	 *	- 'comment_id' (int)
	 *	- 'wikitext' (string)
	 *	- 'xhtml' (string)
	 *	- 'post_time' (timestamp)
	 *	- 'reported_count' (int)
	 *	- 'good' (tinyint)
	 *	- 'rating' (int, NULL if no rating)
	 *	- 'author_id' (id, NULL if anonymous)
	 *	- 'author' (name, NULL if anonymous)
	 */
	function GetCommentsByThreadId($ThreadId, $Subset, $Conditions = array())
	{
		static $preset_conditions = array(
			'visible' => array(
				'comments.comment_deleted = FALSE',
			),
			'all' => array(
			),
			'reported' => array(
				'comments.comment_reported_count > 0',
				'comments.comment_good = FALSE',
				'comments.comment_deleted = FALSE',
			),
		);
		static $preset_sorts = array(
			'visible' => 'comments.comment_post_time ASC',
			'all' => 'comments.comment_post_time ASC',
			'reported' => 'comments.comment_reported_count DESC',
		);
		$conditions = array();
		if ($ThreadId !== NULL) {
			$conditions[] = 'thread.comment_thread_id = '.$this->db->escape($ThreadId);
		}
		if (array_key_exists($Subset, $preset_conditions)) {
			$conditions = array_merge($conditions, $preset_conditions[$Subset], $Conditions);
		}
		if (array_key_exists($Subset, $preset_sorts)) {
			$sort_field = $preset_sorts[$Subset];
		} else {
			$sort_field = $preset_sorts['all'];
		}
		$sql = '
		SELECT
			comments.comment_id as comment_id,
			comments.comment_comment_thread_id as thread_id,
			comments.comment_content_wikitext AS wikitext,
			comments.comment_content_xhtml AS xhtml,
			UNIX_TIMESTAMP(comments.comment_post_time) AS post_time,
			comments.comment_reported_count AS reported_count,
			comments.comment_deleted AS deleted,
			comments.comment_good AS good,
			comment_ratings.comment_rating_value AS rating,
			IF (comments.comment_anonymous = TRUE
					AND thread.comment_thread_allow_anonymous_comments = TRUE,
				NULL,
				comments.comment_author_entity_id) AS author_id,
			IF (comments.comment_anonymous = TRUE
					AND thread.comment_thread_allow_anonymous_comments = TRUE,
				NULL,
				IF (users.user_entity_id IS NOT NULL,
					CONCAT(users.user_firstname," ",users.user_surname),
					IF (organisations.organisation_entity_id IS NOT NULL,
						organisations.organisation_name,
						NULL))) AS author
		FROM comments
		INNER JOIN comment_threads AS thread
			ON comments.comment_comment_thread_id = thread.comment_thread_id
		LEFT JOIN comment_ratings
			ON	comment_ratings.comment_rating_comment_thread_id = comments.comment_comment_thread_id
			AND	comment_ratings.comment_rating_author_entity_id = comments.comment_author_entity_id
		LEFT JOIN users
			ON	NOT comments.comment_anonymous
			AND	users.user_entity_id = comments.comment_author_entity_id
		LEFT JOIN organisations
			ON	NOT comments.comment_anonymous
			AND	organisations.organisation_entity_id = comments.comment_author_entity_id
		WHERE '.implode(' AND ',$conditions).'
		ORDER BY '.$sort_field;
		
		$query = $this->db->query($sql);
		$comments = $query->result_array();
		
		$comments = $this->PostprocessComments($comments);
		
		return $comments;
	}
	
	/// Create a preview comment.
	/**
	 * @param $Comment array Details about the comment.
	 *	- 'wikitext' (string)
	 *	- 'anonymous' (bool, optional = FALSE)
	 *	- 'author_id' (int, optional = user_id)
	 * @pre User is allowed to post a comment on this thread (logged in).
	 * @return comment_array,NULL Comment preview with:
	 *	- 'comment_id' (int)
	 *	- 'wikitext' (string)
	 *	- 'xhtml' (string)
	 *	- 'post_time' (timestamp)
	 *	- 'reported_count' (int)
	 *	- 'good' (tinyint)
	 *	- 'rating' (int, NULL if no rating)
	 *	- 'author_id' (id, NULL if anonymous)
	 *	- 'author' (name, NULL if anonymous)
	 */
	function GetCommentPreview($Comment)
	{
		/// @pre @a $Comment must contain wikitext.
		assert('array_key_exists(\'wikitext\',$Comment)');
		$identities = $this->GetAvailableIdentities();
		if (array_key_exists('author_id', $Comment)) {
			/// @pre @a $Comment['author_id'] must be integral if it exists.
			assert('is_int($Comment[\'author_id\'])');
			if (!array_key_exists($Comment['author_id'], $identities)) {
				/// @note If the specified user isn't the logged in user, returns 0.
				return NULL;
			}
		} else {
			$Comment['author_id'] = $this->user_auth->entityId;
		}
		
		$xhtml = $this->ParseCommentWikitext($Comment['wikitext']);
		return array(
			'comment_id' => 'preview',
			'wikitext' => $Comment['wikitext'],
			'xhtml' => $xhtml,
			'post_time' => time(),
			'reported_count' => FALSE,
			'deleted' => FALSE,
			'good' => FALSE,
			'rating' => NULL,
			'author_id' => $Comment['anonymous'] ? NULL : $Comment['author_id'],
			'author' => $Comment['anonymous'] ? NULL : $identities[$Comment['author_id']],
		);
	}
	
	/// Add a comment to a thread.
	/**
	 * @param $ThreadId int ID of thread.
	 * @param $Comment array Details about the comment.
	 *	- 'wikitext' (string)
	 *	- 'anonymous' (bool, optional = FALSE)
	 *	- 'author_id' (int, optional = user_id)
	 * @return int Affected rows.
	 */
	function AddCommentByThreadId($ThreadId, $Comment)
	{
		static $translation = array(
			'wikitext'  => 'comment_content_wikitext',
			'anonymous' => 'comment_anonymous',
			'author_id'   => 'comment_author_entity_id',
 		);
		/// @pre @a $ThreadId must be integral.
		assert('is_int($ThreadId)');
		/// @pre @a $Comment must contain wikitext.
		assert('array_key_exists(\'wikitext\',$Comment)');
		if (array_key_exists('author_id', $Comment)) {
			/// @pre @a $Comment['author_id'] must be integral if it exists.
			assert('is_int($Comment[\'author_id\'])');
			if (!array_key_exists($Comment['author_id'], $this->GetAvailableIdentities())) {
				/// @note If the specified user isn't the logged in user, returns 0.
				return 0;
			}
		}
		/// @pre User is allowed to post a comment on this thread (logged in).
		assert('$this->user_auth->isLoggedIn || array_key_exists(\'author_id\',$Comment)');
		
		$setters = array(
			'comment_author_entity_id'  => 'comment_author_entity_id = '.$this->user_auth->entityId,
			'comment_comment_thread_id' => 'comment_comment_thread_id = '.$ThreadId,
		);
		foreach ($Comment as $key => $value) {
			if (array_key_exists($key, $translation)) {
				$setters[$translation[$key]] = $translation[$key].' = '.$this->db->escape($value);
			}
		}
		$xhtml = $this->ParseCommentWikitext($Comment['wikitext']);
		$setters['xhtml'] = 'comment_content_xhtml = '.$this->db->escape($xhtml);
		$sql = 'INSERT INTO comments SET '.implode(',', $setters);
		$this->db->query($sql);
		return $this->db->affected_rows();
	}
	
	/// Report a comment to the moderators.
	/**
	 * @param $CommentId int ID of the comment.
	 * @param $ThreadId int ID of the thread.
	 * @return int Number of affected rows.
	 */
	function ReportCommentInThread($CommentId, $ThreadId)
	{
		/// @pre is_int(@a $CommentId)
		assert('is_int($CommentId)');
		/// @pre is_int(@a $ThreadId)
		assert('is_int($ThreadId)');
		$sql = '
		UPDATE comments
		SET comments.comment_reported_count = comments.comment_reported_count + 1
		WHERE	comments.comment_id = "'.$CommentId.'"
		AND		comments.comment_comment_thread_id = "'.$ThreadId.'"';
		$this->db->query($sql);
		return $this->db->affected_rows();
	}
	
	/// Confirms the okayness of a comment by a mod.
	/**
	 * @param $CommentId int ID of the comment.
	 * @return int Number of affected rows.
	 */
	function GoodenComment($CommentId, $Good = TRUE)
	{
		/// @pre is_int(@a $CommentId)
		assert('is_int($CommentId)');
		$sql = '
		UPDATE comments
		SET comments.comment_good = '.($Good?'TRUE':'FALSE').'
		WHERE	comments.comment_id = "'.$CommentId.'"';
		$this->db->query($sql);
		return $this->db->affected_rows();
	}
	
	/// Delete a comment in a thread.
	/**
	 * @param $CommentId int ID of the comment.
	 * @return int Number of affected rows.
	 */
	function DeleteComment($CommentId, $Deleted = TRUE)
	{
		/// @pre is_int(@a $CommentId)
		assert('is_int($CommentId)');
		$sql = '
		UPDATE comments
		SET comments.comment_deleted = '.($Deleted?'TRUE':'FALSE').'
		WHERE	comments.comment_id = "'.$CommentId.'"';
		$this->db->query($sql);
		return $this->db->affected_rows();
	}
	
	/// Flush the wiktiext cache on comments.
	/**
	 * @return Number of rows affected.
	 */
	function FlushWikitextCache()
	{
		$sql = 'UPDATE comments SET comments.comment_content_xhtml = NULL';
		$this->db->query($sql);
		return $this->db->affected_rows();
	}
	
	/// Process comments from the database, filling in a few extra fields.
	/**
	 * @param $Comments array of comments from database.
	 * @return @a $Comments.
	 *
	 * - Comment wikitext caching.
	 * - 'owned' bool Whether the current user owns the comment.
	 */
	function PostprocessComments($Comments)
	{
		$identities = $this->GetAvailableIdentities();
		foreach ($Comments as $key => $comment) {
			if (NULL === $comment['xhtml']) {
				// uncached
				$xhtml = $this->ParseCommentWikitext($comment['wikitext']);
				$Comments[$key]['xhtml'] = $xhtml;
				
				// try updating the cache
				$cache_sql = '
				UPDATE comments
				SET comments.comment_content_xhtml = '.$this->db->escape($xhtml).'
				WHERE comments.comment_id = '.$comment['comment_id'];
				$this->db->query($cache_sql);
			}
			$Comments[$key]['owned'] = array_key_exists($comment['author_id'], $identities);
		}
		return $Comments;
	}
	
	/// Get an array of identities that comments can be posted from.
	/**
	 * @return array[int => string] Array of identities indexed by entity id.
	 */
	function GetAvailableIdentities()
	{
		/// @todo If vip get teams as well as organisation.
		/// @todo Cache the result as may be called multiple times
		$identities = array();
		if ($this->user_auth->isLoggedIn) {
			$identities[$this->user_auth->entityId] = $this->user_auth->firstname.' '.$this->user_auth->surname;
			if ($this->user_auth->organisationLogin >= 0) {
				$identities[$this->user_auth->organisationLogin] = $this->user_auth->organisationName;
			}
		}
		return $identities;
	}
	
	/// Find whether a rating value is valid.
	/**
	 * @param $Value int Rating.
	 * @return bool Whether @a $Value is a valid rating.
	 */
	function ValidateRatingValue($Value)
	{
		return is_int($Value) && $Value > 0 && $Value <= 10;
	}
	
	/// Parse the wikitext for a comment
	/**
	 * @param $Wikitext string Wikitext to parse.
	 * @return string XHTML processed wikitext.
	 */
	function ParseCommentWikitext($Wikitext)
	{
		static $stuff_loaded = FALSE;
		if (!$stuff_loaded) {
			$this->load->library('comments_parser');
			$this->load->helper('wikitext_smiley');
			$stuff_loaded = TRUE;
		}
		$Wikitext = wikitext_parse_smileys($Wikitext);
		$Wikitext = $this->comments_parser->parse($Wikitext."\n",'comment');
		return $Wikitext;
	}
}

?>