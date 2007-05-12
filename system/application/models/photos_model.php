<?php
/**
 *		Model for photo requests
 *
 *		@author	Chris Travis (cdt502 - ctravis@gmail.com)
 */

//TODO - prevent errors if no data present -> DONE?
//		 comment correctly
//		 article_breaking?
//		 optimisation

class Photos_model extends Model
{

	function Photos_Model()
	{
		parent::Model();
	}


	function GetAllOpenPhotoRequests()
	{
		$result['unassigned'] = array();
		$result['assigned'] = array();
		$result['ready'] = array();
		$sql = 'SELECT photo_requests.photo_request_id,
					UNIX_TIMESTAMP(photo_requests.photo_request_timestamp) AS photo_request_timestamp,
					photo_requests.photo_request_title,
					photo_requests.photo_request_flagged
				FROM photo_requests
				WHERE photo_requests.photo_request_deleted = 0
				AND photo_requests.photo_request_chosen_photo_id IS NULL
				ORDER BY photo_requests.photo_request_timestamp DESC';
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$request = array(
					'id'			=>	$row->photo_request_id,
					'title'		=>	$row->photo_request_title,
					'time'		=>	$row->photo_request_timestamp
				);
				$user_sql = 'SELECT photo_request_users.photo_request_user_user_entity_id,
						users.user_firstname,
						users.user_surname
					FROM photo_request_users, users
					WHERE photo_request_users.photo_request_user_status != \'declined\'
					AND photo_request_users.photo_request_user_user_entity_id = users.user_entity_id
					AND photo_request_users.photo_request_user_photo_request_id = ?';
				$user_query = $this->db->query($user_sql, array($row->photo_request_id));
				if ($user_query->num_rows() == 0) {
					$result['unassigned'][] = $request;
				} else {
					$user_row = $user_query->row();
					$request['user_name'] = $user_row->user_firstname . ' ' . $user_row->user_surname;
					$request['user_id'] = $user_row->photo_request_user_user_entity_id;
					if ($row->photo_request_flagged) {
						$result['ready'][] = $request;
					} else {
						$result['assigned'][] = $request;
					}
				}
			}
		}
		return $result;
	}


	function GetLatestId($type, $number)
	//Returns the '$number' most recent article ID of type '$type'
	//Ordered by 'most recent'.
	{
		$sql = 'SELECT content_type_id, content_type_has_children
				FROM content_types
				WHERE content_type_codename = ?';
		$query = $this->db->query($sql,array($type));
		$row = $query->row();
		if (($query->num_rows() > 0) && ($row->content_type_has_children)) {
			$sql = 'SELECT content_type_codename
					FROM content_types
					WHERE content_type_parent_content_type_id = ?';
			$query = $this->db->query($sql,array($row->content_type_id));
			$types = array();
			if ($query->num_rows() > 0) {
				foreach ($query->result() as $row) {
					$types[] = $row->content_type_codename;
				}
			}
		} else {
			$types = array($type);
		}

		$sql = 'SELECT articles.article_id FROM articles
				LEFT JOIN content_types
				ON (content_types.content_type_id = articles.article_content_type_id)
				WHERE articles.article_publish_date < CURRENT_TIMESTAMP
				AND articles.article_live_content_id IS NOT NULL
				AND	articles.article_editor_approved_user_entity_id IS NOT NULL ';
		if (!empty($types)) {
			$sql .= '	AND (';
			$first = TRUE;
			foreach ($types as $type) {
				if (!$first) {
					$sql .= ' OR ';
				} else {
					$first = FALSE;
				}
				$sql .= 'content_types.content_type_codename = ?';
			}
			$sql .= ') ';
		}
		$sql .= 'ORDER BY articles.article_publish_date DESC
				LIMIT 0, ?';
		$types[] = $number;
		$query = $this->db->query($sql,$types);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result[] = $row->article_id;
			}
		}
		while (count($result) < $number) {
			$result[] = NULL;
		}
		return $result;
	}

	/**
	 * Get array containing data needed for 'NewsOther'
	 * @param $id is the article_id of the article data to return
	 * @param $dateformat is an optional string containg the format you wish the dates to be returned
	 * @return An array with 'id','date','heading','subheading','subtext',
	 * @return 'authors','photo'
	 */
	function GetSimpleArticle($id, $image_class = "", $dateformat ='%a, %D %b %y')
	{
		$result['id'] = $id;
		$sql = 'SELECT articles.article_live_content_id,
			DATE_FORMAT(articles.article_publish_date, ?) AS article_publish_date,
			content_types.content_type_codename,
			articles.article_thumbnail_photo_id,
			photos.photo_title
			FROM articles
			INNER JOIN content_types
				ON articles.article_id = ? AND articles.article_content_type_id = content_types.content_type_id
			LEFT JOIN photos
				ON articles.article_thumbnail_photo_id = photos.photo_id
			LIMIT 0,1';
		$query = $this->db->query($sql, array($dateformat,$id));
		$content_id = null;
		if ($query->num_rows() > 0)
		{
		    $row = $query->row();
		    $result['date'] = $row->article_publish_date;
			$result['article_type'] = $row->content_type_codename;
		    $content_id = $row->article_live_content_id;

			if ($row->article_thumbnail_photo_id > 0) {
				$this->load->helper('images');
				$result['photo_xhtml'] = imageLocTag($row->article_thumbnail_photo_id, 'small', false, $row->photo_title, $image_class);
			} else {
				$result['photo_xhtml'] = '<img src="/images/prototype/news/small-default.jpg" alt="" class="'.$image_class.'" />';
			}
		}
		$sql = 'SELECT article_contents.article_content_heading
			FROM article_contents
			WHERE (article_contents.article_content_id = ?)
			LIMIT 0,1';
		$query = $this->db->query($sql, array($content_id));
        if ($query->num_rows() > 0)
		{
		    $row = $query->row();
		    $result['heading'] = $row->article_content_heading;
		}
		$sql = 'SELECT article_writers.article_writer_user_entity_id
			FROM article_writers
			WHERE (article_writers.article_writer_article_id = ?
			AND article_writers.article_writer_status = "accepted"
			AND article_writer_editor_accepted_user_entity_id IS NOT NULL)

			LIMIT 0,10';
		$query = $this->db->query($sql, array($id));
		if ($query->num_rows() > 0)
		{
		    $authors = array();
		    foreach ($query->result() as $row)
			{
				$sql = 'SELECT business_cards.business_card_name
					FROM business_cards
					WHERE (business_cards.business_card_user_entity_id = ?)';
				$author_query = $this->db->query($sql,array($row->article_writer_user_entity_id));
				if ($author_query->num_rows() > 0)
				{
					$author_row = $author_query->row();
					$authors[] = array(
						'name' => $author_row->business_card_name,
						'id' => $row->article_writer_user_entity_id
					);
				}
			}
			$result['authors'] = $authors;
		}
		return $result;
	}

}
?>
