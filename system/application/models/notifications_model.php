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

	function getAnnouncements ()
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
		$sql = 'SELECT		notifications.notification_id AS id,
							notifications.notification_subject AS subject,
							notifications.notification_wikitext_cache AS content,
							UNIX_TIMESTAMP(notifications.notification_date) AS time,
							business_cards.business_card_name AS user_name,
							business_cards.business_card_image_id AS user_image,
							business_cards.business_card_title AS user_title,
							notifications_read.notification_id AS opened
				FROM		notifications
				INNER JOIN	business_cards
					ON		business_cards.business_card_id = notifications.notification_byline_business_card_id
				LEFT JOIN	notifications_read
					ON	(	notifications_read.notification_id = notifications.notification_id
						AND	notifications_read.notification_user_entity_id = ?
						)
				WHERE		notifications.notification_type = "announcement"
				AND			notifications.notification_deleted = 0
				AND	(		notifications.notification_role IN
							(
								SELECT user_role_role_name
								FROM user_roles
								WHERE user_role_user_entity_id = ?
							)
					OR		notifications.notification_role IN ("' . implode('","', $implicitRoles) . '")
					)
				ORDER BY	notifications.notification_date DESC
				LIMIT		0, 20';
		$query = $this->db->query($sql, array($this->user_auth->entityId, $this->user_auth->entityId));
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

	function add ($subject, $wikitext, $cache, $role, $byline)
	{
		$sql = 'INSERT INTO	notifications SET
				notification_role = ?,
				notification_subject = ?,
				notification_wikitext = ?,
				notification_wikitext_cache = ?,
				notification_user_entity_id = ?,
				notification_byline_business_card_id = ?';
		$query = $this->db->query($sql, array($role, $subject, $wikitext, $cache, $this->user_auth->entityId, $byline));
	}

	function markAsRead($notification_id, $user_id) {
		$sql = 'REPLACE INTO notifications_read SET
				notification_id = ?,
				notification_user_entity_id = ?';
		$query = $this->db->query($sql, array($notification_id, $user_id));
	}
}
?>
