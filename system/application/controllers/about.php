<?php

class About extends Controller
{

	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
		$this->load->helpers('images_helper');
		$this->pages_model->SetPageCode('about_us');
	}

	function index()
	{
		$the_website_image = $this->pages_model->GetPropertyText('the_website');
		$our_aims_image = $this->pages_model->GetPropertyText('our_aims');
		
		$data['textblocks'] = array(
			array(
				'shorttitle'   => 'the_website',
				'blurb'        => $this->pages_model->GetPropertyWikitext('the_website').$the_website_image,
				'image' => '/images/photos/null.jpg#'.$the_website_image,
			),
			array(
				'shorttitle'   => 'our_aims',
				'blurb'        => $this->pages_model->GetPropertyWikitext('our_aims'),
				'image' => '/images/photos/null.jpg',//$this->images_helper->photoLocation()
			),
		);
		// Set up the public frame
		$this->frame_public->SetContentSimple('about/about', $data);
		$this->frame_public->Load();
	}
}
?>
