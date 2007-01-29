<?php

class About extends Controller
{

	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
		$this->pages_model->SetPageCode('about_us');
	}

	function index()
	{
			$data['textblocks'] = array(
				array(
					'shorttitle'   => 'the_website',
					'blurb'        => $this->pages_model->GetPropertyWikitext('the_website'),
					'image' => '/images/prototype/reviews/reviews_07.jpg',
					'image_description'        => 'Image Description',
				),
				array(
					'shorttitle'   => 'our_aims',
					'blurb'        => $this->pages_model->GetPropertyWikitext('our_aims'),
					'image' => '/images/prototype/reviews/reviews_07.jpg',
					'image_description'        => 'Image Description',
				),
			);
		// Set up the public frame
		$this->frame_public->SetContentSimple('about/about', $data);
		$this->frame_public->Load();
	}
}
?>
