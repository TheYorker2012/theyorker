<?php
/**
 *	@brief		The Yorker - Article Manager
 *	@version	2.0
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Articlemanager_model extends Model
{

	function __construct()
	{
		parent::Model();
		$this->load->library('wikiparser');
	}

	function isArticle($article_id)
	{
		$sql = 'SELECT		articles.article_id
				FROM		articles
				WHERE		articles.article_id = ?';
		$query = $this->db->query($sql, array($article_id));
		return $query->num_rows();
	}

	function createArticle($user_id)
	{
		$sql = 'INSERT INTO	articles
				SET			articles.article_suggestion_accepted = 0,
							articles.article_request_entity_id = ?';
		$query = $this->db->query($sql, array($user_id));
		return $this->db->insert_id();
	}
}
?>