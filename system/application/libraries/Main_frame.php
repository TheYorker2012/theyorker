<?php

global $chosenMainFrameLibrary;

$CI = & get_instance();
$CI->load->library($chosenMainFrameLibrary);

// capitalise first letter
$mainFrameClassName = strtoupper(substr($chosenMainFrameLibrary,0,1)).substr($chosenMainFrameLibrary, 1);

// Check the class exists.
// This should ensure that closenMainFrameLibrary is contained if it contains
// malicious php code.
if (!class_exists($mainFrameClassName))
{
	exit('Chosen frame class does not exist.');
}

// Define a simple dynamic derived class
eval(
	'class Main_frame extends '.$mainFrameClassName.' {}'
);

?>