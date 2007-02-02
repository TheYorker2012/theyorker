<?php

class About extends Controller
{

	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
		$this->load->helpers('images');
		$this->pages_model->SetPageCode('about_us');
	}

	function index()
	{
		$the_website_image = $this->pages_model->GetPropertyText('the_website');
		$our_aims_image = $this->pages_model->GetPropertyText('our_aims');
		
		$data['textblocks'] = array(
			array(
				'shorttitle'   => 'the_website',
				'blurb'        => $this->pages_model->GetPropertyWikitext('the_website'),
				'image' => photoLocation($the_website_image),
			),
			array(
				'shorttitle'   => 'our_aims',
				'blurb'        => $this->pages_model->GetPropertyWikitext('our_aims'),
				'image' => photoLocation($our_aims_image),
			),
		);
		// Set up the public frame
		$this->frame_public->SetContentSimple('about/about', $data);
		$this->frame_public->Load();
	}
}
?>
