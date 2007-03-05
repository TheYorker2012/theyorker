<?

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
	
	/// Index page (accessed through /office/pr/$organisation)
	function index()
	{
		if (!CheckPermissions('pr')) return;
		$this->pages_model->SetPageCode('office_pr_main');
		$this->main_frame->SetTitleParameters(array(
			'organisation' => VipOrganisationName()
		));
		$this->main_frame->load();
	}
}

?>