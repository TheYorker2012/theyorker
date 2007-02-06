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
		
		// Get url segments after the first (controller).
		$num_segments = $this->uri->total_segments();
		$segments = array();
		for ($counter = 2; $counter <= $num_segments; ++$counter) {
			$segments[] = $this->uri->segment($counter);
		}
		$PageCodeName = implode('/',$segments);
		// We now have the page code so we can continue.
		
		$this->pages_model->SetPageCode('custom:'.$PageCodeName);
		
		// Get the wikitext
		$content = $this->pages_model->GetPropertyWikitext('main', FALSE);
		if (FALSE === $content) {
			// Either no content or page doesn't exist
			show_404($PageCodeName);
			return;
		}
		
		// Setup main_frame
		$data = array(
				'parsed_wikitext' => $content,
			);
		$this->main_frame->SetContentSimple('pages/custom_page',$data);
		
		// Load the frame
		$this->main_frame->Load();
	}
}

?>