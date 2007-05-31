<?php
/*
 * Model for managment of user quick links
 *
 *
 * \author Alex Fargus (agf501)
 *
 *
 *
 */
class Links_Model extends Model {
	/*
	 * Constructor, calls default model constructor
	 */
	function Home_Model() {
		parent::Model();
	}
	/*
	 * Gets gallery links
	 */
	function GalleryLinks() {
			$sql = 'SELECT image_id, image_title, image_image_type_id FROM images, image_types WHERE image_image_type_id = image_type_id AND image_type_codename = ? AND image_title = "" ';
			return $this->db->query($sql, array('link'));
	}
	/*
	 * Adds a user link with default image to database
	 */
	function AddLink($name, $url, $nominated, $image_id = 0) {
		$sql = 'INSERT INTO links(link_url,link_name,link_nominated,link_image_id) VALUES (?, ?, ?, ?)';
		$query = $this->db->query($sql,array($url, $name, $nominated, $image_id));
		return $this->db->insert_id();
	}
	/*
	 * Removes user link from database (+ any non default or official pics)
	 * Unknown what will happen if user has same link twice
	 */
	function DeleteLink($user,$id) {
		$this->db->trans_start();
		$sql = 'SELECT link_official, link_image_id
			FROM links WHERE link_id = ?';
		$query = $this->db->query($sql,array($id));
		$row = $query->first_row();
		if ($row->link_official == 0) {
			$sql ='DELETE FROM links WHERE link_id = ?';
			$this->db->query($sql,array($id));
			//TODO move this static number into a config file somewhere

			$sql ='SELECT image_title FROM links, images WHERE image_id = link_image_id AND link_id = ?';
			$result = $this->db->query($sql,array($id));

			if($result->num_rows() > 0 && $result->row()->image_title != '') {
				$sql = 'DELETE FROM images WHERE image_id = ?';
				$this->db->query($sql,array($row->link_image_id));
			}
		}
		$this->db->trans_complete();
	}

	/*
	 * Removes user link from database (+ any non default or official pics) only if no longer in use.
	 */
	function DeleteOfficialLink($id) {
		$sql = 'DELETE FROM user_links WHERE user_link_link_id= ?';
		$query = $this->db->query($sql,array($id));

		//Get Image Id
		$sql = 'SELECT link_image_id
			FROM links WHERE link_id = ?';
		$query = $this->db->query($sql,array($id));
		$row = $query->first_row();

		if($query->num_rows() > 0) {
			$sql ='DELETE FROM links WHERE link_id = ?';
			$this->db->query($sql,array($id));
			//TODO move this static number into a config file somewhere
			if ($row->link_image_id != 232) {
				$sql = 'DELETE FROM images WHERE image_id = ?';
				$this->db->query($sql,array($row->link_image_id));
			}
		}
	}

	function UpdateLink($link_id, $link_name, $link_url) {
		$sql = 'UPDATE links SET link_name = ?, link_url = ? WHERE link_id = ?';
		$this->db->query($sql,array($link_name, $link_url, $link_id));
	}

	/*
	 * Makes a link an officical link, cannot be undone!
	 */
	function PromoteLink($user, $linkId) {
		$sql = 'UPDATE links SET link_official = 1, link_editor_entity_id = ? WHERE link_id = ?';
		$this->db->query($sql,array($user, $linkId));
	}

	/*
	 * Rejects a link from nomination, cannot be undone!
	 */
	function RejectLink($user, $linkId) {
		$sql = 'UPDATE links SET link_nominated = 0, link_editor_entity_id = ? WHERE link_id = ?';
		$this->db->query($sql,array($user, $linkId));

		$sql = 'SELECT user_link_link_id FROM user_links WHERE user_link_link_id= ?';
		$query = $this->db->query($sql,array($linkId));
		$users_with_link = $query->num_rows();

		if ($users_with_link == 0) {
			//Get Image Id
			$sql = 'SELECT link_image_id
				FROM links WHERE link_id = ?';
			$query = $this->db->query($sql,array($linkId));
			$row = $query->first_row();

			if($query->num_rows() > 0) {
				$sql ='DELETE FROM links WHERE link_id = ?';
				$this->db->query($sql,array($linkId));
				//TODO move this static number into a config file somewhere
				if ($row->link_image_id != 232) {
					$sql = 'DELETE FROM images WHERE image_id = ?';
					$this->db->query($sql,array($row->link_image_id));
				}
			}
		}
	}

