<?php
class Members_model extends Model {

	function Members_Model()
	{
		parent::Model();
	}

	/// Gets a users membership with an organisation.
	/**
	 * @param $organisation_id integer/array(int) Organisation entity id.
	 * @param $user_id integer User entity id.
	 * @return array of associative arrays of membership attributes.
	 *	- Subscription must be membership
	 *	- Subscription must not be deleted
	 *	- email will only be returned if the member is on the mailing list
	 */
	function GetMemberDetails($organisation_id, $user_id = NULL, $FilterSql = 'TRUE', $BindData = array())
	{
		if (is_array($organisation_id) && empty($organisation_id)) {
			return array();
		}
		$bind_data = array();
		$sql = '
			SELECT
				subscriptions.subscription_organisation_entity_id AS team_id,
				subscriptions.subscription_paid AS paid,
				subscriptions.subscription_email AS on_mailing_list,
				subscriptions.subscription_vip AS vip,
				subscriptions.subscription_user_confirmed AS confirmed,
				entities.entity_username AS username,
				users.user_entity_id AS user_id,
				users.user_firstname AS firstname,
				users.user_surname AS surname,
				users.user_nickname AS nickname,
				(users.user_office_password IS NULL AND users.user_office_access = 1) AS office_writer_access,
				(users.user_office_password IS NOT NULL AND users.user_office_access = 1) AS office_editor_access,
				entities.entity_username AS email,
				users.user_gender AS gender,
				users.user_enrolled_year AS enrol_year
			FROM
				subscriptions
			INNER JOIN users
				ON	subscriptions.subscription_user_entity_id = users.user_entity_id
			INNER JOIN entities
				ON	subscriptions.subscription_user_entity_id = entities.entity_id
			WHERE entities.entity_deleted = 0 ';
		// If there's a restriction on the usert, apply it here
		if (NULL !== $user_id) {
			$sql .= '	AND subscriptions.subscription_user_entity_id = ?  ';
			$bind_data[] = $user_id;
		}
		// Final conditions
		if (is_array($organisation_id) && count($organisation_id) == 1) {
			$organisation_id = current($organisation_id);
		}
		if (is_array($organisation_id)) {
			// escape organisation ids
			$organisations = array_map(array(&$this->db, 'escape'), $organisation_id);
			$sql .= '	AND subscriptions.subscription_organisation_entity_id IN ('.
				implode(',',$organisations).')';
		} else {
			$sql .= '	AND subscriptions.subscription_organisation_entity_id = ?';
			$bind_data[] = $organisation_id;
		}
		$sql .= '
				AND	subscriptions.subscription_organisation_confirmed = TRUE
				AND	subscriptions.subscription_deleted = FALSE
			';
		// Run the query and return the raw results array.
		$sql .= ' AND ' . $FilterSql;
		$bind_data = array_merge($bind_data, $BindData);
		$query = $this->db->query($sql, $bind_data);
		return $query->result_array();
	}

	function GetUsername($EntityId) {
		$sql = 'SELECT entities.entity_username as entity_username, users.user_nickname as nickname
				FROM entities, users
				WHERE entities.entity_id = users.user_entity_id AND entity_id = ?';
		
		$query = $this->db->query($sql, array($EntityId));
		return $query->row();
	}

