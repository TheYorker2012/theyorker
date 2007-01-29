<?php

class Policy extends Controller
{

	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
		$this->pages_model->SetPageCode('our_policy');
	}

	function index()
	{
			$data['textblocks'] = array(
				array(
					'shorttitle'   => 'statement_of_policy',
					'blurb'        => $this->pages_model->GetPropertyWikitext('statement_of_policy'),
					'image' => '/images/prototype/reviews/reviews_07.jpg',
					'image_description'        => 'Image Description',
				),
				array(
					'shorttitle'   => 'privacy_policy',
					'blurb'        => $this->pages_model->GetPropertyWikitext('privacy_policy'),
					'image' => '/images/prototype/reviews/reviews_07.jpg',
					'image_description'        => 'Image Description',
				),
				array(
					'shorttitle'   => 'user_agreement',
					'blurb'        => $this->pages_model->GetPropertyWikitext('user_agreement'),
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