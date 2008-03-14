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
	function hasPermission($permission)
	{
		$this->getPermissions();
		return isset($this->permissionsCache[$permission]);
	}
	
	/// Get a list of permissions.
	/**
	 * @return array[permission => array[role]]
	 */
	function getPermissions()
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
	
}

?>