<?php

class Listings extends Controller {

	function Listings()
	{
		parent::Controller();	
	}
	
	// default function
	function index()
	{
		// Load my "minitemplater" helper.
		// This is a very basic S&R script
		// : Allows chunks of template code to be parsed without cluttering
		// up the script :)
		$this->load->helper('minitemplater');
		
		// This array get sent to the view listings_view.php
		$data = Array ();
		
		// I don't trust users to set their clocks properly
		$data['server_dt'] = time(); 
		// Set title and other such
		$data['title'] = 'Listing viewer prototype';
		
		// this is temporary for testing only
		$data['days'] = array ();
		$daycalc = array ();
		for ($dayoffset = 0; $dayoffset < 7; $dayoffset++) {
			$dayofweek = date('N',time ()) - 1;
			
			$monday = strtotime ('-'.$dayofweek." day",time());
			
			$day_ts = strtotime ('+'.$dayoffset." day",$monday);
			
			$data['days'][]	= date ("jS M", $day_ts);
			$daycalc[] = date ('d#m#y',$day_ts);
		}
		
		// returns the day of the week that a date falls on if
		// that date is within the range of the current calendar

		
		// define some dummy events with a rough schema until we have access
		// to some real data to play with
		$data['dummies'] = array (
			array (
				'ref_id' => '1',
				'name' => 'House Party',
				'date' => '2006-11-24',
				'day' => $this->get_dow_offset ('2006-11-24',$daycalc),
				'starttime' => '2100',
				'endtime' => '0000',
				'system_update_ts' => '3',
				'user_update_ts' => '2',
				'blurb' => 'Bangin\' house party in my house!',
				'shortloc' => 'my house',
				'type' => 'social'
			),
			array (
				'ref_id' => '2',
				'name' => 'boring lecture about vegetables',
				'date' => '2006-11-21',
				'day' => $this->get_dow_offset ('2006-11-21',$daycalc),
				'starttime' => '1245',
				'endtime' => '1500',
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'this will be well good i promise',
				'shortloc' => 'L/049',
				'type' => 'academic'
			)
		);
		
		
		
		$pass_data['subdata'] = $data;
		$pass_data['content_view'] = "listings/listings";
		// load crazy frame deely		
		$this->load->view('frames/StudentFrameCss',$pass_data);
		
		//$this->load->view('listings_view',$data);
	}
	
	function get_dow_offset ($date,$daycalci) {
		$ts = strtotime ($date);
		foreach ($daycalci as $os => $date) {
			if (date ('d#m#y',$ts) == $date) {
				return $os;
				break;
			}
		}
		return -1;
		break;
	}
}
?>