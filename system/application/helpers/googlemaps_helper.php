<?php

/**
 * @file googlemaps_helper.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Simple helper functions for interfacing with maps.google.co.uk
 */


/// Generate a google maps url from way points
/**
 * @param $Waypoints array of strings, each describing a location.
 * @return string URL string
 */
function GoogleMapsRouteUrl($waypoints)
{
	/// @pre count(@a $waypoints) > 1
	assert('count($waypoints) > 1');
	
	static $base_url = 'http://maps.google.com/maps';
	
	$start = $waypoints[0];
	$the_rest = array_slice($waypoints,1);
	
	$paramters = array(
		'f=d',
		'hl=en',
		'saddr='.urlencode($start),
		'daddr='.urlencode(implode(' to:',$the_rest)),
	);
	
	return $base_url.'?'.implode('&',$paramters);
}

?>