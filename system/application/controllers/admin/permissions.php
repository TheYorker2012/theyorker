<?php

/**
 * @file controllers/admin/permissions.php
 * @brief Permissions interface for role based office permissions.
 * @author James Hogan <james_hogan@theyorker.co.uk>
 */

/// Permissions interface controller.
class Permissions extends Controller
{
	/// Main permissions interface.
	function index()
	{
		if (!CheckPermissions('office')) return;
		// Allow admins to do this, in case somebody screws with permissions.
		if (GetUserLevel() != 'admin') {
			if (!CheckRolePermissions('PERMISSIONS_VIEW')) return;
		}
		else {
			$this->load->model('permissions_model');
		}
		
		$data = array();
		
		$data['permissionDescriptions'] = $this->permissions_model->getAllPermissions();
		$data['rolePermissions'] = $this->permissions_model->getAllRolePermissions();
		$data['implicitRoles'] = $this->permissions_model->getImplicitRoles();
		list($data['userRoles'], $data['userNames']) = $this->permissions_model->getAllUserRoles();
		
		// Roles might not have permissions
		foreach ($data['userRoles'] as $roles) {
			foreach ($roles as $role) {
				if (!isset($data['rolePermissions'][$role])) {
					$data['rolePermissions'][$role] = array();
				}
			}
		}
		
		$this->main_frame->IncludeJs('javascript/simple_ajax.js');
		$this->main_frame->IncludeJs('javascript/admin/permissions.js');
		$this->main_frame->IncludeCss('stylesheets/permissions.css');
		$this->main_frame->SetContentSimple('admin/permissions/index', $data);
		$this->main_frame->Load();
	}
	
	/// Role permissions exporter.
	function export()
	{
		if (!CheckPermissions('admin')) return;
		$this->load->model('permissions_model');
		
		$generateTextRoles = true;
		if (isset($_POST['role_permissions']) && is_string($_POST['role_permissions'])) {
			$textRoles = $_POST['role_permissions'];
			$lines = explode("\n", $textRoles);
			$rolePermissions = array();
			$fail = false;
			foreach ($lines as $count => $line) {
				if (strlen($line)) {
					if (preg_match('/^\s*"([^"]+)"\s*:((\s*"[^"]+")*)\s*$/', $line, $matches)) {
						$escaped_role = $matches[1];
						$role = xml_unescape($escaped_role);
						if (!preg_match('/^\w*$/', $role)) {
							$this->messages->AddMessage('error', 'Invalid role: "'.$escaped_role.'" on line '.($count+1).'.');
							$fail = true;
						}
						$permissionsString = $matches[2];
						$permissionsSparse = explode('"', $permissionsString);
						$permissions = array();
						foreach ($permissionsSparse as $id => $permission) {
							if ($id % 2 == 1) {
								$unescaped_permission = xml_unescape($permission);
								if (!preg_match('/^\w*$/', $unescaped_permission)) {
									$this->messages->AddMessage('error', 'Invalid permission: "'.$permission.'" in role "'.$escaped_role.'" on line '.($count+1).'.');
									$fail = true;
								}
								$permissions[] = $unescaped_permission;
							}
						}
						$rolePermissions[$role] = $permissions;
					}
					else {
						$this->messages->AddMessage('error', 'Syntax error on line '.($count+1).'.');
						$fail = true;
					}
				}
			}
			if (!$fail) {
				$this->permissions_model->clearRolePermissions();
				$this->permissions_model->addRolePermissions($rolePermissions);
				$this->messages->AddMessage('success', 'Role permissions replaced');
			}
			else {
				$generateTextRoles = false;
			}
		}
		
		if ($generateTextRoles) {
			$rolePermissions = $this->permissions_model->getAllRolePermissions();
			
			$textRoles = '';
			foreach ($rolePermissions as $role => $permissions) {
				$textRoles .= ('"'.xml_escape($role).'":');
				foreach ($permissions as $permission) {
					$textRoles .= (' "'.xml_escape($permission).'"');
				}
				$textRoles .= ("\n");
			}
		}
		
		$data = array( 'textRoles' => $textRoles );
			
		$this->main_frame->SetContentSimple('admin/permissions/export_roles', $data);
		$this->main_frame->Load();
	}
	
	/// AJAX Updater.
	function update()
	{
		if (!CheckPermissions('office', false)) return;
		// Allow admins to do this, in case somebody screws with permissions.
		if (GetUserLevel() != 'admin') {
			if (!CheckRolePermissions('PERMISSIONS_MODIFY_ROLES')) return;
		}
		else {
			$this->load->model('permissions_model');
		}
		
		
		// Confirm changes
		if (isset($_GET['roles'])) {
			$roleChanges = $_GET['roles'];
			if (isset($roleChanges[1])) {
				$this->permissions_model->removeRolePermissions($roleChanges[1]);
			}
			if (isset($roleChanges[0])) {
				$this->permissions_model->addRolePermissions($roleChanges[0]);
			}
		}
		if (isset($_GET['users'])) {
			$userChanges = $_GET['users'];
			if (isset($userChanges[1])) {
				$this->permissions_model->removeUserRoles($userChanges[1]);
			}
			if (isset($userChanges[0])) {
				$this->permissions_model->addUserRoles($userChanges[0]);
			}
		}
		
	}
}

?>