<?php

/**
 * @file organisation.php
 * @brief Wizard for organisation suggestion.
 */

/**
 */
class Organisation extends controller
{
	/// Default constructor
	function __construct()
	{
		parent::controller();
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;

		$this->main_frame->SetPage('wizard_organisation');
		$this->pages_model->SetPageCode('wizard_organisation');

		$data = array();

		// Set up the public frame
		$the_view = $this->frames->view('wizard/organisation', $data);
		$this->main_frame->SetContent($the_view);

		// Load the public frame view (which will load the content view)
		$this->main_frame->load();
	}
}
?>