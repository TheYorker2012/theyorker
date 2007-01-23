<?php

class Policy extends Controller {

	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}
	
	function index()
	{
			$policydata = array(
				array(
					'title'   => 'Statement of Policy',
					'blurb'        => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus id justo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. Phasellus enim leo, varius eu, sodales non, egestas nec, urna. Etiam lacus orci, molestie ac, malesuada at, ullamcorper eu, tortor. Phasellus fermentum, mauris a ullamcorper porta, dui erat tincidunt arcu, ac lacinia mauris lorem ac est. Sed nunc justo, feugiat sed, ultricies nec, vehicula eget, massa. Nam eget massa ut elit pretium tincidunt. Fusce sollicitudin vulputate tellus. Aliquam erat volutpat. Pellentesque turpis risus, bibendum vel, lobortis eget, facilisis non, risus.',
					'image' => '/images/prototype/reviews/reviews_07.jpg',
					'image_description'        => 'Image Description',
				),
				array(
					'title'   => 'Privacy Policy',
					'blurb'        => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus id justo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. Phasellus enim leo, varius eu, sodales non, egestas nec, urna. Etiam lacus orci, molestie ac, malesuada at, ullamcorper eu, tortor. Phasellus fermentum, mauris a ullamcorper porta, dui erat tincidunt arcu, ac lacinia mauris lorem ac est. Sed nunc justo, feugiat sed, ultricies nec, vehicula eget, massa. Nam eget massa ut elit pretium tincidunt. Fusce sollicitudin vulputate tellus. Aliquam erat volutpat. Pellentesque turpis risus, bibendum vel, lobortis eget, facilisis non, risus.',
					'image' => '/images/prototype/reviews/reviews_07.jpg',
					'image_description'        => 'Image Description',
				),
			);
			$data['policydata']= $policydata;
		
		// Set up the public frame
		$this->frame_public->SetTitle('Our Policy');
		$this->frame_public->SetContentSimple('about/policy', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}
?>
