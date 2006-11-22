<?php
class Campaign extends Controller {
	function index()
	{
		$subdata = array(
			'Title' => 'This Is The Title',
			'Blurb' => '<b>testing blurb</b> and it <i>works!</i> Yay!'
		);
		$data = array(
			'content_view' => 'campaign/CampaignSelection',
			'subdata' => $subdata
		);
		$this->load->view('frames/StudentFrameCss',$data);
	}
}
?>