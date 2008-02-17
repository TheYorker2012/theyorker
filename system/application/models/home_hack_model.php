<?php
/**
 * Model created for a homepage with speed
 * Warning: contains hacks, is one giant hack
 * 
 * Looking back at this, it actually seems less hacky than the proper one.  
 *
 * @author Andrew Oakley (ado500)
 *
 */

class Home_Hack_Model extends Model {
	function Home_Hack_Model() {
		parent::Model();
	}

	function getLatestArticleIds($types) {
		$params = array();
		$result = array();

		$sql_contentTypeIds = '
			SELECT
				child.content_type_id
			FROM 
				content_types
			LEFT JOIN 
				content_types AS child 
			ON
				child.content_type_parent_content_type_id = content_types.content_type_id 
			OR
				child.content_type_id = content_types.content_type_id
			WHERE 
				content_types.content_type_codename = ?';

		$sql_latestArtcileIds = '
			(SELECT
				articles.article_id, 
				? AS content_type
			FROM 
				articles
			WHERE
				articles.article_publish_date < CURRENT_TIMESTAMP 
			AND
				articles.article_live_content_id IS NOT NULL
			AND
				articles.article_editor_approved_user_entity_id	IS NOT NULL
			AND
				articles.article_content_type_id IN ('.$sql_contentTypeIds.')
			AND
				articles.article_deleted = 0
			ORDER BY
				articles.article_publish_date DESC
			LIMIT 0, ?)';

		$sql_requestedArticles = array();
		foreach($types as $type => $number) {
			$result[$type] = array();
			$sql_requestedArticles[] = $sql_latestArtcileIds;
			$params[] = $type;
			$params[] = $type;
			$params[] = $number;
		}
		$sql_requestedArticles = implode(
			' UNION ', 
			$sql_requestedArticles
		);

		$query = $this->db->query($sql_requestedArticles, $params);

		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row)
				$result[$row->content_type][] = $row->article_id;
		}

		return $result;
	}

	function getArticleTitles($articles) {
		if (count($articles) == 0)
			return array();

		$sql_requestedArticles = implode(', ', $articles);
		$sql = '
			SELECT 
				content_types.content_type_codename
					AS article_type,
				articles.article_id
					AS id,
				article_contents.article_content_heading
					AS heading,
				photo_requests.photo_request_chosen_photo_id
					AS photo_id,
				photo_requests.photo_request_title
					AS photo_title
			FROM articles
			INNER JOIN content_types ON
				articles.article_content_type_id = 
					content_types.content_type_id
			INNER JOIN article_contents ON
				article_contents.article_content_id = 
					articles.article_live_content_id
			LEFT JOIN photo_requests ON
				(articles.article_thumbnail_photo_id =
					photo_requests.photo_request_relative_photo_number
				AND photo_requests.photo_request_article_id =
					articles.article_id)
			WHERE
				articles.article_id 
					IN ('.$sql_requestedArticles.')
			ORDER BY
				article_publish_date DESC';

		$query = $this->db->query($sql, array());
		return $query->result_array();
	}

	function getArticleSummaries($articles, $dateFormat) {
		if (count($articles) == 0)
			return array();

		$params = array($dateFormat);

		$sql_requestedArticles = implode(', ', $articles);
		$sql = '
			SELECT
				content_types.content_type_codename
					AS article_type,
				articles.article_id
					AS id,
				article_contents.article_content_heading
					AS heading,
				DATE_FORMAT(
					articles.article_publish_date,
					?)
					AS date,
				article_contents.article_content_blurb
					AS blurb,
				photo_requests.photo_request_chosen_photo_id
					AS photo_id,
				photo_requests.photo_request_title
					AS photo_title
			FROM articles
			INNER JOIN content_types ON
				articles.article_content_type_id =
					content_types.content_type_id
			LEFT JOIN photo_requests ON
				(articles.article_thumbnail_photo_id =
					photo_requests.photo_request_relative_photo_number
				AND photo_requests.photo_request_article_id =
					articles.article_id)
			INNER JOIN article_contents ON
				article_contents.article_content_id =
					articles.article_live_content_id
			WHERE
				articles.article_id
					IN ('.$sql_requestedArticles.')
			AND
				articles.article_deleted = 0
			ORDER BY
				article_publish_date DESC';

		$query = $this->db->query($sql, $params);
		return $query->result_array();
	}

}