	/*
	 * Takes an array of link ids and orders them in the order of the array
	 * Returns false on error.
	 */
	function SetUserLinkOrder($user,$ordered_array){
		$this->db->trans_start();
		$sql = 'SELECT user_link_id FROM user_links WHERE user_link_user_entity_id = ?';
		$query = $this->db->query($sql,array($user));
		if (sizeof($ordered_arry) == $query->num_rows()) {
			for ($i = 0; $i <= sizeof($ordered_array); $i++){
				$sql = 'UPDATE user_links SET user_link_order = ? WHERE user_link_id = ?';
				$this->db->query($sql,array($i,$ordered_array[$i]));
			}
			$this->db->trans_complete();
			return True;
		} else {
			$this->db->trans_rollback();
			return False;
		}
	}

	function ChangeUserLinks($user, $links) {
		$sql = 'SELECT user_link_link_id AS id
		        FROM user_links
		        INNER JOIN links
		        ON links.link_id = user_link_link_id
		        WHERE user_link_user_entity_id= ?
		        AND link_official = 0';
		$query = $this->db->query($sql, array($user));
		foreach ($query->result() as $unofficialLink) {
			if (array_search($unofficialLink->id, $links) === false) {
				$this->DeleteLink($user, $unofficialLink->id);
			}
		}
		$this->DropUserLinks($user);
		$this->AddUserLinks($user, $links);
	}

	function DropUserLinks($user) {
		$sql = 'DELETE FROM user_links WHERE user_link_user_entity_id= ?';
		return $this->db->query($sql, array($user));
	}

	/*
	 * Returns an array with users images + links
	 */
	function GetUserLinks($user) {
		$sql = 'SELECT link_id, link_url, link_name, link_image_id
			FROM links
			INNER JOIN user_links
			ON user_link_link_id = links.link_id
			WHERE user_link_user_entity_id = ?
			ORDER BY user_link_order ASC';
		return $this->db->query($sql, array($user));
	}

	function GetLink($link_id) {
		$sql = 'SELECT link_id, link_url, link_name, link_image_id
			FROM links
			WHERE link_id = ?
			';
		return $this->db->query($sql, array($link_id))->row();
	}

	function GetAllOfficialLinks() {
		$sql = 'SELECT link_id, link_url,link_name,link_image_id
			FROM links
			WHERE link_official = 1
			ORDER BY link_name ASC';
		$query = $this->db->query($sql);
		return $query;
	}

	function GetAllNominatedLinks() {
		$sql = 'SELECT link_id, link_url,link_name,link_image_id
			FROM links
			WHERE link_official = 0 AND link_nominated = 1
			ORDER BY link_name ASC';
		$query = $this->db->query($sql);
		return $query;
	}

//First checks that the user owns the link and it is not official
	function ReplaceImage($linkID, $userID, $imageID, $admin = false) {
		$sql = 'SELECT user_link_link_id FROM user_links
		        INNER JOIN links ON links.link_id = user_links.user_link_link_id
		        WHERE user_link_link_id = ? AND user_link_user_entity_id = ?
		          AND links.link_official = 0 LIMIT 1';
		if ($this->db->query($sql, array($linkID, $userID))->num_rows() == 1 or $admin) {
			$this->db->where('link_id',  $linkID)
			         ->update('links', array('link_image_id' => $imageID));
			return true;
		}
		return false;
}

	function UserTotalLinks($user) {
		$sql = 'SELECT COUNT(*) AS total FROM user_links WHERE user_link_user_entity_id = ?';
		return $this->db->query($sql, array($user))->first_row()->total;
	}

	function GetLinkImageTypeId() {
		$sql = 'SELECT image_type_id FROM image_types WHERE image_types.image_type_codename = "link"';
		return $this->db->query($sql)->first_row()->image_type_id;
	}

	function AddUserLink($user, $link) {
		$sql = 'INSERT INTO user_links
		        (user_link_user_entity_id, user_link_link_id, user_link_order)
		        VALUES (?, ?, ?)';
		$this->db->query($sql, array($user, $link, $this->UserTotalLinks($user)));
	}

	function AddUserLinks($user, $links) {
		$first = true;
		$sql = 'INSERT INTO user_links (user_link_user_entity_id, user_link_link_id, user_link_order) VALUES';
		for ($i = 0; $i < count($links); $i++) {
			if (!$first) {
				$sql.= ',';
			} else {
				$first = false;
			}
			$sql.= ' ('.$user.', ?, '.$i.')';
		}

		return $this->db->query($sql, $links);
	}
}
?>
