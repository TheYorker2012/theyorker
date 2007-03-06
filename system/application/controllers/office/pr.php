<?php

/**
 * @file pr.php
 * @brief Main PR area of office.
 */

/// Main PR area of office controller.
/**
 */
class Pr extends controller
{
	/// Default constructor
	function __construct()
	{
		parent::controller();
	}
	
	/// Index page (accessed through /office/pr)
	function index()
	{
		// Not accessed through /office/pr/$organisation, not organisation
		// specific so needs to be office permissions.
		if (!CheckPermissions('office')) return;
		$this->pages_model->SetPageCode('office_pr');
		
		// Organisations to list depends on whether editor
		if (PermissionsSubset('editor', GetUserLevel())) {
			// Show all organisations
		} else {
			// Show only pr rep organisations
		}
		
		$this->main_frame->load();
	}
}

?>