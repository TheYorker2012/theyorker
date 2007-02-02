<?php

class Policy extends Controller
{

	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
		$this->load->helpers('images');
		$this->pages_model->SetPageCode('our_policy');
	}

	function index()
	{
		$statement_of_policy_image = $this->pages_model->GetPropertyText('statement_of_policy');
		$privacy_policy_image = $this->pages_model->GetPropertyText('privacy_policy');
		$user_agreement = $this->pages_model->GetPropertyText('user_agreement');
			$data['textblocks'] = array(
				array(
					'shorttitle'   => 'statement_of_policy',
					'blurb'        => $this->pages_model->GetPropertyWikitext('statement_of_policy'),
					'image' => photoLocation($statement_of_policy_image),
				),
				array(
					'shorttitle'   => 'privacy_policy',
					'blurb'        => $this->pages_model->GetPropertyWikitext('privacy_policy'),
					'image' => photoLocation($privacy_policy_image),
				),
				array(
					'shorttitle'   => 'user_agreement',
					'blurb'        => $this->pages_model->GetPropertyWikitext('user_agreement'),
					'image' => photoLocation($user_agreement),
				),
			);
		// Set up the public frame
		$this->frame_public->SetContentSimple('about/about', $data);
		$this->frame_public->Load();
	}
}
?>