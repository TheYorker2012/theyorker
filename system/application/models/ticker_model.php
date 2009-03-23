<?php

/**
 *	@brief		Article information for news ticker
 *	@brief		Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Ticker_Model extends Model {

	function Ticker_Model() {
		parent::Model();
	}

	function getNews ($limit = 5) {
		$sql = 'SELECT		articles.article_id AS id,
							content_types.content_type_section AS section,
							content_types.content_type_codename AS type,
							article_contents.article_content_heading AS headline
				FROM		articles
				INNER JOIN	content_types
					ON		articles.article_content_type_id = content_types.content_type_id
				INNER JOIN	article_contents
					ON		articles.article_live_content_id = article_contents.article_content_id
				WHERE		articles.article_live_content_id IS NOT NULL
				AND			articles.article_pulled = 0
				AND			articles.article_deleted = 0
				AND			articles.article_publish_date < CURRENT_TIMESTAMP
				ORDER BY	articles.article_publish_date DESC
				LIMIT		0, ?';
		$query = $this->db->query($sql, array($limit));
		return $query->result();
	}

}

?>