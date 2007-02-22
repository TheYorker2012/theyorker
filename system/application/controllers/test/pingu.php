<?php
class Pingu extends Controller {

	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}

	function index()
	{
		$this->load->model('pingu_model','pingu_model');
		$this->load->model('campaign_model','campaign_model');
		$this->load->model('article_model','article_model');
		$this->pages_model->SetPageCode('campaign_selection');
		$data = array();

		//$this->article_model->CommitArticle(14, 0, 2, date('y-m-d H:i:s'), 'Costcutter', '', '', 'Costcutter is a franch', '');

/*
		$data['content_types'] = $this->pingu_model->GetContentTypes(0, TRUE);
		if (count($data['content_types']) > 0)
		{
			foreach ($data['content_types'] as $key => $row)
			{
				$data['content_types'][$key]['children'] = $this->pingu_model->GetContentTypes($key, FALSE);
			}
		}
		*/

		// Setup XAJAX functions

		$this->load->library('xajax');
	        $this->xajax->registerFunction(array('_xajaxTest', &$this, '_xajaxTest'));
	        $this->xajax->processRequests();

		// Set up the public frame
		$this->frame_public->SetTitle('test page');
		$this->frame_public->SetContentSimple('test/pingutest', $data);
		$this->frame_public->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	function _xajaxTest($test)
	{
		$xajax_response = new xajaxResponse();
		$xajax_response->addAlert('Please enter an xajaxTest to submit.');
		$xajax_response->addScriptCall('doxajaxTest');
		return $xajax_response;
	}
}
?>