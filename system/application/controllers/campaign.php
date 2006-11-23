<?php
class Campaign extends Controller {
	function index()
	{
		if(1==0){ // change to if deadline not passed then...
			$subdata = array(
				'Description' => '<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!<b>testing blurb</b> and it <i>works!</i> Yay!',
				'DeadLine' => '23 May 2019'
			);
			$data = array(
				'content_view' => 'campaign/CampaignSelection',
				'subdata' => $subdata
			);
			$this->load->view('frames/StudentFrameCss',$data);
		} else { // Campaign is chosen
			$subdata = array(
				'Title' => 'Pie Eating',
				'Picture' => 'www.SomeUrlToImage.com',
				'Summery' => 'This is the campaign summery.This is the campaign summery.This is the campaign summery.This is the campaign summery.This is the campaign summery.This is the campaign summery.This is the campaign summery.This is the campaign summery.This is the campaign summery.This is the campaign summery.This is the campaign summery.This is the campaign summery.',
				'NumOfSignatures' => '89546',
				'Username' => 'Tom Jones'
			);
			$data = array(
				'content_view' => 'campaign/CampaignVote',
				'subdata' => $subdata
			);
			$this->load->view('frames/StudentFrameCss',$data);
		}
	}
	function Details($SelectedCampaign = 'Pie Eating')
	{
		$subdata = array(
			'Title' => $SelectedCampaign,
			'Description' => 'Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!Lets all eat pie!',
			'Author' => 'Mr Jones',
			'Society' => 'Vegetarian Soc',
			'Links' => 'www.ThisIsALink.com',
		);
		$data = array(
			'content_view' => 'campaign/CampaignDetials',
			'subdata' => $subdata
		);
		$this->load->view('frames/StudentFrameCss',$data);
	}
}
?>