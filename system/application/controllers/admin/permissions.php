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
		if (!CheckPermissions('editor')) return;
		if (!CheckRolePermissions('ROLE_PERMISSION_VIEW', 'USER_ROLE_VIEW')) return;
		
		$this->load->config('permissions');
		
		$this->main_frame->IncludeJs('javascript/admin/permissions.js');
		$this->main_frame->IncludeCss('stylesheets/permissions.css');
		$this->main_frame->SetContentSimple('admin/permissions/index', array());
		$this->main_frame->Load();
	}
}

?>