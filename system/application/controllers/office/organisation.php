<?php
/**
 *	@brief	Yorker Organisation Chart
 *	@author Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Organisation extends Controller
{

	function __construct()
	{
		parent::Controller();
	}


	function _remap($chart_type = 0)
	{
		if (!CheckPermissions('office')) return;
		if (!CheckRolePermissions('ORGCHART_VIEW')) return;

		$data = array();
		$this->pages_model->SetPageCode('office_orgchart');
		$data['data'] = $this->pages_model->GetPropertyText('chart_data');
		$data['type'] = $chart_type;

		$this->main_frame->SetContentSimple('office/orgchart/chart', $data);
		$this->main_frame->Load();
	}
}

?>
