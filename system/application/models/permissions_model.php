<?php

/**
 * @file models/permissions_model.php
 * @brief Office role based permissions system.
 * @author James Hogan <james_hogan@theyorker.co.uk>
 */

/// Office role based permissions system model.
class permissions_model extends Model
{
	// *** Variables *** //
	
	protected $permissionsCache = null;
	
	// *** Controller level access functions *** //
	
	/// Find whether the current user has a specific permission.
	/**
	 * @param $permission The parameter to look for.
	 */
	function hasUserPermission($permission)
	{
		$this->getPermissions();
		return isset($this->permissionsCache[$permission]);
	}
	
	/// Get a list of permissions.
	/**
	 * @return array[permission => array[role]]
	 */
	function getUserPermissions()
	{
		if (null === $this->permissionsCache) {
			$this->permissionsCache = array();
			
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
			
			$query = $this->db->query(
				'	SELECT		role_permission_permission_name AS permission,'.
				'				role_permission_role_name       AS role'.
				'	FROM		role_permissions'.
				'	LEFT JOIN	user_roles'.
				'		ON		user_role_role_name = role_permission_role_name'.
				'			AND	user_role_user_entity_id = ?'.
				'	WHERE		user_role_user_entity_id IS NOT NULL'.
				(	empty($implicitRoles)
					? ''
					: '	OR	role_permission_role_name IN ("'.implode('","', $implicitRoles).'")'
				),
				array(
					$this->user_auth->entityId
				)
			);
			foreach ($query->result_array() as $row) {
				$this->permissionsCache[$row['permission']][] = $row['role'];
			}
		}
		return $this->permissionsCache;
		
	}
	
	// *** Permissions interface level functions *** //
	
	/// Get all permissions from config.
	/**
	 * @return array[permission => description] All existing permissions.
	 */
	function getAllPermissions()
	{
		return $this->config->Item('permissions');
	}
	
	/// Get all roles permissions.
	function getAllRolePermissions()
	{
		$query = $this->db->query(
			'	SELECT	role_permission_role_name       AS role,'.
			'			role_permission_permission_name AS permission'.
			'	FROM	role_permissions'
		);
		$results = array();
		foreach ($query->result_array() as $row) {
			$results[$row['role']][] = $row['permission'];
		}
		return $results;
	}
	
	/// Get roles which are implicit and not modifiable by role-based permissions.
	/**
	 * @return array[role] Implicit roles
	 */
	function getImplicitRoles()
	{
		return array(
			'LEVEL_OFFICER' => true,
			'LEVEL_EDITOR'  => true,
			'LEVEL_ADMIN'   => true,
		);
	}
	
	/// Get all user roles.
	/**
	 * @return array(array[username]=>array[role], array[username=>fulname]) User information.
	 * @todo Get implicit roles.
	 */
	function getAllUserRoles()
	{
		$query = $this->db->query(
			'	SELECT	user_role_role_name                               AS role,'.
			'			entity_username                                   AS username,'.
			'			CONCAT(user_firstname, " ", user_surname)         AS fullname,'.
			'			(user_office_password IS NOT NULL)                AS editor,'.
			'			(user_office_password IS NOT NULL AND user_admin) AS admin'.
			'	FROM	users'.
			'	INNER JOIN	entities'.
			'			ON	entity_id = user_entity_id'.
			'	LEFT  JOIN	user_roles'.
			'			ON	user_entity_id = user_role_user_entity_id'.
			'	WHERE	user_office_access = true'
		);
		$userRoles = array();
		$userNames = array();
		foreach ($query->result_array() as $row) {
			if (!isset($userRoles[$row['username']])) {
				$userRoles[$row['username']] = array();
				// Only needs adding once per user
				$userRoles[$row['username']][] = 'LEVEL_OFFICER';
				if ($row['editor']) {
					$userRoles[$row['username']][] = 'LEVEL_EDITOR';
				}
				if ($row['admin']) {
					$userRoles[$row['username']][] = 'LEVEL_ADMIN';
				}
				// set name
				$userNames[$row['username']] = $row['fullname'];
			}
			// add each role from database
			if (NULL !== $row['role']) {
				$userRoles[$row['username']][] = $row['role'];
			}
		}
		return array($userRoles, $userNames);
	}
}

?>