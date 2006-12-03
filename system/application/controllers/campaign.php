<?php
class Campaign extends Controller {
	function index()
	{
		if(1==1){ // change to if deadline not passed then...
			$data = array(
				'content_view' => 'campaign/CampaignSelection',
				'Description' => 'Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text.',
				'Picture' => 'http://localhost/images/prototype/campaign/field.jpg',
				'DeadLine' => '23 May 2019'
			);
			$this->load->view('frames/student_frame',$data);
		} else { // Campaign is chosen
			//curently the test fuction
		}
	}
	
	function Details($SelectedCampaign = 'Pie Eating')
	{
		$data = array(
			'content_view' => 'campaign/CampaignDetials',
			'Title' => $SelectedCampaign,
			'Description' => 'Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text.',
			'Author' => 'Mr Jones',
			'Society' => 'Vegetarian Soc',
			'Links' => 'www.ThisIsALink.com',
			'Picture' => 'http://localhost/images/prototype/campaign/field.jpg'
		);
		$this->load->view('frames/student_frame',$data);
	}
	
	function Test($SelectedCampaign = 'Pie Eating')
	{
		$data = array(
			'content_view' => 'campaign/CampaignVote',
			'Title' => 'Pie Eating',
			'Picture' => 'http://localhost/images/prototype/campaign/field.jpg',
			'Summery' => 'Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text. Descriptive Text.',
			'NumOfSignatures' => '89546',
			'Username' => 'Tom Jones',
			'ProgressItems' => array(
				array('good'=>'y','details'=>'Progress Report 1, Progress Report 1, Progress Report 1, Progress Report 1, Progress Report 1.'),
				array('good'=>'n','details'=>'Progress Report 2, Progress Report 2, Progress Report 2, Progress Report 2, Progress Report 2.'),
				array('good'=>'n','details'=>'Progress Report 3, Progress Report 3, Progress Report 3, Progress Report 3, Progress Report 3.')
				)
		);
		$this->load->view('frames/student_frame',$data);
	}
	
	function Edit($SelectedCampaign = '')
	{
		if($SelectedCampaign == ''){
			$data = array(
				'content_view' => 'campaign/CampaignsEditSelect'
			);
			$this->load->view('frames/student_frame',$data);
		} else {
			$data = array(
				'content_view' => 'campaign/CampaignsEditDetials',
				'CampaignTitle' => $SelectedCampaign
			);
			$this->load->view('frames/student_frame',$data);
		}
	}

}
?>