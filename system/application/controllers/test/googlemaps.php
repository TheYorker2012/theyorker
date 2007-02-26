<?php

class Googlemaps extends controller
{
	function __construct()
	{
		parent::controller();
		$this->load->helper('googlemaps');
	}
	
	function index()
	{
		if (!CheckPermissions('admin')) return;
		
		$sql = 'SELECT	locations.location_lat AS lat,
						locations.location_lng AS lng
				FROM locations';
		$query = $this->db->query($sql);
		$locations = $query->result_array();
		
		$waypoints = array();
		foreach ($locations as $location) {
			$waypoints[] = $location['lat'].','.$location['lng'];
		}
		if (count($waypoints) > 1) {
			$link = GoogleMapsRouteUrl($waypoints);
			$this->messages->AddMessage('information','Try <a href="'.$link.'">THIS</a>');
		} else {
			$this->messages->AddMessage('error','Not enough locations');
		}
		
		$this->main_frame->Load();
	}
}

?>