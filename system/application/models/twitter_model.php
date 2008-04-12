<?php

class Twitter_model extends Model
{
	function Twitter_model () {
		parent::Model();
	}

	function checkTwitterAccess ($twitter_user_id) {
		$sql = 'SELECT		users.user_entity_id,
							users.user_twitter_id
				FROM		users
				WHERE		users.user_twitter_id = ?
				AND			users.user_office_access = 1';
		$query = $this->db->query($sql, array($twitter_user_id));
		if ($query->num_rows() == 1) {
			return $query->row();
		}
		return FALSE;
	}
	
	function getTwitterId ($user_id) {
		$sql = 'SELECT		users.user_twitter_id
				FROM		users
				WHERE		users.user_entity_id = ?';
		$query = $this->db->query($sql, array($user_id));
		return $query->row()->user_twitter_id;
	}
	
	function setTwitterId ($user_id, $twitter_user_id) {
		$sql = 'UPDATE		users
				SET			users.user_twitter_id = ?
				WHERE		users.user_entity_id = ?';
		$query = $this->db->query($sql, array($twitter_user_id, $user_id));
	}
}
?>