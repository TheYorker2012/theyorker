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
				photo_requests.photo_request_flagged
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
				'id'					=> $id,
				'title'				=>	$row->photo_request_title,
				'description'		=>	$row->photo_request_description,
				'article_id'		=>	$row->photo_request_article_id,
				'article_title'	=>	$row->article_request_title,
				'time'				=>	$row->photo_request_timestamp,
				'reporter_id'		=>	$row->photo_request_user_entity_id,
				'reporter_name'	=>	$row->user_firstname . ' ' . $row->user_surname,
				'editor_id'			=>	$row->photo_request_approved_user_entity_id
      	);
      	if ($row->photo_request_approved_user_entity_id !== NULL) {
				$editor_sql = 'SELECT users.user_firstname,
										users.user_surname
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
			if ($user_query->num_rows() == 0) {
				$result['status'] = 'unassigned';
				$result['assigned_status'] = 'unassigned';
			} else {
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
		$this->load->helper('images');
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
				'id'			=>	$photo->photo_request_photo_photo_id,
				'comment'	=>	$photo->photo_request_photo_comment,
				'time'		=>	$photo->photo_request_photo_date,
				'user_id'	=>	$photo->photo_request_photo_user_id,
				'user_name'	=>	$photo->user_firstname . ' ' . $photo->user_surname,
				'url'			=>	imageLocation($photo->photo_request_photo_photo_id, 'small')
			);
		}
		return $result;
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

}
?>