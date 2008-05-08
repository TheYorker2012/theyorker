<?php
/**
 *	This model retrieves data required for the Preferences Wizard
 *
 *	@author	Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Prefs_model extends Model {

    function Prefs_model()
    {
        // Call the Model constructor
        parent::Model();
    }

	/**
	 *	General
	 */

	function GetColleges()
	{
		$sql =
			'SELECT'.
			' college_organisation_entity_id AS college_id,'.
			' college_name '.
			'FROM colleges '.
			'ORDER BY college_name ASC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function collegeExists($college)
	{
		$sql =
			'SELECT'.
			' college_name '.
			'FROM colleges '.
			'WHERE college_organisation_entity_id = ' . $college;
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	function GetYears()
	{
		$sql =
			'SELECT'.
			' year_id '.
			'FROM years '.
			'ORDER BY year_id DESC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// Check that the enrolled year is within the last 10 years
	function yearValid($year)
	{
		return ($year <= date('Y')) && ($year >= (date('Y') - 10));
	}

	function timeValid($time)
	{
		return ($time == 12) || ($time == 24);
	}

	function genderCheck($str)
	{
		switch ($str) {
			case 'm':
			case 'f':
				return $str;
				break;
			case 'n':				
			default:
				return NULL;
				break;
		}
	}

	function getUserInfo ($uid)
	{
		$sql =
			'SELECT'.
			' user_college_organisation_entity_id  AS user_college,'.
			' user_firstname,'.
			' user_surname,'.
			' (user_facebook_session_id IS NOT NULL) as user_facebook_enabled,'.
			' user_store_password,'.
			' user_nickname,'.
			' user_gender,'.
			' user_time_format,'.
			' user_enrolled_year '.
			'FROM users '.
			'WHERE user_entity_id = ' . $uid;
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	function updateUserInfo ($uid, $info)
	{
		/// @todo Fix this function to take sensible arguments and ensure users comply (account_personal library).
		/// @bug This function is a load of bollocks, whoever wrote it needs shooting.
		
		// This line serves no purpose
		//$row = $this->getUserInfo ($uid);

		$sql =
			'UPDATE users '.
			'SET'.
			' user_college_organisation_entity_id = ?,'.
			' user_firstname = ?,'.
			' user_surname = ?,'.
			' user_store_password = ?,'.
			' user_nickname = ?,'.
			' user_gender = ?,'.
			' user_enrolled_year = ?,'.
			' user_time_format = ?, '.
			' user_facebook_session_id = ? '.
			'WHERE user_entity_id = ' . $uid;
		$query = $this->db->query($sql, $info);
	}

	/**
	 *	COMMON
	 */

	function getOrganisationDescription ($org_id)
	{
		$sql =
			'SELECT'.
			' organisations.organisation_name AS name, '.
			' organisation_content_description AS description '.
			'FROM organisations '.
			'INNER JOIN organisation_contents '.
			'ON organisations.organisation_live_content_id = organisation_contents.organisation_content_id '.
			'WHERE organisation_entity_id = '.$org_id;
		$query = $this->db->query($sql);
		return $query->row_array();
	}

	function isSubscribed ($user_id, $org_id)
	{
		$sql =
			'SELECT'.
			' subscription_organisation_entity_id '.
			'FROM subscriptions '.
			'WHERE subscription_user_entity_id = ?'.
			' AND subscription_organisation_entity_id = ?'.
			' AND subscription_deleted = 0';
		$query = $this->db->query($sql, array($user_id, $org_id));
		return $query->num_rows();
	}

	function isDeletedSubscription ($user_id, $org_id)
	{
		$sql =
			'SELECT'.
			' subscription_organisation_entity_id '.
			'FROM subscriptions '.
			'WHERE subscription_user_entity_id = ?'.
			' AND subscription_organisation_entity_id = ?'.
			' AND subscription_deleted = 1';
		$query = $this->db->query($sql, array($user_id, $org_id));
		return $query->num_rows();
	}

	function addSubscription ($user_id, $org_id)
	{
		$sql =
			'INSERT INTO subscriptions '.
			'SET subscription_organisation_entity_id = ?,'.
			' subscription_user_entity_id = ?,'.
			' subscription_user_confirmed = 1';
		$query = $this->db->query($sql, array($org_id, $user_id));
	}

	function reactivateSubscription ($user_id, $org_id)
	{
		$sql =
			'UPDATE subscriptions '.
			'SET subscription_deleted = 0,'.
			' subscription_timestamp = CURRENT_TIMESTAMP '.
			'WHERE subscription_organisation_entity_id = ?'.
			' AND subscription_user_entity_id = ?';
		$query = $this->db->query($sql, array($org_id, $user_id));
	}

	function deleteSubscription ($user_id, $org_id)
	{
		$sql =
			'UPDATE subscriptions '.
			'SET subscription_deleted = 1,'.
			' subscription_timestamp = CURRENT_TIMESTAMP '.
			'WHERE subscription_organisation_entity_id = ?'.
			' AND subscription_user_entity_id = ?';
		$query = $this->db->query($sql, array($org_id, $user_id));
	}
	
	/// Duplicate the subscriptions of user 0.
	/**
	 * Copy (replace) various fields from the subscriptions of user 0 into the current user.
	 * @note Does not copy vip status, paid status
	 * @note Only copy membership subscriptions
	 * @param $UserId int Entity idea of user.
	 * @return int The number of rows affected.
	 */
	function initialiseSubscriptions($UserId)
	{
		$sql = '
		REPLACE INTO subscriptions (
				subscription_organisation_entity_id,
				subscription_user_entity_id,
				subscription_email,
				subscription_user_confirmed,
				subscription_organisation_confirmed
		)
		SELECT	subscription_organisation_entity_id,
				'.$this->db->escape($UserId).',
				subscription_email,
				subscription_user_confirmed,
				subscription_organisation_confirmed
		FROM	subscriptions
		WHERE	(	subscription_user_confirmed
				OR	subscription_organisation_confirmed)
			AND	subscription_deleted = 0
			AND	subscription_user_entity_id = 0
			';
		$query = $this->db->query($sql);
		return $this->db->affected_rows();
	}
	
	/// Set the calendar subscription properties.
	/**
	 * @param $user_id int The entity id of the user.
	 * @param $org_name int The organisation directory entry name of the organisation.
	 * @param $calendar NULL,bool NULL for no change or the new calendar subscription value.
	 * @param $todo NULL,bool NULL for no change or the new todo subscription value.
	 */
	function setCalendarSubscriptionByOrgName($user_id, $org_name, $calendar, $todo = NULL)
	{
		$sets = array();
		if (is_bool($calendar)) {
			$sets[] = 'event_subscription_calendar='.$this->db->escape($calendar);
		}
		if (is_bool($todo)) {
			$sets[] = 'event_subscription_todo='.$this->db->escape($todo);
		}
		if (!empty($sets)) {
			$imploded_sets = implode(',', $sets);
			$sql =
				'INSERT INTO event_subscriptions SET '.
					'event_subscription_organisation_entity_id = (
						SELECT organisation_entity_id FROM organisations 
						WHERE organisation_directory_entry_name='.$this->db->escape($org_name).'),'.
					'event_subscription_user_entity_id = '.$this->db->escape($user_id).','.
					$imploded_sets.
				" ON DUPLICATE KEY UPDATE $imploded_sets";
			$this->db->query($sql);
			return $this->db->affected_rows();
		}
		return -1;
	}

	/* Store info about a VIP application */
	function vipApplication ($user_id,$org_id,$position,$phone)
	{
		$sql = 'UPDATE	subscriptions, users
				SET		subscriptions.subscription_user_position = ?,
						users.user_contact_phone_number = ?
				WHERE	subscriptions.subscription_user_entity_id = ?
				AND		subscriptions.subscription_organisation_entity_id = ?
				AND		subscriptions.subscription_user_entity_id = users.user_entity_id';
		$query = $this->db->query($sql,array($position,$phone,$user_id,$org_id));

		$sql = 'UPDATE';
	}

	/**
	 *	Organisation Subscriptions
	 */

	function isOrganisationType($type)
	{
		$sql = 'SELECT		organisation_types.organisation_type_name			AS friendlyname
				FROM		organisation_types
				WHERE		organisation_types.organisation_type_codename = ?';
		$query = $this->db->query($sql,array($type));
		return $query->row_array();
	}

	function getAllOrganisations ($type)
	{
		$sql = 'SELECT		organisation_entity_id							AS id,
							organisation_contents.organisation_content_url	AS url,
							organisation_directory_entry_name				AS directory,
							organisation_name								AS name
				FROM		organisations,
							organisation_contents,
							organisation_types
				WHERE		organisations.organisation_live_content_id = organisation_contents.organisation_content_id
				AND			organisations.organisation_organisation_type_id = organisation_types.organisation_type_id
				AND			organisations.organisation_events = 1
				AND			organisation_types.organisation_type_codename = ?
				ORDER BY	name ASC';
		$query = $this->db->query($sql,array($type));
		return $query->result_array();
	}

	function isOfOrganisationType ($org_id,$type)
	{
		$sql = 'SELECT		organisation_entity_id
				FROM		organisations,
							organisation_types
				WHERE		organisation_organisation_type_id = organisation_types.organisation_type_id
				AND			organisations.organisation_events = 1
				AND			organisation_types.organisation_type_codename = ?
				AND			organisation_entity_id = ?';
		$query = $this->db->query($sql,array($type, $org_id));
		return $query->num_rows();
	}

	function getOrganisationTypeSubscriptions ($user_id,$type)
	{
		$sql = 'SELECT		subscriptions.subscription_organisation_entity_id	AS orgid
				FROM		subscriptions,
							organisations,
							organisation_types
				WHERE		subscriptions.subscription_user_entity_id = ?
				AND			subscriptions.subscription_organisation_entity_id = organisations.organisation_entity_id
				AND			subscriptions.subscription_deleted = 0
				AND			organisations.organisation_organisation_type_id = organisation_types.organisation_type_id
				AND			organisation_types.organisation_type_codename = ?';
		$query = $this->db->query($sql,array($user_id,$type));
		$orgs = array();
		foreach ($query->result_array() as $row) {
			array_push($orgs, $row['orgid']);
		}
		return $orgs;
	}

	function getAllSubscriptions ($user_id)
	{
		$sql =
		'SELECT		organisations.organisation_name							AS organisation_name,
					organisation_types.organisation_type_name				AS organisation_type_name,
					subscriptions.subscription_organisation_entity_id		AS org_id,
					subscriptions.subscription_email						AS subscription_email,
					subscriptions.subscription_paid							AS subscription_paid,
					IF(	event_subscriptions.event_subscription_user_entity_id IS NOT NULL,
							event_subscriptions.event_subscription_calendar,
							default_event_subscription.event_subscription_calendar IS NOT NULL
								AND default_event_subscription.event_subscription_calendar) 		AS subscription_calendar,
					IF(	event_subscriptions.event_subscription_user_entity_id IS NOT NULL,
							event_subscriptions.event_subscription_todo,
							default_event_subscription.event_subscription_todo IS NOT NULL
								AND default_event_subscription.event_subscription_todo)				AS subscription_todo,
					subscriptions.subscription_vip_status					AS vip_status
		FROM subscriptions
		LEFT JOIN	event_subscriptions
			ON		event_subscriptions.event_subscription_organisation_entity_id	= subscription_organisation_entity_id
				AND	event_subscriptions.event_subscription_user_entity_id			= subscription_user_entity_id
		LEFT JOIN	event_subscriptions AS default_event_subscription
			ON		default_event_subscription.event_subscription_organisation_entity_id	= subscription_organisation_entity_id
				AND	default_event_subscription.event_subscription_user_entity_id			= 0
		INNER JOIN	organisations
			ON		subscriptions.subscription_organisation_entity_id = organisations.organisation_entity_id
		INNER JOIN	organisation_types
			ON		organisations.organisation_organisation_type_id = organisation_types.organisation_type_id

		INNER JOIN	entities
			ON organisations.organisation_entity_id = entities.entity_id
		WHERE		subscriptions.subscription_user_entity_id = ?
		AND			subscriptions.subscription_deleted = 0 AND entities.entity_deleted = 0
		ORDER BY	organisation_name ASC';

		$query = $this->db->query($sql, $user_id);

		return $query->result_array();
	}

	/**
	 *	Academic
	 */

	function getDepartments()
	{
		$sql =
			'SELECT'.
			' organisation_entity_id AS department_id,'.
			' organisation_name AS department_name '.
			'FROM organisations '.
			'WHERE organisation_organisation_type_id = 7 '.
			'ORDER BY department_name ASC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function isDepartment($DeptId)
	{
		$sql =
			'SELECT'.
			' organisation_entity_id '.
			'FROM organisations '.
			'WHERE organisation_organisation_type_id = 7 '.
			'AND organisation_entity_id = ' . $DeptId;
		$query = $this->db->query($sql);
		if ($query->num_rows() == 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function isModule($ModuleId)
	{
		$sql =
			'SELECT'.
			' organisation_entity_id '.
			'FROM organisations '.
			'WHERE organisation_organisation_type_id = 8 '.
			'AND organisation_entity_id = ' . $ModuleId;
		$query = $this->db->query($sql);
		if ($query->num_rows() == 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function getModules($DeptId)
	{
		$sql =
			'SELECT'.
			' organisation_entity_id AS module_id,'.
			' organisation_name AS module_name '.
			'FROM organisations '.
			'WHERE organisation_organisation_type_id = 8 '.
			'AND organisation_parent_organisation_entity_id = ' . $DeptId . ' '.
			'ORDER BY module_name ASC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function getModuleSubscriptions ($user_id, $subject_id = -1)
	{
		$sql =
			'SELECT'.
			' organisations.organisation_name AS module_name,'.
			' subscriptions.subscription_organisation_entity_id AS module_id '.
			'FROM subscriptions, organisations '.
			'WHERE subscriptions.subscription_user_entity_id = '.$user_id.
			' AND subscriptions.subscription_organisation_entity_id = organisations.organisation_entity_id'.
			' AND subscriptions.subscription_deleted = 0';
		if ($subject_id > -1) {
			$sql .= ' AND organisations.organisation_parent_organisation_entity_id = '.$subject_id;
		}
		$sql .=
			' AND organisations.organisation_organisation_type_id = 8 '.
			'ORDER BY module_name ASC';
		$query = $this->db->query($sql);
		if ($subject_id == -1) {
			return $query->result_array();
		} else {
			$societies = array();
			foreach ($query->result_array() as $row) {
				array_push($societies, $row['module_id']);
			}
			return $societies;
		}
	}

}