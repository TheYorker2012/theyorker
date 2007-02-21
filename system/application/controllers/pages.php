<?php

/// Controller for serving custom static pages.
class Pages extends Controller
{
	/// Default constructor
	function __construct()
	{
		parent::Controller();
	}

	/// Takes all input
	/**
	 * @param $PageCodeName string Path to custom page (corresponding with codename).
	 */
	function _remap($DummyRequired)
	{
		if (!CheckPermissions('public')) return;
		
		$this->load->library('custom_pages');
		
		// Get url segments after the first (controller).
		$num_segments = $this->uri->total_segments();
		$segments = array();
		for ($counter = 2; $counter <= $num_segments; ++$counter) {
			$segments[] = $this->uri->segment($counter);
		}
		$PageCodeName = implode('/',$segments);
		// We now have the page code so we can continue.
		
		$custom_page = new CustomPageView($PageCodeName, 'custom');
		$this->main_frame->SetContent($custom_page);
		
		// Load the frame
		$this->main_frame->Load();
	}
}

?>