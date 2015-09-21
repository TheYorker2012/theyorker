<?php
/*************************************************
 * Yorker Homepage Model
 * Original Authors - Andrew Oakley and Alex Fargus
 * Modified by Richard Crosby
 *
 * A few functions for controlling dynamic content on the homepage
 *
 ************************************************/
class Home_Model extends Model {

	
	/*
	 * Function to obtain a random banner image for today.
	 *@param string name of the homepage section you want a banner for this is the homepage's content_type codename
	 * Returns the image, if there is one.
	 */
	function GetBannerImageForHomepage($homepage_codename='home') 
	{
		$this->load->library('image');
		//Get id of content_type from codename
		//Get image id(s) from homepage_banners table with content_type id
		//Use image id(s) to get all image information
		//Take the one with the current date (if there is one)
		$sql = 'SELECT images.image_id AS id, image_types.image_type_codename AS type, homepage_banners.homepage_banner_link as link
		FROM images
		INNER JOIN image_types ON image_types.image_type_id = images.image_image_type_id 
		INNER JOIN homepage_banners ON images.image_id = homepage_banners.homepage_banner_image_id 
		INNER JOIN content_types ON homepage_banners.homepage_banner_content_type_id = content_types.content_type_id 
		WHERE content_types.content_type_codename = ? 
		AND	DATE(images.image_last_displayed_timestamp) = CURRENT_DATE()';
		$query = $this->db->query($sql, array($homepage_codename));
		//Update current homepage if there is no result
		if($query->num_rows() == 0){
			//Find the oldest homepage image
			$sql = 'SELECT images.image_id AS id, image_types.image_type_codename AS type, homepage_banners.homepage_banner_link as link
			FROM images
			INNER JOIN image_types ON image_types.image_type_id = images.image_image_type_id 
			INNER JOIN homepage_banners ON images.image_id = homepage_banners.homepage_banner_image_id 
			INNER JOIN content_types ON homepage_banners.homepage_banner_content_type_id = content_types.content_type_id 
			WHERE content_types.content_type_codename = ? 
			ORDER BY images.image_last_displayed_timestamp LIMIT 0,1';
			$query = $this->db->query($sql, array($homepage_codename));
			//Update the oldest with the current time&date if there is one
			if($query->num_rows() > 0){
				$sql = 'UPDATE images
					SET images.image_last_displayed_timestamp = CURRENT_TIMESTAMP()
					WHERE images.image_id = ?';
				$update = $this->db->query($sql,array($query->row()->id));
			}
		}
		//Just make sure one has now been found.
		if($query->num_rows() > 0){
			$id = $query->row()->id;
			$type = $query->row()->type;
			$link = $query->row()->link;
			return array(
					'id'=>$id,
					'image'=>$this->image->getImage($id,$type),
					'link'=>$link,
					);
		}else{
			//no homepage found!!
			return "";
		}
	}

	function ignore ($articles)
	{
		if (is_array($articles)) {
			foreach ($articles as $a) {
				$this->ignoreArticles[] = $a['id'];
			}
		} else {
			$this->ignoreArticles[] = $articles;
		}
	}

	function getArticlesByTags ($tags, $number = 4, $ignore_articles = array())
	{
		if (!empty($ignore_articles)) {
			if (!is_array($ignore_articles)) {
				$ignore_articles = array($ignore_articles);
			}
		} else {
			$ignore_articles = $this->ignoreArticles;
		}

		$params = $tags;
		$inputs = array_fill(0, count($tags), '?');
		$params[] = count($tags);
		$params[] = $number;
		$sql = 'SELECT		a.article_id AS id,
							UNIX_TIMESTAMP(a.article_publish_date) AS date,
							ac.article_content_heading AS headline,
							ac.article_content_blurb AS blurb,
							pr.photo_request_chosen_photo_id AS photo_id,
							pr.photo_request_title AS photo_title,
							COUNT(tags.tag_id) AS tag_count
				FROM		articles AS a,
							article_contents AS ac,
							article_tags,
							tags,
							photo_requests AS pr
				WHERE		a.article_id = article_tags.article_tag_article_id
				AND			article_tags.article_tag_tag_id = tags.tag_id
				AND			tags.tag_name IN (' . implode(',', $inputs) . ')
				AND			UNIX_TIMESTAMP(a.article_publish_date) < UNIX_TIMESTAMP()
				AND			a.article_pulled = 0
				AND			a.article_deleted = 0
				AND			a.article_live_content_id IS NOT NULL
				AND			ac.article_content_id = a.article_live_content_id
				AND			pr.photo_request_article_id = a.article_id
				AND			a.article_thumbnail_photo_id = pr.photo_request_relative_photo_number ';
		if (!empty($ignore_articles)) {
			$sql .= 'AND a.article_id NOT IN (' . implode(',', $ignore_articles) . ')';
		}
		$sql .= '
				GROUP BY	a.article_id
				HAVING		tag_count = ?
				ORDER BY	a.article_publish_date DESC
				LIMIT		0, ?';
		$query = $this->db->query($sql, $params);
		return $query->result_array();
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

	function getArticleTitles($articles, $dateFormat) {
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
				DATE_FORMAT(
					articles.article_publish_date,
					?)
					AS date,
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

		$query = $this->db->query($sql, array($dateFormat));
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
?>
