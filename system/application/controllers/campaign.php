<?php
class Campaign extends Controller {
	function index()
	{
		if(1==1){ // change to if deadline not passed then...
			$data = array(
				'content_view' => 'campaign/CampaignSelection',
				'Description' => '<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!',
				'Picture' => 'http://localhost/images/prototype/campaign/field.jpg',
				'DeadLine' => '23 May 2019'
			);
			$this->load->view('frames/student_frame',$data);
		} else { // Campaign is chosen
			$data = array(
				'content_view' => 'campaign/CampaignVote',
				'Title' => 'Pie Eating',
				'Picture' => 'http://localhost/images/prototype/campaign/field.jpg',
				'Summery' => 'This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. ',
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
	}
	
	function Details($SelectedCampaign = 'Pie Eating')
	{
		$data = array(
			'content_view' => 'campaign/CampaignDetials',
			'Title' => $SelectedCampaign,
			'Description' => 'Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!',
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
			'Summery' => 'This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. This is the campaign summery. ',
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
}
?>