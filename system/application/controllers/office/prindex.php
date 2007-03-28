<?php

/**
 * @file prindex.php
 * @brief Main PR page for an organisation.
 */

/// Main PR area for an organisation controller.
/**
 */
class Prindex extends controller
{
	/// Default constructor
	function __construct()
	{
		parent::controller();
	}

	/// Set up the navigation bar
	private function _SetupNavbar()
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('suggestions', 'Suggestions',
				'/office/pr/suggestions');
		$navbar->AddItem('unnassigned', 'Unnassigned',
				'/office/pr/unnassigned');
		$navbar->AddItem('summary', 'Summary',
				'/office/pr/summary');
	}
	
	/// Index page (accessed through /office/pr/org/$organisation)
	function orgindex()
	{
		if (!CheckPermissions('pr')) return;
		$this->pages_model->SetPageCode('office_pr_main');
		$this->main_frame->SetTitleParameters(array(
			'organisation' => VipOrganisationName()
		));
		$this->main_frame->load();
	}

	function index()
	{
		self::summary();
	}

	function suggestions()
	{
		// Not accessed through /office/pr/org/$organisation, not organisation
		// specific so needs to be office permissions.
		if (!CheckPermissions('office')) return;

		$this->_SetupNavbar();
		$this->main_frame->SetPage('suggestions');
		$this->pages_model->SetPageCode('suggestions');
		$data = array();

		// Set up the public frame
		$the_view = $this->frames->view('office/pr/suggestions', $data);
		$this->main_frame->SetContent($the_view);

		// Load the public frame view (which will load the content view)
		$this->main_frame->load();
	}

	function unnassigned()
	{
		// Not accessed through /office/pr/org/$organisation, not organisation
		// specific so needs to be office permissions.
		if (!CheckPermissions('office')) return;

		$this->_SetupNavbar();
		$this->main_frame->SetPage('unnassigned');
		$this->pages_model->SetPageCode('unnassigned');
		$data = array();

		// Set up the public frame
		$the_view = $this->frames->view('office/pr/unnassigned', $data);
		$this->main_frame->SetContent($the_view);

		// Load the public frame view (which will load the content view)
		$this->main_frame->load();
	}

	function summary()
	{
		// Not accessed through /office/pr/org/$organisation, not organisation
		// specific so needs to be office permissions.
		if (!CheckPermissions('office')) return;

		$this->_SetupNavbar();
		$this->main_frame->SetPage('summary');
		$this->pages_model->SetPageCode('summary');
		$data = array();

		// Set up the public frame
		$the_view = $this->frames->view('office/pr/summary', $data);
		$this->main_frame->SetContent($the_view);

		// Load the public frame view (which will load the content view)
		$this->main_frame->load();
	}
}

?>