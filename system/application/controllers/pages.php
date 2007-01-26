<?php

/// Controller for serving custom static pages.
class Pages extends Controller
{
	/// Default constructor
	function __construct()
	{
		parent::Controller();
		
		$this->load->library('frame_public');
	}

	/// Takes all input
	function _remap($PageCodeName)
	{
		$this->pages_model->SetPageCode('custom:'.$PageCodeName);
		
		// Get the wikitext
		$content = $this->pages_model->GetPropertyWikitext('main', FALSE);
		if (FALSE === $content) {
			// Either no content or page doesn't exist
			show_404($PageCodeName);
			return;
		}
		
		// Setup frame_public
		$data = array(
				'parsed_wikitext' => $content,
			);
		$this->frame_public->SetContentSimple('pages/custom_page',$data);
		
		// Load the frame
		$this->frame_public->Load();
	}
}

?>