	function GetBusinessCards($OrganisationId, $FilterSql = 'TRUE', $BindData = array())
	{
		$bind_data = array($OrganisationId);
		$sql = '
			SELECT	business_cards.business_card_user_entity_id AS user_id,
					business_cards.business_card_id AS id,
					business_cards.business_card_business_card_group_id AS group_id,
					business_cards.business_card_image_id AS image_id,
					business_cards.business_card_name AS name,
					business_cards.business_card_title AS title,
					business_cards.business_card_course AS course,
					business_cards.business_card_blurb AS blurb,
					business_cards.business_card_email AS email,
					business_cards.business_card_mobile AS mobile,
					business_cards.business_card_phone_internal AS phone_internal,
					business_cards.business_card_phone_external AS phone_external,
					business_cards.business_card_postal_address AS postal_address,
					business_card_groups.business_card_group_name AS group_name
			FROM		business_cards
			INNER JOIN	business_card_groups
					ON	business_cards.business_card_business_card_group_id
							= business_card_groups.business_card_group_id
			LEFT JOIN	subscriptions
					ON	subscriptions.subscription_user_entity_id
							= business_cards.business_card_user_entity_id
					AND	subscriptions.subscription_deleted	= FALSE
					AND subscriptions.subscription_user_confirmed	= TRUE
					AND subscriptions.subscription_organisation_confirmed	= TRUE
			LEFT JOIN	users
					ON	users.user_entity_id
							= business_cards.business_card_user_entity_id
			WHERE		business_card_groups.business_card_group_organisation_entity_id
							= ?
					AND	business_cards.business_card_deleted = 0
					AND	' . $FilterSql;
		$bind_data = array_merge($bind_data, $BindData);
		$query = $this->db->query($sql, $bind_data);
		return $query->result_array();
	}


	# returns whether a member is subscribed and confirmed
	function IsSubscribed($UserId,$OrganisationId) {
		$sql = '
				SELECT subscriptions.subscription_user_entity_id
				FROM   subscriptions
				WHERE  subscriptions.subscription_user_entity_id = "'.$UserId.'"
					   AND subscriptions.subscription_organisation_entity_id = "'.$OrganisationId.'"
					AND subscriptions.subscription_user_confirmed	= TRUE
					AND subscriptions.subscription_organisation_confirmed	= TRUE
				';
		$query = $this->db->query($sql);
		$result_array = $query->result_array();
		if(count($result_array) == 0) {
			return false;
		} else {
			return true;
		}
	}

	# sets vip to $status
	function UpdateVipStatus($Status,$UserId,$OrgId) {
		$sql = '
				UPDATE subscriptions
				SET    subscription_vip = "'.$Status.'"
				WHERE  subscription_user_entity_id = "'.$UserId.'"
				       AND subscription_organisation_entity_id = "'.$OrgId.'"
				';
		$query = $this->db->query($sql);
		return $this->db->affected_rows();
	}

	# sets paid to $status
	function UpdatePaidStatus($Status,$UserId,$OrgId) {
		$sql = '
				UPDATE subscriptions
				SET subscription_paid = ?
				WHERE  subscription_user_entity_id = ?
				       AND subscription_organisation_entity_id = ?
				';
		$this->db->query($sql, array($Status, $UserId, $OrgId));
		return $this->db->affected_rows();
	}

	# set access level accordingly
	function UpdateAccessLevel($OfficeAccess,$OfficePassword,$UserId) {
		$sql = '
				UPDATE users
				SET user_office_access = ?, user_office_password = ?
				WHERE users.user_entity_id = ?
				';
		$this->db->query($sql, array($OfficeAccess, $OfficePassword, $UserId));
		return $this->db->affected_rows();
	}

	/// Invite a set of users to join the organisation.
	/**
	 * This simply tries to set a subscription up with organisation_confirmed=1,
	 *	So any existing members aren't affected.
	 * Any users who aren't registered are ignored.
	 * @param $OrganisationId integer Organisation's entity_id.
	 * @param $Users array Users to invite.
	 * @param $UserField string Field which elements of @a $Users must correspond to.
	 *	- 'username'
	 *	- 'id'
	 * @return bool Whether any subscriptions were affected.
	 */
	function InviteUsers($OrganisationId, $Users, $UserField)
	{
		$sql = '
			INSERT INTO subscriptions (
				subscriptions.subscription_organisation_entity_id,
				subscriptions.subscription_user_entity_id,
				subscriptions.subscription_organisation_confirmed
			)
			SELECT
				?,
				users.user_entity_id,
				TRUE
			FROM	entities
			INNER JOIN users
				ON	users.user_entity_id = entities.entity_id
			WHERE
				entities.entity_'.$UserField.'
					IN ('.implode(',',array_fill(1,count($Users),'?')).')
			ON DUPLICATE KEY UPDATE
				subscriptions.subscription_organisation_confirmed = TRUE
			';
		$bind_data = array_merge(array($OrganisationId), $Users);
		$this->db->query($sql, $bind_data);
		return ($this->db->affected_rows() > 0);
	}

