<?php

/**
 *	@brief		Temporary functionality for Roses 2008
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 *	@date		30th April 2008
 *	@todo		Disable / Delete as appropriate after Roses weekend
 */

class Roses_model extends Model
{
	function Roses_model ()
	{
		parent::Model();
	}

	function enterComp ($poll_id, $user_id, $york_score, $lancs_score)
	{
		$sql = 'INSERT INTO	poll_votes
			SET		poll_vote_poll_id = ?,
					poll_vote_user_id = ?,
					poll_vote_choice_text = ?';
		$query = $this->db->query($sql, array($poll_id, $user_id, $york_score . '-' . $lancs_score));
	}

	function getAllResults()
	{
		$sql = 'SELECT		UNIX_TIMESTAMP(event_time) AS event_time,
					event_id,
					event_sport,
					event_name,
					event_venue,
					event_points,
					event_lancaster_score,
					event_york_score,
					event_score_time AS event_score_time
			FROM		roses_scores2011
			ORDER BY	event_time ASC,
					event_sport ASC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getResult ($event_id)
	{
		$sql = 'SELECT	event_id,
						UNIX_TIMESTAMP(event_time) AS event_time,
						event_sport,
						event_name,
						event_venue,
						event_points,
						event_score_time,
						event_blog_entry_id
				FROM	roses_scores2011
				WHERE	event_id = ?';
		$query = $this->db->query($sql, array($event_id));
		if ($query->num_rows() == 1)
			return $query->row();
		return NULL;
	}

	function setResult ($event_id, $score_lancs, $score_york, $score_time)
	{
		$sql = 'UPDATE	roses_scores2011
				SET		event_lancaster_score = ?,
						event_york_score = ?,
						event_score_time = ?
				WHERE	event_id = ?';
		$query = $this->db->query($sql, array($score_lancs, $score_york, $score_time, $event_id));
	}

	function addBlogEntry ($article_id, $wikitext, $cache, $user_id)
	{
		$sql = 'INSERT INTO	article_liveblog
				SET			article_liveblog_article_id = ?,
							article_liveblog_wikitext = ?,
							article_liveblog_wikitext_cache = ?,
							article_liveblog_user_entity_id = ?';
		$query = $this->db->query($sql, array($article_id, $wikitext, $cache, $user_id));
		return $this->db->insert_id();
	}

	function noteScoreBlogEntry($event_id, $entry_id)
	{
		$sql = 'UPDATE		roses_scores
				SET			event_blog_entry_id = ?
				WHERE		event_id = ?';
		$this->db->query($sql, array($entry_id, $event_id));
	}

	function updateBlogEntry ($blog_id, $wikitext, $cache, $user_id)
	{
		$sql = 'UPDATE		article_liveblog
				SET			article_liveblog_wikitext = ?,
							article_liveblog_wikitext_cache = ?,
							article_liveblog_user_entity_id = ?
				WHERE		article_liveblog_id = ?';
		$query = $this->db->query($sql, array($wikitext, $cache, $user_id, $blog_id));
	}

	function deleteBlogEntry ($entry_id)
	{
		$sql = 'UPDATE		article_liveblog
				SET			article_liveblog_deleted = 1
				WHERE		article_liveblog_id = ?';
		$this->db->query($sql, array($entry_id));
	}

	function isLiveBlog ($article_id)
	{
		$sql = 'SELECT		article_id
				FROM		articles
				WHERE		article_id = ?
				AND			article_liveblog = 1';
		return $this->db->query($sql, array($article_id))->num_rows();
	}

	function getLiveBlog ($article_id)
	{
		$sql = 'SELECT		article_liveblog.article_liveblog_id,
							article_liveblog.article_liveblog_wikitext,
							article_liveblog.article_liveblog_wikitext_cache,
							article_liveblog.article_liveblog_user_entity_id,
							UNIX_TIMESTAMP(article_liveblog.article_liveblog_posted_time) AS article_liveblog_posted_time,
							users.user_firstname,
							users.user_surname
				FROM		article_liveblog,
							users
				WHERE		article_liveblog.article_liveblog_article_id = ?
				AND			article_liveblog.article_liveblog_deleted = 0
				AND			article_liveblog.article_liveblog_user_entity_id = users.user_entity_id
				ORDER BY	article_liveblog.article_liveblog_posted_time DESC';
		$query = $this->db->query($sql, array($article_id));
		$result = array();
		$result['rows'] = array();
		$result['all'] = array();
		$result['all']['wikitext'] = '';
		$result['all']['cache'] = '';
		foreach($query->result() as $row) {
			$result['rows'][] = $row;
			$result['all']['wikitext'] .= $row->article_liveblog_wikitext . "\n\n";
			$result['all']['cache'] .= $row->article_liveblog_wikitext_cache;
		}
		return $result;
	}

	function getPublishDate ($article_id)
	{
		$sql = 'SELECT		articles.article_publish_date
				FROM		articles
				WHERE		articles.article_id = ?';
		return $this->db->query($sql, array($article_id))->row()->article_publish_date;
	}

}
?>
