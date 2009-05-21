<?php

/**
 *	@brief		Model for retrieving notifications
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Notifications_model extends Model
{

	function __construct()
	{
		parent::Model();
	}

	/**
	 *	NOTIFICATIONS
	 */

	function checkAnnouncements ()
	{
		$implicitRoles = array();
		switch (GetUserLevel()) {
			case 'admin':
				$implicitRoles[] = 'LEVEL_ADMIN';
				// Fall-thru
			case 'editor':
				$implicitRoles[] = 'LEVEL_EDITOR';
				// Fall-thru
			case 'office':
				$implicitRoles[] = 'LEVEL_OFFICER';
		}
		$sql = 'SELECT		COUNT(*) as rowcount
				FROM		notifications
				LEFT JOIN	notifications_recipients
					ON	(	notifications_recipients.notification_id = notifications.notification_id
						AND	notifications_recipients.notification_user_entity_id = ?
						)
				WHERE		notifications.notification_type = "announcement"
				AND			notifications.notification_deleted = 0
				AND	(		notifications_recipients.notification_read IS NULL
					OR		notifications_recipients.notification_read = 0
					)
				AND	(		notifications.notification_role IN
							(
								SELECT user_role_role_name
								FROM user_roles
								WHERE user_role_user_entity_id = ?
							)
					OR		notifications.notification_role IN ("' . implode('","', $implicitRoles) . '")
					OR		notifications_recipients.notification_user_entity_id IS NOT NULL
					)';
		$query = $this->db->query($sql, array($this->user_auth->entityId, $this->user_auth->entityId));
		return $query->row()->rowcount;
	}

	function checkPendingBylines ()
	{
		$sql = 'SELECT		COUNT(*) AS rowcount
				FROM		business_card_groups,
							business_cards
				WHERE		business_cards.business_card_business_card_group_id = business_card_groups.business_card_group_id
				AND			business_card_groups.business_card_group_organisation_entity_id IS NULL
				AND			business_cards.business_card_deleted = 0
				AND			business_cards.business_card_approved = 0';
		$query = $this->db->query($sql);
		return $query->row()->rowcount;
	}





	/**
	 *	ACTIVITY
	 */

	function getActivity ($count = 30, $start_date = NULL)
	{
		if (empty($start_date))
			$start_date = mktime();

		$implicitRoles = array();
		switch (GetUserLevel()) {
			case 'admin':
				$implicitRoles[] = 'LEVEL_ADMIN';
				// Fall-thru
			case 'editor':
				$implicitRoles[] = 'LEVEL_EDITOR';
				// Fall-thru
			case 'office':
				$implicitRoles[] = 'LEVEL_OFFICER';
		}
		$sql = 'SELECT		notifications.notification_id AS id,
							notifications.notification_type AS type,
							notifications.notification_subject AS subject,
							notifications.notification_wikitext_cache AS wikitext,
							notifications.notification_user_entity_id AS user_id,
							notifications.notification_byline_business_card_id AS byline_id,
							UNIX_TIMESTAMP(notifications.notification_date) AS date,
							notifications_recipients.notification_read AS opened,
							CONCAT(users.user_firstname, " ", users.user_surname) AS user_name
				FROM		notifications
				INNER JOIN	users
					ON		notifications.notification_user_entity_id = users.user_entity_id
				LEFT JOIN	notifications_recipients
					ON	(	notifications_recipients.notification_id = notifications.notification_id
						AND	notifications_recipients.notification_user_entity_id = ?
						)
				WHERE		notifications.notification_deleted = 0
				AND	(		notifications.notification_role IN
							(
								SELECT user_role_role_name
								FROM user_roles
								WHERE user_role_user_entity_id = ?
							)
					OR		notifications.notification_role IN ("' . implode('","', $implicitRoles) . '")
					OR		notifications_recipients.notification_user_entity_id IS NOT NULL
					OR		notifications.notification_permission IN
							(
								SELECT role_permission_permission_name
								FROM role_permissions
								WHERE role_permission_role_name IN
								(
									SELECT user_role_role_name
									FROM user_roles
									WHERE user_role_user_entity_id = ?
								)
								OR role_permission_role_name IN ("' . implode('","', $implicitRoles) . '")
							)
					)
				AND			UNIX_TIMESTAMP(notifications.notification_date) <= ?
				ORDER BY	notifications.notification_date DESC
				LIMIT		0, ?';
		$query = $this->db->query($sql, array($this->user_auth->entityId, $this->user_auth->entityId, $this->user_auth->entityId, $start_date, $count));
		return $query->result();
	}

	function sendToUsers ($type, $subject, $wikitext, $users = array(), $byline_id = NULL)
	{
		if (!is_array($users)) {
			$users = array($users);
		}
		if (count($users) == 0) return;
		$this->send($type, $subject, $wikitext, '', '', $users, $byline_id);
	}

	function sendToRole ($type, $subject, $wikitext, $role = '', $byline_id = NULL)
	{
		if (empty($role)) return;
		$this->send($type, $subject, $wikitext, $role, '', array(), $byline_id);
	}

	function sendToPermission ($type, $subject, $wikitext, $permission = '', $byline_id = NULL)
	{
		if (empty($permission)) return;
		$this->send($type, $subject, $wikitext, '', $permission, array(), $byline_id);
	}

	function send ($type, $subject, $wikitext, $role = '', $permission = '', $users = array(), $byline_id = NULL)
	{
		$this->load->library('wikiparser');
		$wikicache = $this->wikiparser->parse($wikitext);

		$sql = 'INSERT INTO	notifications SET
				notification_type = ?,
				notification_role = ?,
				notification_permission = ?,
				notification_subject = ?,
				notification_wikitext = ?,
				notification_wikitext_cache = ?,
				notification_user_entity_id = ?,
				notification_byline_business_card_id = ?';
		$query = $this->db->query($sql, array(
			$type,
			$role,
			$permission,
			$subject,
			$wikitext,
			$wikicache,
			$this->user_auth->entityId,
			$byline_id
		));
		$new_id = $this->db->insert_id();

		$sql = 'INSERT INTO notifications_recipients SET
				notification_id = ?,
				notification_user_entity_id = ?';
		foreach ($users as $user) {
			if (!empty($user)) {
				$this->db->query($sql, array($new_id, $user));
			}
		}
	}

	/**
	 *	ANNOUNCEMENTS
	 */

	function getUserBylines ()
	{
		$sql = 'SELECT		business_card_id AS id,
							business_card_image_id AS image,
							business_card_name AS name,
							business_card_title AS title,
							IF(user_default_byline_business_card_id = business_card_id,1,0) AS default_byline
				FROM		business_cards
				INNER JOIN	business_card_groups
					ON		business_card_business_card_group_id = business_card_group_id
				INNER JOIN	users
					ON		business_card_user_entity_id = user_entity_id
				WHERE		business_card_user_entity_id = ?
				AND			business_card_deleted = 0
				AND			business_card_approved = 1
				AND			business_card_group_organisation_entity_id IS NULL
				ORDER BY	default_byline DESC';
		$query = $this->db->query($sql, array($this->user_auth->entityId));
		return $query->result();
	}

	function getAllUserRoles ()
	{
		$sql = 'SELECT		DISTINCT(role_permission_role_name) AS role
				FROM		role_permissions
				ORDER BY	role_permission_role_name';
		$query = $this->db->query($sql);
		return $query->result();
	}
	
	function getAllUsersWithRole ($role_codename) {
		$sql = 'SELECT		users.user_entity_id AS id,
							users.user_firstname AS firstname,
							users.user_surname AS surname
				FROM		users';

		switch ($role_codename) {
			case 'LEVEL_ADMIN':
				$sql .= ' WHERE users.user_office_access = 1 AND users.user_office_password IS NOT NULL AND users.user_admin = 1';
				break;
			case 'LEVEL_EDITOR':
				$sql .= ' WHERE users.user_office_access = 1 AND users.user_office_password IS NOT NULL';
				break;
			case 'LEVEL_OFFICER':
				$sql .= ' WHERE users.user_office_access = 1';
				break;
			default:
				$sql .= ', user_roles WHERE user_roles.user_role_role_name = ? AND user_roles.user_role_user_entity_id = users.user_entity_id';
		}
		$query = $this->db->query($sql, array($role_codename));
		return $query->result();
	}

	function getAllAnnouncements ()
	{
		$sql = 'SELECT		notifications.notification_id AS id,
							notifications.notification_role AS sent_to,
							notifications.notification_subject AS subject,
							notifications.notification_wikitext AS content,
							notifications.notification_wikitext_cache AS cache,
							UNIX_TIMESTAMP(notifications.notification_date) AS time,
							notifications.notification_user_entity_id AS user_id,
							CONCAT(users.user_firstname, " ", users.user_surname) AS user_name,
							notifications.notification_byline_business_card_id AS byline_id,
							business_cards.business_card_name AS byline_name,
							business_cards.business_card_image_id AS byline_image,
							business_cards.business_card_title AS byline_title,
							notifications.notification_deleted AS deleted
				FROM		notifications
				INNER JOIN	users
					ON		notifications.notification_user_entity_id = users.user_entity_id
				INNER JOIN	business_cards
					ON		business_cards.business_card_id = notifications.notification_byline_business_card_id
				WHERE		notifications.notification_type = "announcement"
				AND			notifications.notification_deleted = 0
				ORDER BY	notifications.notification_date DESC
				LIMIT		0, 20';
		$query = $this->db->query($sql);
		return $query->result();
	}

	function postAnnouncement ($subject, $wikitext, $role, $byline)
	{
		$this->send('announcement', $subject, $wikitext, $role, '', array(), $byline);
	}

	function markAsRead($notification_id, $user_id) {
		$sql = 'REPLACE INTO notifications_recipients SET
				notification_id = ?,
				notification_user_entity_id = ?,
				notification_read = 1';
		$query = $this->db->query($sql, array($notification_id, $user_id));
	}
}
?>
