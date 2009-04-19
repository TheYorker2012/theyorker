<?php
/**
 *		Model for photo requests
 *
 *		@author	Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Photos_model extends Model
{

	function Photos_Model()
	{
		parent::Model();
	}

	function GetWatermarkColours ()
	{
		$sql = 'SELECT		photo_watermark_colour_id AS id,
							photo_watermark_colour_name AS name
				FROM		photo_watermark_colours
				ORDER BY	photo_watermark_colour_name ASC';
		$result = array();
		$query = $this->db->query($sql);
		foreach ($query->result() as $row) {
			$result[$row->id] = $row;
		}
		return $result;
	}

	function GetTypeInfo ($type_codename)
	{
		$sql = 'SELECT	image_type_id AS id,
						image_type_name AS name,
						image_type_width AS width,
						image_type_width AS x,
						image_type_height AS height,
						image_type_height AS y,
						image_type_codename AS codename
				FROM	image_types
				WHERE	image_type_codename = ?';
		$query = $this->db->query($sql, array($type_codename));
		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			return false;
		}
	}

	function GetAllTypesInfo ()
	{
		$sql = 'SELECT		image_type_id AS id,
							image_type_name AS name,
							image_type_width AS width,
							image_type_height AS height,
							image_type_codename AS codename
				FROM		image_types
				ORDER BY	image_type_width ASC,
							image_type_height ASC,
							image_type_name ASC';
		return $this->db->query($sql)->result();
	}

	function GetTagDetails ($tag_name)
	{
		$sql = 'SELECT		tags.tag_id,
							tags.tag_name
				FROM		tags
				WHERE		LOWER(tags.tag_name) = LOWER(?)
				AND			tags.tag_type = \'photo\'';
		$query = $this->db->query($sql, array($tag_name));
		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			return false;
		}
	}

	function AddPhotoTag ($tag_name)
	{
		$sql = 'INSERT INTO	tags
				SET			tags.tag_name = LOWER(?),
							tags.tag_type = \'photo\'';
		$query = $this->db->query($sql, array($tag_name));
		return $this->db->insert_id();
	}

	function AssociatePhotoTag ($photo_id, $tag_id)
	{
		$sql = 'INSERT INTO	photo_tags
				SET			photo_tags.photo_tag_photo_id = ?,
							photo_tags.photo_tag_tag_id = ?
				ON DUPLICATE KEY UPDATE photo_tag_tag_id = photo_tag_tag_id';
		$query = $this->db->query($sql, array($photo_id, $tag_id));
	}

	function UpdatePhotoDetails ($photo_id, $title, $source, $watermark, $watermark_colour, $hidden, $hidden_gallery, $public_gallery)
	{
		$sql = 'UPDATE		photos
				SET			photos.photo_title = ?,
							photos.photo_source = ?,
							photos.photo_watermark = ?,
							photos.photo_watermark_colour_id = ?,
							photos.photo_gallery = ?,
							photos.photo_deleted = ?,
							photos.photo_public_gallery = ?
				WHERE		photos.photo_id = ?';
		$query = $this->db->query($sql, array($title, $source, $watermark, $watermark_colour, $hidden_gallery, $hidden, $public_gallery, $photo_id));
	}

	function GetOriginalPhotoProperties ($id)
	{
		$sql = 'SELECT		photos.photo_id AS id,
							UNIX_TIMESTAMP(photos.photo_timestamp) AS timestamp,
							photos.photo_title AS title,
							photos.photo_width AS width,
							photos.photo_height AS height,
							photos.photo_author_user_entity_id AS user_id,
							users.user_firstname AS user_firstname,
							users.user_surname AS user_surname,
							photos.photo_public_gallery AS public_gallery,
							photos.photo_gallery AS gallery,
							photos.photo_complete AS complete,
							photos.photo_deleted AS deleted,
							photos.photo_watermark AS watermark,
							photos.photo_watermark_colour_id AS watermark_colour_id,
							photos.photo_source AS source
				FROM		photos
				LEFT JOIN	users
					ON		photos.photo_author_user_entity_id = users.user_entity_id
				WHERE		photos.photo_id = ?';
		$query = $this->db->query($sql, array($id));
		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			return false;
		}
	}

	function ResetThumbnails ($photo_id)
	{
		$sql = 'UPDATE	photo_thumbs
				SET		photo_thumbs.photo_thumbs_timestamp = NULL
				WHERE	photo_thumbs.photo_thumbs_photo_id = ?';
		$query = $this->db->query($sql, array($photo_id));
	}

	function GetWatermark ($photo_id, $type_id)
	{
		$sql = 'SELECT	photos.photo_watermark AS watermark,
						photo_watermark_colours.photo_watermark_colour_red AS red,
						photo_watermark_colours.photo_watermark_colour_blue AS blue,
						photo_watermark_colours.photo_watermark_colour_green AS green
				FROM	photos,
						photo_watermark_colours,
						image_types
				WHERE	photos.photo_id = ?
				AND		photos.photo_watermark != ""
				AND		photos.photo_watermark_colour_id = photo_watermark_colours.photo_watermark_colour_id
				AND		image_types.image_type_id = ?
				AND		image_types.image_type_watermark = 1';
		$query = $this->db->query($sql, array($photo_id, $type_id));
		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			return false;
		}
	}

	function GetThumbnail ($id, $type)
	{
		$sql = 'SELECT	photo_thumbs.photo_thumbs_data AS data,
						photos.photo_mime AS mime,
						photo_thumbs.photo_thumbs_coord_x AS x,
						photo_thumbs.photo_thumbs_coord_y AS y,
						photo_thumbs.photo_thumbs_width AS width,
						photo_thumbs.photo_thumbs_height AS height,
						UNIX_TIMESTAMP(photo_thumbs.photo_thumbs_timestamp) AS timestamp
				FROM	photo_thumbs,
						photos
				WHERE	photo_thumbs.photo_thumbs_photo_id = ?
				AND		photo_thumbs.photo_thumbs_photo_id = photos.photo_id
				AND		photo_thumbs.photo_thumbs_image_type_id = ?';
		$query = $this->db->query($sql, array($id, $type));
		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			return false;
		}
	}

	function GetFullPhoto ($id)
	{
		$sql = 'SELECT	photo_mime AS mime,
						photo_data AS data,
						UNIX_TIMESTAMP(photo_timestamp) AS timestamp
				FROM	photos
				WHERE	photo_id = ?';
		$query = $this->db->query($sql, array($id));
		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			return false;
		}
	}

	function GetErrorImage ($type)
	{
		$sql = 'SELECT	image_type_error_mime AS mime,
						image_type_error_data AS data,
						UNIX_TIMESTAMP() AS timestamp
				FROM	image_types WHERE image_type_codename = ?';
		$query = $this->db->query($sql, array($type));
		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			return false;
		}
	}

	function GallerySearch ($terms, $current_page, $per_page, $count = false)
	{
		$conditions = array();
		$params = array();
		foreach ($terms as $term) {
			$conditions[] = 'LOWER(photos.photo_title) LIKE CONCAT("%", CONCAT(LOWER(?), "%"))';
			$params[] = $term;
			$conditions[] = 'LOWER(tags.tag_name) = LOWER(?)';
			$params[] = $term;
		}
		if (count($conditions) > 0) {
			$conditions = 'AND (' . implode(' OR ', $conditions) . ')';
		} else {
			$conditions = '';
		}
		if ($count) {
			$fields = array('COUNT(*) AS total_rows');
		} else {
			$fields = array(
				'photos.photo_id',
				'photos.photo_timestamp',
				'photos.photo_author_user_entity_id',
				'photos.photo_title',
				'photos.photo_width',
				'photos.photo_height',
				'photos.photo_gallery',
				'photos.photo_complete',
				'photos.photo_deleted'
			);
		}
		$sql = 'SELECT		' . implode(', ', $fields) . '
				FROM		photos
				LEFT JOIN	photo_tags
					ON		photos.photo_id = photo_tags.photo_tag_photo_id
				LEFT JOIN	tags
					ON	(	photo_tags.photo_tag_tag_id = tags.tag_id
						AND	tags.tag_type = "photo"
						AND	tags.tag_deleted = 0
						)
				WHERE		photos.photo_deleted = 0
				' . $conditions . '
				GROUP BY	photos.photo_id
				ORDER BY	photos.photo_timestamp DESC';
		if (!$count) {
			$sql .= ' LIMIT		?, ?';
			$params[] = (int)$current_page;
			$params[] = (int)$per_page;
		}
		$query = $this->db->query($sql, $params);
		if ($count) {
			return $query->num_rows();
		} else {
			return $query->result();
		}
	}

	// Functions specifically for photo requests listed below

	function GetMyRequests($user_id)
	{
		$result = array();
		$sql = 'SELECT		photo_requests.photo_request_id								AS	id,
				 			UNIX_TIMESTAMP(photo_requests.photo_request_timestamp)		AS	created,
							UNIX_TIMESTAMP(articles.article_publish_date)				AS	deadline,
							photo_requests.photo_request_title							AS	title,
							users.user_firstname,
							users.user_surname,
							photo_request_users.photo_request_user_user_entity_id		AS	photographer_id,
							photo_request_users.photo_request_user_status				AS	photographer_status
				FROM		articles,
							photo_requests,
							photo_request_users,
							users
				WHERE		photo_request_users.photo_request_user_user_entity_id = ?
				AND			photo_request_users.photo_request_user_photo_request_id = photo_requests.photo_request_id
				AND			photo_request_users.photo_request_user_status != "declined"
				AND			photo_request_users.photo_request_user_user_entity_id = users.user_entity_id
				AND			photo_requests.photo_request_article_id = articles.article_id
				AND			photo_requests.photo_request_chosen_photo_id IS NULL
				AND			photo_requests.photo_request_approved_user_entity_id IS NULL
				AND			photo_requests.photo_request_flagged = 0
				AND			photo_requests.photo_request_deleted = 0
				ORDER BY	articles.article_publish_date ASC';
		$query = $this->db->query($sql,array($user_id));
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$result[] = array(
					'id'		=>	$row->id,
					'created'	=>	$row->created,
					'deadline'	=>	$row->deadline,
					'title'		=>	$row->title,
					'box'		=>	'Photos',
					'type'		=>	'photo',
					'reporters' =>	array(
										array(
											'id'		=>	$row->photographer_id,
											'byline_id'	=>	NULL,
											'name'		=>	$row->user_firstname . ' ' . $row->user_surname,
											'status'	=>	$row->photographer_status
										)
									)
				);
			}
		}
		return $result;
	}


	function GetAllOpenPhotoRequests()
	{
		$result['unassigned'] = array();
		$result['assigned'] = array();
		$result['ready'] = array();
		$sql = 'SELECT		photo_requests.photo_request_id,
							UNIX_TIMESTAMP(photo_requests.photo_request_timestamp) AS photo_request_timestamp,
							photo_requests.photo_request_title,
							photo_requests.photo_request_flagged,
							UNIX_TIMESTAMP(articles.article_publish_date) AS article_publish_date
				FROM		photo_requests, articles
				WHERE		photo_requests.photo_request_deleted = 0
				AND			photo_requests.photo_request_chosen_photo_id IS NULL
				AND			photo_requests.photo_request_article_id = articles.article_id
				ORDER BY	articles.article_publish_date ASC';
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$request = array(
					'id'		=>	$row->photo_request_id,
					'title'		=>	$row->photo_request_title,
					'time'		=>	$row->photo_request_timestamp,
					'deadline'	=>	$row->article_publish_date
				);
				$user_sql = 'SELECT		photo_request_users.photo_request_user_user_entity_id,
										photo_request_users.photo_request_user_status,
										users.user_firstname,
										users.user_surname
							FROM		photo_request_users, users
							WHERE		photo_request_users.photo_request_user_status != \'declined\'
							AND			photo_request_users.photo_request_user_user_entity_id = users.user_entity_id
							AND			photo_request_users.photo_request_user_photo_request_id = ?';
				$user_query = $this->db->query($user_sql, array($row->photo_request_id));
				if ($user_query->num_rows() == 0) {
					$request['user_name'] = '';
					$request['user_status'] = '';
					$request['user_id'] = '';
					$result['unassigned'][] = $request;
				} else {
					$user_row = $user_query->row();
					$request['user_name'] = $user_row->user_firstname . ' ' . $user_row->user_surname;
					$request['user_status'] = $user_row->photo_request_user_status;
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

	function GetThumbnailTypes ()
	{
		$sql = 'SELECT		image_type_id AS id,
							image_type_name AS name
				FROM		image_types
				WHERE		image_type_photo_thumbnail = 1
				ORDER BY	image_type_width ASC,
							image_type_height ASC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function GetPhotoRequestsForArticle($article_id)
	{
		$sql = 'SELECT		photo_request_id AS request_id,
							photo_request_relative_photo_number AS photo_number,
							photo_request_chosen_photo_id AS photo_id,
							photo_request_image_type_id AS photo_type,
							photo_request_title AS photo_caption,
							photo_request_description AS photo_alt
				FROM		photo_requests
				WHERE		photo_request_article_id = ?
				ORDER BY	photo_request_relative_photo_number ASC';
		$query = $this->db->query($sql, array($article_id));
		return $query->result_array();
	}

	function GetPhotoRequestDetails($id)
	{
		$sql = 'SELECT photo_requests.photo_request_title,
				photo_requests.photo_request_description,
				photo_requests.photo_request_article_id,
				UNIX_TIMESTAMP(photo_requests.photo_request_timestamp) AS photo_request_timestamp,
				photo_requests.photo_request_user_entity_id,
				photo_requests.photo_request_approved_user_entity_id,
				articles.article_request_title,
				users.user_firstname,
				users.user_surname,
				photo_requests.photo_request_deleted,
				photo_requests.photo_request_chosen_photo_id,
				photo_requests.photo_request_flagged,
				photo_requests.photo_request_comment_thread_id,
				photo_requests.photo_request_relative_photo_number
			FROM photo_requests, articles, users
			WHERE photo_requests.photo_request_id = ?
			AND photo_requests.photo_request_article_id = articles.article_id
			AND photo_requests.photo_request_user_entity_id = users.user_entity_id';
		$query = $this->db->query($sql, array($id));
		if ($query->num_rows() == 0) {
			return FALSE;
		} else {
			$row = $query->row();
	      	$result = array(
					'id'				=>	$id,
					'title'				=>	$row->photo_request_title,
					'description'		=>	$row->photo_request_description,
					'article_id'		=>	$row->photo_request_article_id,
					'article_title'		=>	$row->article_request_title,
					'time'				=>	$row->photo_request_timestamp,
					'reporter_id'		=>	$row->photo_request_user_entity_id,
					'reporter_name'		=>	$row->user_firstname . ' ' . $row->user_surname,
					'editor_id'			=>	$row->photo_request_approved_user_entity_id,
					'comments_thread'	=>	$row->photo_request_comment_thread_id,
					'chosen_photo'		=>	$row->photo_request_chosen_photo_id,
					'photo_number'		=>	$row->photo_request_relative_photo_number
	      	);
	      	if ($row->photo_request_approved_user_entity_id !== NULL) {
				$editor_sql = 'SELECT users.user_firstname, users.user_surname
							FROM users
							WHERE users.user_entity_id = ?';
				$editor_query = $this->db->query($editor_sql,array($row->photo_request_approved_user_entity_id));
				$editor_row = $editor_query->row();
				$result['editor_name'] = $editor_row->user_firstname . ' ' . $editor_row->user_surname;
			}
			$user_sql = 'SELECT photo_request_users.photo_request_user_user_entity_id,
					photo_request_users.photo_request_user_status,
					users.user_firstname,
					users.user_surname
				FROM photo_request_users, users
				WHERE photo_request_users.photo_request_user_user_entity_id = users.user_entity_id
				AND photo_request_users.photo_request_user_photo_request_id = ?';
			$user_query = $this->db->query($user_sql, array($id));
			$result['status'] = 'unassigned';
			$result['assigned_status'] = 'unassigned';
			if ($user_query->num_rows() > 0) {
				$user_row = $user_query->row();
				$result['assigned_name'] = $user_row->user_firstname . ' ' . $user_row->user_surname;
				$result['assigned_id'] = $user_row->photo_request_user_user_entity_id;
				$result['assigned_status'] = $user_row->photo_request_user_status;
				if ($result['assigned_status'] != 'declined') {
					$result['status'] = 'assigned';
				}
			}
			if ($row->photo_request_deleted) {
				$result['status'] = 'deleted';
			} elseif ($row->photo_request_chosen_photo_id !== NULL) {
				$result['status'] = 'completed';
			} elseif ($row->photo_request_flagged) {
				$result['status'] = 'ready';
			}
	      	return $result;
		}
	}

	function GetSuggestedPhotos($id)
	{
		$result = array();
		$sql = 'SELECT photo_request_photos.photo_request_photo_photo_id,
					photo_request_photos.photo_request_photo_comment,
					UNIX_TIMESTAMP(photo_request_photos.photo_request_photo_date) AS photo_request_photo_date,
					photo_request_photos.photo_request_photo_user_id,
					users.user_firstname,
					users.user_surname
				FROM photo_request_photos, users
				WHERE photo_request_photos.photo_request_photo_photo_request_id = ?
				AND photo_request_photos.photo_request_photo_user_id = users.user_entity_id
				ORDER BY photo_request_photos.photo_request_photo_date ASC';
		$query = $this->db->query($sql,array($id));
		foreach ($query->result() as $photo) {
			$result[] = array(
				'id'		=>	$photo->photo_request_photo_photo_id,
				'comment'	=>	$photo->photo_request_photo_comment,
				'time'		=>	$photo->photo_request_photo_date,
				'user_id'	=>	$photo->photo_request_photo_user_id,
				'user_name'	=>	$photo->user_firstname . ' ' . $photo->user_surname
			);
		}
		return $result;
	}

	function AddNewPhotoRequest($user_id,$article_id,$title,$description)
	{
		/// Get next relative photo number
		$sql = 'SELECT	MAX(photo_request_relative_photo_number) AS next_number
				FROM	photo_requests
				WHERE	photo_request_article_id = ?';
		$query = $this->db->query($sql,array($article_id));
		$row = $query->row();
		if ($row->next_number === NULL) {
			$row->next_number = 0;
		} else {
			$row->next_number++;
		}

		/// Insert new photo request
		$sql = 'INSERT INTO	photo_requests(
					photo_request_user_entity_id,
					photo_request_article_id,
					photo_request_relative_photo_number,
					photo_request_view_large,
					photo_request_title,
					photo_request_description)
				VALUES	(?,?,?,1,?,?)';
		$query = $this->db->query($sql,array($user_id,$article_id,$row->next_number,$title,$description));
		$new_id = $this->db->insert_id();

		/// Create new comment thread
		$this->load->model('comments_model');
		$CI = &get_instance();
		$CI->comments_model->CreateThread(array('comment_thread_allow_anonymous_comments' => FALSE), 'photo_requests', array('photo_request_id' => $new_id), 'photo_request_comment_thread_id');

		return $new_id;
	}

	function SuggestPhoto($request_id,$photo_id,$comment,$user_id)
	{
		$sql = 'INSERT INTO photo_request_photos
				SET photo_request_photos.photo_request_photo_photo_request_id = ?,
					photo_request_photos.photo_request_photo_photo_id = ?,
					photo_request_photos.photo_request_photo_comment = ?,
					photo_request_photos.photo_request_photo_user_id = ?';
		$query = $this->db->query($sql,array($request_id,$photo_id,$comment,$user_id));
	}

	function ChangeDetails($request_id,$title,$description)
	{
		$sql = 'UPDATE photo_requests
				SET photo_requests.photo_request_title = ?,
					photo_requests.photo_request_description = ?
				WHERE photo_requests.photo_request_id = ?';
		$query = $this->db->query($sql,array($title,$description,$request_id));
	}

	/**
	 *	@brief	Retrieves a list of all users subscribed to the photographers organisation (ID:456)
	 */
	function GetPhotographers()
	{
		$result = array();
		$sql = 'SELECT	users.user_entity_id,
						users.user_firstname,
						users.user_surname
				FROM	users, subscriptions
				WHERE	users.user_entity_id = subscriptions.subscription_user_entity_id
				AND		subscriptions.subscription_organisation_entity_id = 456
				AND		subscriptions.subscription_user_confirmed = 1
				AND		subscriptions.subscription_organisation_confirmed = 1
				AND		subscriptions.subscription_deleted = 0
				ORDER BY users.user_surname ASC, users.user_firstname ASC';
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$result[] = array(
					'id'	=>	$row->user_entity_id,
					'name'	=>	$row->user_firstname . ' ' . $row->user_surname
				);
			}
		}
		return $result;
	}

	function AssignPhotographer($request_id,$user_id,$status = 'requested')
	{
		/// Remove any previous assigned photographer as only one may be assigned at a time
		$this->UnassignPhotographer($request_id);
		/// Insert new request for a photographer
		$sql = 'INSERT INTO photo_request_users
				SET	photo_request_users.photo_request_user_photo_request_id = ?,
					photo_request_users.photo_request_user_user_entity_id = ?,
					photo_request_users.photo_request_user_status = ?';
		$query = $this->db->query($sql,array($request_id,$user_id,$status));
	}

	function UnassignPhotographer($request_id)
	{
		$sql = 'DELETE FROM photo_request_users
				WHERE photo_request_users.photo_request_user_photo_request_id = ?';
		$query = $this->db->query($sql,array($request_id));
	}

	function FlagRequestReady($request_id, $status = 1)
	{
		$sql = 'UPDATE	photo_requests
				SET		photo_requests.photo_request_flagged = ?
				WHERE	photo_requests.photo_request_id = ?';
		$query = $this->db->query($sql,array($status, $request_id));
	}

	function CancelRequest($request_id)
	{
		$sql = 'UPDATE	photo_requests
				SET		photo_requests.photo_request_deleted = 1
				WHERE	photo_requests.photo_request_id = ?';
		$query = $this->db->query($sql,array($request_id));
	}

	function SelectPhoto($request_id,$photo_id,$editor_id)
	{
		$sql = 'UPDATE	photo_requests
				SET		photo_requests.photo_request_chosen_photo_id = ?,
						photo_requests.photo_request_approved_user_entity_id = ?,
						photo_requests.photo_request_description = \'\'
				WHERE	photo_requests.photo_request_id = ?';
		$query = $this->db->query($sql,array($photo_id,$editor_id,$request_id));
	}

	function SetArticleMainPhoto($article_id,$photo_number)
	{
		$sql = 'UPDATE	articles
				SET		articles.article_main_photo_id = ?
				WHERE	articles.article_id = ?';
		$query = $this->db->query($sql,array($photo_number,$article_id));
	}

	function SetArticleThumbnailPhoto($article_id,$photo_number)
	{
		$sql = 'UPDATE	articles
				SET		articles.article_thumbnail_photo_id = ?
				WHERE	articles.article_id = ?';
		$query = $this->db->query($sql,array($photo_number,$article_id));
	}

	function GetPhotoDetails ($photo_id)
	{
		$sql = 'SELECT	photos.photo_id,
						photos.photo_title
				FROM	photos
				WHERE	photos.photo_id = ?
				AND		photos.photo_deleted = 0';
		$query = $this->db->query($sql, array($photo_id));
		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			return NULL;
		}
	}
	
	function GetPhotosTags ($photo_id)
	{
		$sql = 'SELECT		photo_tags.photo_tag_tag_id AS tag_id,
							tags.tag_name
				FROM		photo_tags,
							tags
				WHERE		photo_tags.photo_tag_photo_id = ?
				AND			photo_tags.photo_tag_tag_id = tags.tag_id
				AND			tags.tag_type = \'photo\'
				AND			tags.tag_deleted = 0';
		$query = $this->db->query($sql, array($photo_id));
		return $query->result_array();
	}

	function GetPhotosForTag ($tag_id)
	{
		$sql = 'SELECT		photos.photo_id
				FROM		photo_tags,
							photos
				WHERE		photo_tags.photo_tag_tag_id = ?
				AND			photo_tags.photo_tag_photo_id = photos.photo_id
				AND			photos.photo_deleted = 0
				ORDER BY	photos.photo_timestamp DESC';
		$query = $this->db->query($sql, array($tag_id));
		return $query->result_array();
	}

}
?>
