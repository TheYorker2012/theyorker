<?php
class Campaign extends Controller {
	function index()
	{
		$subdata = array(
			'content_view' => 'asdf',
		);
		$data = array(
			'content_view' => 'campaign/CampaignSelection',
			'subdata' => $subdata
		);
		$this->load->view('frames/StudentFrameCss',$data);
	}
}
?>