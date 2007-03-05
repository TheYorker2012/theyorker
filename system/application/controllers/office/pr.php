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
		// Not accesses through /office/pr/$organisation, not organisation
		// specific so needs to be office permissions.
		if (!CheckPermissions('office')) return;
		$this->pages_model->SetPageCode('office_pr');
		$this->main_frame->load();
	}
}

?>