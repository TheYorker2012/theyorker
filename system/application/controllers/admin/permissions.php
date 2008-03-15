<?php

/**
 * @file controllers/admin/permissions.php
 * @brief Permissions interface for role based office permissions.
 * @author James Hogan <james_hogan@theyorker.co.uk>
 */

/// Permissions interface controller.
class Permissions extends Controller
{
	function index()
	{
		if (!CheckPermissions('office')) return;
		if (GetUserLevel() != 'admin') {
			if (!CheckRolePermissions('ROLE_PERMISSION_VIEW', 'USER_ROLE_VIEW')) return;
		}
		else {
			$this->load->model('permissions_model');
		}
		
		$this->load->config('permissions');
		
		$this->main_frame->IncludeJs('javascript/simple_ajax.js');
		$this->main_frame->IncludeJs('javascript/admin/permissions.js');
		$this->main_frame->IncludeCss('stylesheets/permissions.css');
		$this->main_frame->SetContentSimple('admin/permissions/index', array());
		$this->main_frame->Load();
	}
	
	function update()
	{
		if (!CheckPermissions('office', false)) return;
		if (GetUserLevel() != 'admin') {
			if (!CheckRolePermissions('ROLE_PERMISSION_VIEW', 'USER_ROLE_VIEW')) return;
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