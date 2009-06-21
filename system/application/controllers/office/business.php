<?php

/**
 *	@brief	Office homepage 2.0
 *	@author	Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Business extends Controller
{

	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		if (!CheckPermissions('office')) return;
		$this->pages_model->SetPageCode('office_index');
		$data = array();

		// Set up the content
		$this->main_frame->SetData('menu_tab', 'business');
		$this->main_frame->SetContentSimple('office/business', $data);
		$this->main_frame->Load();
	}

}

?>
