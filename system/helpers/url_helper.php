<?php

function site_url($uri = '') {
	$CI =& get_instance();
	return $CI->config->site_url($uri);
}

function base_url() {
	$CI =& get_instance();
	return $CI->config->slash_item('base_url');
}

function index_page() {
	$CI =& get_instance();
	return $CI->config->item('index_page');
}

function redirect($uri = '') {
	header("location:".site_url($uri));
	exit;
}

?>