	/// Get information about the specified users.
	/**
	 * @param $OrganisationId integer Organisation's entity_id.
	 * @param $Users array of users.
	 * @param $UserField string Field which elements of @a $Users must correspond to.
	 *	- 'username'
	 *	- 'id'
	 * @return Array of user data
	 */
	function GetUsersStatuses($OrganisationId, $Users, $UserField)
	{
		$sql = '
			SELECT
				entities.entity_id AS id,
				entities.entity_username AS username,
				subscriptions.subscription_user_confirmed AS member,
				subscriptions.subscription_deleted AS deleted
			FROM entities
			INNER JOIN subscriptions
				ON	subscriptions.subscription_organisation_entity_id = ?
				AND	subscriptions.subscription_user_entity_id
						= entities.entity_id
				AND	subscriptions.subscription_organisation_confirmed = TRUE
			WHERE	entities.entity_'.$UserField.'
						IN ('.implode(',',array_fill(1,count($Users),'?')).')
			ORDER BY entities.entity_username ASC
			';
		$bind_data = array_merge(array($OrganisationId), $Users);
		$query = $this->db->query($sql, $bind_data);
		return $query->result_array();
	}

	/// Get a list of the invited users.
	/**
	 * @param $OrganisationId integer Organisation's entity_id.
	 * @return Array of user data
	 */
	function GetInvitedUsers($OrganisationId)
	{
		$sql = '
			SELECT
				users.user_firstname,
				users.user_surname,
				users.user_nickname,
			FROM subscriptions
			INNER JOIN users
				ON	users.user_entity_id
						= subscriptions.subscription_user_entity_id
			WHERE	subscriptions.subscription_organisation_entity_id = ?
				AND	subscriptions.subscription_organisation_confirmed = TRUE
				AND	subscriptions.subscription_user_confirmed = FALSE
			';
		$bind_data = array($OrganisationId);
		$query = $this->db->query($sql, $bind_data);
		return $query->result_array();
	}

	// (not in use yet, subscription_member is depreciated)
	/*function RemoveSubscription($UserId,$OrgId) {
		$sql = '
				UPDATE subscriptions
				SET subscription_member = "0"
				WHERE  subscription_user_entity_id = "'.$UserId.'"
				       AND subscription_organisation_entity_id = "'.$OrgId.'"
				';
		$this->db->query($sql);
	}*/

	# sets member=1
	// (not in use yet, subscription_member is depreciated)
	/*function ConfirmMember($UserId,$OrgId) {
		$sql = '
				UPDATE subscriptions
				SET subscription_member = "1"
				WHERE  subscription_user_entity_id = "'.$UserId.'"
				       AND subscription_organisation_entity_id = "'.$OrgId.'"
				';
		$this->db->query($sql);
	}*/

	#sets confirmed=1
	function ConfirmSubscription($UserId,$OrgId) {
		$sql = '
				UPDATE subscriptions
				SET subscription_user_confirmed = "1"
				WHERE  subscription_user_entity_id = "'.$UserId.'"
				       AND subscription_organisation_entity_id = "'.$OrgId.'"
				';
		$this->db->query($sql);
	}

	# A check against an organisation inviting a user twice
	function AlreadyMember($UserId,$OrgId) {
		$sql = '
				SELECT subscriptions.subscription_user_entity_id
				FROM   subscriptions
				WHERE  subscriptions.subscription_user_entity_id = "'.$UserId.'"
					   AND subscriptions.subscription_organisation_entity_id = "'.$OrganisationId.'"
				';
		$query = $this->db->query($sql);
		$result_array = $query->result_array();
		if(count($result_array) == 0) {
			return false;
		} else {
			return true;
		}
	}

}
?>