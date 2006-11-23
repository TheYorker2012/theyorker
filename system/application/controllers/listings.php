<?php

class Listings extends Controller {

	function Listings()
	{
		parent::Controller();	
	}
	
	// default function
	function index()
	{
		// This array get sent to the view listings_view.php
		$data = Array ();
		
		// I don't trust users to set their clocks properly
		$data['server_dt'] = time(); 
		// Set title and other such
		$data['title'] = 'Listing viewer prototype';
		
		// define some dummy events with a rough schema until we have access
		// to some real data to play with
		$data['dummies'] = array (
			array (
				'ref_id' => '1',
				'name' => 'House Party',
				'date' => '2006-11-24',
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
				'starttime' => '1245',
				'endtime' => '1500',
				'system_update_ts' => '1',
				'user_update_ts' => '1',
				'blurb' => 'this will be well good i promise',
				'shortloc' => 'L/049',
				'type' => 'academic'
			)
		);
		
		
		$this->load->view('listings_view',$data);
	}
}
?>