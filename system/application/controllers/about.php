<?php

class About extends Controller
{

	function __construct()
	{
		parent::Controller();
		
		$this->load->helpers('images');
	}

	function index()
	{
		if (!CheckPermissions('public')) return;
		
		$this->pages_model->SetPageCode('about_us');
		
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
		$this->main_frame->SetContentSimple('about/about', $data);
		$this->main_frame->Load();
	}
}
?>
