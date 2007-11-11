<?php
/**
 *	@brief		This model allows the configuration of which articles to display in the Facebook news ticker
 *
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Facebookticker_model extends Model
{

	function __construct()
	{
		// Call the Model Constructor
		parent::Model();
	}

	function GetTickerSettings ()
	{
		$sql = 'SELECT		facebook_articles.facebook_article_id,
							facebook_articles.facebook_article_article_id,
							facebook_articles.facebook_article_content_type_id,
							content_types.content_type_codename,
							content_types.content_type_name,
							article_contents.article_content_heading
				FROM		facebook_articles
				LEFT JOIN	content_types
					ON		content_types.content_type_id = facebook_articles.facebook_article_content_type_id
				LEFT JOIN	articles
					ON	(	articles.article_id = facebook_articles.facebook_article_article_id
						AND	articles.article_pulled = 0
						AND	articles.article_deleted = 0
						)
				LEFT JOIN	article_contents
					ON		article_contents.article_content_id = articles.article_live_content_id
				ORDER BY	facebook_articles.facebook_article_display_order ASC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function GetArticleSlot ($slot_id)
	{
		$sql = 'SELECT		facebook_articles.facebook_article_id,
							facebook_articles.facebook_article_article_id,
							facebook_articles.facebook_article_content_type_id,
							content_types.content_type_codename,
							content_types.content_type_name,
							article_contents.article_content_heading
				FROM		facebook_articles
				LEFT JOIN	content_types
					ON		content_types.content_type_id = facebook_articles.facebook_article_content_type_id
				LEFT JOIN	articles
					ON	(	articles.article_id = facebook_articles.facebook_article_article_id
						AND	articles.article_pulled = 0
						AND	articles.article_deleted = 0
						)
				LEFT JOIN	article_contents
					ON		article_contents.article_content_id = articles.article_live_content_id
				WHERE		facebook_articles.facebook_article_id = ?
				ORDER BY	facebook_articles.facebook_article_display_order ASC';
		$query = $this->db->query($sql, array($slot_id));
		return $query->row_array();
	}

	function AddArticleSlot ()
	{
		$sql = 'INSERT INTO	facebook_articles
							(facebook_articles.facebook_article_display_order)
				SELECT		(MAX(facebook_articles.facebook_article_display_order) + 1)
				FROM		facebook_articles';
		$query = $this->db->query($sql);
		return ($this->db->affected_rows() > 0);
	}

	function DeleteArticleSlot ($article_slot_id)
	{
		$sql = 'DELETE FROM	facebook_articles
				WHERE		facebook_articles.facebook_article_id = ?';
		$query = $this->db->query($sql, array($article_slot_id));
		return ($this->db->affected_rows() > 0);
	}

	function GetAllArticleContentTypes ()
	{
		$sql = 'SELECT		content_types.content_type_id,
							IF(content_types.content_type_parent_content_type_id IS NOT NULL, CONCAT(parent_type.content_type_name, " - ", content_types.content_type_name), content_types.content_type_name) AS content_type_name
				FROM		content_types
				LEFT JOIN	content_types AS parent_type
					ON		parent_type.content_type_id = content_types.content_type_parent_content_type_id
				WHERE		content_types.content_type_section != "hardcoded"
				ORDER BY	parent_type.content_type_name ASC,
							content_types.content_type_name ASC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function GetContentTypeArticleCount()
	{
		$sql = 'SELECT		DISTINCT(facebook_articles.facebook_article_content_type_id),
							content_types.content_type_id,
							content_types.content_type_codename,
							COUNT(facebook_articles.facebook_article_id) as article_count
				FROM		facebook_articles,
							content_types
				WHERE		facebook_articles.facebook_article_content_type_id = content_types.content_type_id
				GROUP BY	facebook_articles.facebook_article_content_type_id';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function UpdateArticleSlot($slot_id, $type_id, $article_id)
	{
		$sql = 'UPDATE		facebook_articles
				SET			facebook_articles.facebook_article_content_type_id = ?,
							facebook_articles.facebook_article_article_id = ?
				WHERE		facebook_articles.facebook_article_id = ?';
		$query = $this->db->query($sql, array($type_id, $article_id, $slot_id));
		return ($this->db->affected_rows() > 0);
	}





	// adds a byline, if the group id refers to a group with null org id
	function NewBusinessCard($user_id, $group_id, $image_id, $name,
			$title, $blurb, $course, $email, $mobile,
			$phone_internal, $phone_external, $postal_address,
			$order, $start_date, $end_date, $force_publish=0, $aboutusonly=0)
	{
		$sql = 'INSERT INTO business_cards(
				business_card_user_entity_id,
				business_card_image_id,
				business_card_name,
				business_card_title,
				business_card_blurb,
				business_card_course,
				business_card_business_card_group_id,
				business_card_email,
				business_card_mobile,
				business_card_phone_internal,
				business_card_phone_external,
				business_card_postal_address,
				business_card_order,
				business_card_start_date,
				business_card_end_date,
				business_card_deleted,
				business_card_approved,
				business_card_about_us,
				business_card_timestamp)
			VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0,?,?,CURRENT_TIMESTAMP)';
		$this->db->query($sql,array($user_id, $image_id, $name,
						$title, $blurb, $course,
						$group_id, $email, $mobile,
						$phone_internal, $phone_external,
						$postal_address, $order,
						$start_date, $end_date, $force_publish, $aboutusonly));
		return $this->db->insert_id();
	}

	function UpdateBuisnessCard($user_id, $group_id, $image_id, $name,
			$title, $blurb, $course, $email, $mobile,
			$phone_internal, $phone_external, $postal_address,
			$order, $start_date, $end_date, $business_card_id, $force_publish=0, $aboutus=0)
	{
		$sql = "UPDATE `business_cards` SET".
				" `business_card_user_entity_id` = ?,".
				" `business_card_image_id` = ?,".
				" `business_card_name` = ?,".
				" `business_card_title` = ?,".
				" `business_card_blurb` = ?,".
				" `business_card_course` = ?,".
				" `business_card_business_card_group_id` = ?,".
				" `business_card_email` = ?,".
				" `business_card_mobile` = ?,".
				" `business_card_phone_internal` = ?,".
				" `business_card_phone_external` = ?,".
				" `business_card_postal_address` = ?,".
				" `business_card_order` = ?,".
				" `business_card_start_date` = ?,".
				" `business_card_end_date` = ?,".
				" `business_card_approved` = ?,".
				" `business_card_about_us` = ?".
				" WHERE `business_cards`.`business_card_id` = ?";
				$this->db->query($sql,array($user_id, $image_id, $name, 
							$title, $blurb, $course, 
							$group_id, $email, $mobile, 
							$phone_internal, $phone_external, 
							$postal_address, $order,
							$start_date, $end_date, $force_publish, $aboutus, $business_card_id));
		return ($this->db->affected_rows() > 0);
	}

	function ApproveBusinessCard($Id)
	{
		$sql = "UPDATE `business_cards` SET".
				" `business_card_approved` = 1".
				" WHERE `business_cards`.`business_card_id` = ?";
				$this->db->query($sql,$Id);
		return ($this->db->affected_rows() > 0);
	}

	function MaxBusinessCardOrder($GroupID)
	{
		$sql = 'SELECT	MAX(business_card_order) AS highest_number
				FROM	business_cards
				WHERE	business_card_business_card_group_id = ?';
		$query = $this->db->query($sql, $GroupID);
		$row = $query->row();
		return $row->highest_number;
	}

	function GetUserIdFromUsername($username)
	{
		$sql =
			'SELECT entities.entity_id as id '.
			'FROM entities '.
			'WHERE entities.entity_username=? ';
		$query = $this->db->query($sql, $username);
		$row = $query->row();
		if(empty($row)){
			return null;
		}else{
			return $row->id;
		}
	}
	function GetUsernameFromUserId($UserId)
	{
		$sql =
			'SELECT entities.entity_username as username '.
			'FROM entities '.
			'WHERE entities.entity_id=? ';
		$query = $this->db->query($sql, $UserId);
		$row = $query->row();
		if(empty($row)){
			return null;
		}else{
			return $row->username;
		}
	}
	
	function DeleteBusinessCard($id)
	{
		$sql = 'UPDATE 	business_cards
			SET	business_card_deleted = 1
			WHERE	(business_card_id=?)';
		$this->db->query($sql,array($id));
		return ($this->db->affected_rows() > 0);
	}
	
	function AddOrganisationCardGroup($Data)
	{
		$sql = 'INSERT INTO business_card_groups 
		(business_card_group_name, business_card_group_organisation_entity_id, business_card_group_order)
		VALUES (?, ?, ?)';
		$query = $this->db->query($sql, array($Data['group_name'], $Data['organisation_id'], $Data['group_order']));
		return ($this->db->affected_rows() > 0);
	}

	function RenameOrganisationCardGroup($id, $name) {
		$sql = 'UPDATE
					business_card_groups
				SET
					business_card_group_name = ?
				WHERE
					business_card_group_id = ?';
		$query = $this->db->query($sql, array($name, $id));
		return ($this->db->affected_rows() > 0);
	}

	function SelectMaxGroupOrderById($OrgId)
	{
		$sql = 'SELECT	MAX(business_card_group_order) AS highest_group_number
				FROM	business_card_groups
				WHERE	business_card_groups.business_card_group_organisation_entity_id';
		if ($OrgId === NULL) {
			$sql .= ' IS NULL';
		} else {
			$sql .= ' = ?';
		}
		$query = $this->db->query($sql, $OrgId);
		$row = $query->row();
		return $row->highest_group_number;
	}

	function RemoveOrganisationCardGroupById($Id)
	{
		// Remove the organisation card group with the given id
		$sql =
			'DELETE FROM business_card_groups '.
			'WHERE	business_card_groups.business_card_group_id=? LIMIT 1';
		$query = $this->db->query($sql, $Id);
		return ($this->db->affected_rows() > 0);
	}

	/**
	 *	@brief	Re-orders groups, note this does not insist upon sequential ordering numbers
	 *	@param	$direction:string	'up', 'down'
	 */
	function ReorderOrganisationCardGroup ($group_id, $direction)
	{
		// Get current group position
		$sql = 'SELECT		business_card_groups.business_card_group_order,
							business_card_groups.business_card_group_organisation_entity_id,
							business_card_groups.business_card_group_id
				FROM		business_card_groups
				WHERE		business_card_groups.business_card_group_id = ?';
		$query = $this->db->query($sql, array($group_id));
		$group_info = $query->row_array();

		// Map direction change to logic operator
		switch ($direction) {
			case 'up':
				$direction = '<';
				$order = 'DESC';
				break;
			case 'down':
				$direction = '>';
				$order = 'ASC';
				break;
			default:
				return false;
		}
		
		// Find group to swap with
		$sql = 'SELECT		business_card_groups.business_card_group_order,
							business_card_groups.business_card_group_id
				FROM		business_card_groups
				WHERE		business_card_groups.business_card_group_order ' . $direction . ' ?
				AND			business_card_groups.business_card_group_organisation_entity_id ';
		if ($group_info['business_card_group_organisation_entity_id'] === NULL) {
			$sql .= 'IS NULL ';
		} else {
			$sql .= '= ? ';
		}
		$sql .='ORDER BY	business_card_groups.business_card_group_order ' . $order . '
				LIMIT		0, 1';
		$query = $this->db->query($sql, array($group_info['business_card_group_order'], $group_info['business_card_group_organisation_entity_id']));
		if ($query->num_rows() == 0)
			// If no rows returned then the group is already the top-most/bottom-most group
			return true;
		$swap_info = $query->row_array();
	
		// Swap the groups around
		$sql = 'UPDATE		business_card_groups
				SET			business_card_groups.business_card_group_order = ?
				WHERE		business_card_groups.business_card_group_id = ?';
		$query = $this->db->query($sql, array($group_info['business_card_group_order'], $swap_info['business_card_group_id']));
		$query = $this->db->query($sql, array($swap_info['business_card_group_order'], $group_info['business_card_group_id']));
		return true;
	}

	/**
	 *	All functions below here are for bylines which are a special case of business cards
	 */

	/**
	 *	@brief	Get all the current byline teams
	 */
	function GetBylineTeams ()
	{
		$sql = 'SELECT		business_card_groups.business_card_group_id,
							business_card_groups.business_card_group_name,
							business_card_groups.business_card_group_order
				FROM		business_card_groups
				WHERE		business_card_groups.business_card_group_organisation_entity_id IS NULL
				ORDER BY	business_card_groups.business_card_group_order ASC';
		$query = $this->db->query($sql);
		$result = $query->result_array();
		foreach ($result as &$r) {
			$sql = 'SELECT		COUNT(*) AS group_count
					FROM		business_cards
					WHERE		business_cards.business_card_business_card_group_id = ?
					AND			business_cards.business_card_deleted = 0
					AND			business_cards.business_card_approved = 1';
			$query = $this->db->query($sql, array($r['business_card_group_id']));
			$result2 = $query->row_array();
			$r['business_card_group_count'] = $result2['group_count'];
		}
		return $result;
	}

	function BylineTeamInfo ($team_id)
	{
		$sql = 'SELECT		business_card_groups.business_card_group_id,
							business_card_groups.business_card_group_name
				FROM		business_card_groups
				WHERE		business_card_groups.business_card_group_id = ?';
		$query = $this->db->query($sql, array($team_id));
		return $query->row_array();
	}

	function GetTeamBylines ($team_id)
	{
		$sql = 'SELECT		business_cards.business_card_id,
							business_cards.business_card_user_entity_id,
							business_cards.business_card_image_id,
							business_cards.business_card_name,
							business_cards.business_card_title,
							business_cards.business_card_blurb,
							business_cards.business_card_course,
							business_cards.business_card_email,
							business_cards.business_card_mobile,
							business_cards.business_card_phone_internal,
							business_cards.business_card_phone_external,
							business_cards.business_card_postal_address,
							business_cards.business_card_order,
							business_cards.business_card_about_us,
							UNIX_TIMESTAMP(business_cards.business_card_start_date) AS business_card_start_date,
							UNIX_TIMESTAMP(business_cards.business_card_end_date) AS business_card_end_date,
							business_cards.business_card_approved,
							users.user_firstname,
							users.user_surname
				FROM		business_card_groups,
							business_cards
				LEFT JOIN	users
					ON		users.user_entity_id = business_cards.business_card_user_entity_id
				WHERE		business_card_groups.business_card_group_organisation_entity_id IS NULL
				AND			business_card_groups.business_card_group_id = ?
				AND			business_cards.business_card_business_card_group_id = business_card_groups.business_card_group_id
				AND			business_cards.business_card_deleted = 0
				ORDER BY	business_cards.business_card_order ASC';
		$query = $this->db->query($sql, array($team_id));
		return $query->result_array();
	}

	function GetBylineInfo ($byline_id)
	{
		$sql = 'SELECT		business_cards.business_card_id,
							business_cards.business_card_user_entity_id,
							business_cards.business_card_business_card_group_id,
							business_cards.business_card_image_id,
							business_cards.business_card_name,
							business_cards.business_card_title,
							business_cards.business_card_blurb,
							business_cards.business_card_course,
							business_cards.business_card_email,
							business_cards.business_card_mobile,
							business_cards.business_card_phone_internal,
							business_cards.business_card_phone_external,
							business_cards.business_card_postal_address,
							business_cards.business_card_order,
							business_cards.business_card_about_us,
							UNIX_TIMESTAMP(business_cards.business_card_start_date) AS business_card_start_date,
							UNIX_TIMESTAMP(business_cards.business_card_end_date) AS business_card_end_date,
							business_cards.business_card_approved,
							users.user_firstname,
							users.user_surname,
							business_card_groups.business_card_group_name
				FROM		business_card_groups,
							business_cards
				LEFT JOIN	users
					ON		users.user_entity_id = business_cards.business_card_user_entity_id
				WHERE		business_cards.business_card_id = ?
				AND			business_cards.business_card_business_card_group_id = business_card_groups.business_card_group_id
				AND			business_cards.business_card_deleted = 0
				AND			business_card_groups.business_card_group_organisation_entity_id IS NULL';
		$query = $this->db->query($sql, array($byline_id));
		return $query->row_array();
	}

	function GetPublicBylineInfo ($byline_id)
	{
		$sql = 'SELECT		business_cards.business_card_id,
							business_cards.business_card_user_entity_id,
							business_cards.business_card_business_card_group_id,
							business_cards.business_card_image_id,
							business_cards.business_card_name,
							business_cards.business_card_title,
							business_cards.business_card_blurb,
							business_cards.business_card_course,
							business_cards.business_card_email,
							business_cards.business_card_mobile,
							business_cards.business_card_phone_internal,
							business_cards.business_card_phone_external,
							business_cards.business_card_postal_address,
							business_cards.business_card_order,
							business_cards.business_card_about_us,
							UNIX_TIMESTAMP(business_cards.business_card_start_date) AS business_card_start_date,
							UNIX_TIMESTAMP(business_cards.business_card_end_date) AS business_card_end_date,
							business_cards.business_card_approved,
							business_card_groups.business_card_group_name
				FROM		business_card_groups,
							business_cards
				WHERE		business_cards.business_card_id = ?
				AND			business_cards.business_card_business_card_group_id = business_card_groups.business_card_group_id
				AND			business_cards.business_card_deleted = 0
				AND			business_cards.business_card_approved = 1
				AND			business_card_groups.business_card_group_organisation_entity_id IS NULL';
		$query = $this->db->query($sql, array($byline_id));
		return $query->row_array();
	}

	function GetPendingBylines ()
	{
		$sql = 'SELECT		business_cards.business_card_id,
							business_cards.business_card_user_entity_id,
							business_cards.business_card_business_card_group_id,
							business_cards.business_card_image_id,
							business_cards.business_card_name,
							business_cards.business_card_title,
							business_cards.business_card_blurb,
							business_cards.business_card_course,
							business_cards.business_card_email,
							business_cards.business_card_mobile,
							business_cards.business_card_phone_internal,
							business_cards.business_card_phone_external,
							business_cards.business_card_postal_address,
							business_cards.business_card_order,
							business_cards.business_card_about_us,
							UNIX_TIMESTAMP(business_cards.business_card_start_date) AS business_card_start_date,
							UNIX_TIMESTAMP(business_cards.business_card_end_date) AS business_card_end_date,
							business_cards.business_card_approved,
							users.user_firstname,
							users.user_surname,
							business_card_groups.business_card_group_name
				FROM		business_card_groups,
							business_cards
				LEFT JOIN	users
					ON		users.user_entity_id = business_cards.business_card_user_entity_id
				WHERE		business_cards.business_card_business_card_group_id = business_card_groups.business_card_group_id
				AND			business_cards.business_card_deleted = 0
				AND			business_cards.business_card_approved = 0';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function GetUserBylines ($user_id)
	{
		$sql = 'SELECT		business_cards.business_card_id,
							business_cards.business_card_user_entity_id,
							business_cards.business_card_business_card_group_id,
							business_cards.business_card_image_id,
							business_cards.business_card_name,
							business_cards.business_card_title,
							business_cards.business_card_blurb,
							business_cards.business_card_course,
							business_cards.business_card_email,
							business_cards.business_card_mobile,
							business_cards.business_card_phone_internal,
							business_cards.business_card_phone_external,
							business_cards.business_card_postal_address,
							business_cards.business_card_order,
							business_cards.business_card_about_us,
							UNIX_TIMESTAMP(business_cards.business_card_start_date) AS business_card_start_date,
							UNIX_TIMESTAMP(business_cards.business_card_end_date) AS business_card_end_date,
							business_cards.business_card_approved,
							business_cards.business_card_facebook_link,
							users.user_firstname,
							users.user_surname,
							business_card_groups.business_card_group_name
				FROM		business_card_groups,
							business_cards
				LEFT JOIN	users
					ON		users.user_entity_id = business_cards.business_card_user_entity_id ';
		if ($user_id === NULL) {
			$sql .= 'WHERE		business_cards.business_card_user_entity_id IS NULL ';
		} else {
			$sql .= 'WHERE		business_cards.business_card_user_entity_id = ? ';
		}
		$sql .= 'AND		business_cards.business_card_business_card_group_id = business_card_groups.business_card_group_id
				AND			business_card_groups.business_card_group_organisation_entity_id IS NULL
				AND			business_cards.business_card_deleted = 0';
		$query = $this->db->query($sql, array($user_id));
		return $query->result_array();
	}

	function BylineFacebookSetting ($byline_id, $setting = 1)
	{
		$sql = 'UPDATE		business_cards
				SET			business_cards.business_card_facebook_link = ?
				WHERE		business_cards.business_card_id = ?';
		$query = $this->db->query($sql, array($setting, $byline_id));
		return ($this->db->affected_rows > 0);
	}

	function GetUserBylinesForFacebook ($user_id)
	{
		$sql = 'SELECT		business_cards.business_card_id,
							business_cards.business_card_user_entity_id,
							business_cards.business_card_business_card_group_id,
							business_cards.business_card_image_id,
							business_cards.business_card_name,
							business_cards.business_card_title,
							business_cards.business_card_blurb,
							business_cards.business_card_course,
							business_cards.business_card_email,
							business_cards.business_card_mobile,
							business_cards.business_card_phone_internal,
							business_cards.business_card_phone_external,
							business_cards.business_card_postal_address,
							business_cards.business_card_order,
							business_cards.business_card_about_us,
							UNIX_TIMESTAMP(business_cards.business_card_start_date) AS business_card_start_date,
							UNIX_TIMESTAMP(business_cards.business_card_end_date) AS business_card_end_date,
							business_cards.business_card_approved,
							business_cards.business_card_facebook_link,
							users.user_firstname,
							users.user_surname,
							business_card_groups.business_card_group_name
				FROM		business_card_groups,
							business_cards
				LEFT JOIN	users
					ON		users.user_entity_id = business_cards.business_card_user_entity_id ';
		if ($user_id === NULL) {
			$sql .= 'WHERE		business_cards.business_card_user_entity_id IS NULL ';
		} else {
			$sql .= 'WHERE		business_cards.business_card_user_entity_id = ? ';
		}
		$sql .= 'AND		business_cards.business_card_business_card_group_id = business_card_groups.business_card_group_id
				AND			business_card_groups.business_card_group_organisation_entity_id IS NULL
				AND			business_cards.business_card_deleted = 0
				AND			business_cards.business_card_facebook_link = 1';
		$query = $this->db->query($sql, array($user_id));
		return $query->result_array();
	}

	/**
	 *	@brief	Re-orders bylines within a group, note this does not insist upon sequential ordering numbers
	 *	@param	$direction:string	'up', 'down'
	 */
	function ReorderByline ($byline_id, $direction)
	{
		// Get current byline position
		$sql = 'SELECT		business_cards.business_card_order,
							business_cards.business_card_business_card_group_id,
							business_cards.business_card_id
				FROM		business_cards
				WHERE		business_cards.business_card_id = ?';
		$query = $this->db->query($sql, array($byline_id));
		$byline_info = $query->row_array();

		// Map direction change to logic operator
		switch ($direction) {
			case 'up':
				$direction = '<';
				$order = 'DESC';
				break;
			case 'down':
				$direction = '>';
				$order = 'ASC';
				break;
			default:
				return false;
		}

		// Find byline to swap with
		$sql = 'SELECT		business_cards.business_card_order,
							business_cards.business_card_id
				FROM		business_cards
				WHERE		business_cards.business_card_order ' . $direction . ' ?
				AND			business_cards.business_card_business_card_group_id ';
		if ($byline_info['business_card_business_card_group_id'] === NULL) {
			$sql .= 'IS NULL ';
		} else {
			$sql .= '= ? ';
		}
		$sql .='ORDER BY	business_cards.business_card_order ' . $order . '
				LIMIT		0, 1';
		$query = $this->db->query($sql, array($byline_info['business_card_order'], $byline_info['business_card_business_card_group_id']));
		if ($query->num_rows() == 0)
			// If no rows returned then the group is already the top-most/bottom-most group
			return true;
		$swap_info = $query->row_array();

		// Swap the groups around
		$sql = 'UPDATE		business_cards
				SET			business_cards.business_card_order = ?
				WHERE		business_cards.business_card_id = ?';
		$query = $this->db->query($sql, array($byline_info['business_card_order'], $swap_info['business_card_id']));
		$query = $this->db->query($sql, array($swap_info['business_card_order'], $byline_info['business_card_id']));
		return true;
	}

	function GetDefaultByline ($user_id)
	{
		$sql = 'SELECT		users.user_default_byline_business_card_id
				FROM		users
				WHERE		users.user_entity_id = ?';
		$query = $this->db->query($sql, array($user_id));
		$result = $query->row_array();
		return $result['user_default_byline_business_card_id'];
	}

	function SetDefaultByline ($user_id, $byline_id)
	{
		$sql = 'UPDATE		users
				SET			users.user_default_byline_business_card_id = ?
				WHERE		users.user_entity_id = ?';
		$query = $this->db->query($sql, array($byline_id, $user_id));
		return ($this->db->affected_rows() > 0);
	}

	function AddPhotoToByline($byline_id, $photo)
	{
		$sql = 'UPDATE		business_cards
				SET			business_cards.business_card_image_id = ?,
							business_cards.business_card_approved = 0
				WHERE		business_cards.business_card_id = ?';
		$query = $this->db->query($sql, array($photo, $byline_id));
		return ($this->db->affected_rows() > 0);
	}

	/// Get all bylines
	/**
	 * @return array[bylines].
	 */
	function GetBylines()
	{
		$sql =
			'SELECT'.
			' business_cards.business_card_user_entity_id,'.
			' business_cards.business_card_id,'.
			' business_cards.business_card_name '.
			'FROM business_cards '.
			'INNER JOIN business_card_groups '.
			' ON business_card_groups.business_card_group_id = business_cards.business_card_business_card_group_id '.
			'WHERE business_cards.business_card_deleted = 0 '.
			'AND business_card_groups.business_card_group_organisation_entity_id IS NULL '.
			'ORDER BY business_cards.business_card_name';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$result_item['user_id'] = $row->business_card_user_entity_id;
				$result_item['id'] = $row->business_card_id;
				$result_item['name'] = $row->business_card_name;
				$result[] = $result_item;
			}			
		}
		return $result;
	}
}

?>