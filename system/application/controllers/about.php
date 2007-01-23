<?php

class About extends Controller
{

	function __construct()
	{
		parent::Controller();
		
		// Load the public frame
		$this->load->library('frame_public');
	}

	function index()
	{
			$aboutdata = array(
				array(
					'title'   => 'The Website',
					'blurb'        => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus id justo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. Phasellus enim leo, varius eu, sodales non, egestas nec, urna. Etiam lacus orci, molestie ac, malesuada at, ullamcorper eu, tortor. Phasellus fermentum, mauris a ullamcorper porta, dui erat tincidunt arcu, ac lacinia mauris lorem ac est. Sed nunc justo, feugiat sed, ultricies nec, vehicula eget, massa. Nam eget massa ut elit pretium tincidunt. Fusce sollicitudin vulputate tellus. Aliquam erat volutpat. Pellentesque turpis risus, bibendum vel, lobortis eget, facilisis non, risus.',
					'image' => '/images/prototype/reviews/reviews_07.jpg',
					'image_description'        => 'Image Description',
				),
				array(
					'title'   => 'Our Aims',
					'blurb'        => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus id justo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. Phasellus enim leo, varius eu, sodales non, egestas nec, urna. Etiam lacus orci, molestie ac, malesuada at, ullamcorper eu, tortor. Phasellus fermentum, mauris a ullamcorper porta, dui erat tincidunt arcu, ac lacinia mauris lorem ac est. Sed nunc justo, feugiat sed, ultricies nec, vehicula eget, massa. Nam eget massa ut elit pretium tincidunt. Fusce sollicitudin vulputate tellus. Aliquam erat volutpat. Pellentesque turpis risus, bibendum vel, lobortis eget, facilisis non, risus.',
					'image' => '/images/prototype/reviews/reviews_07.jpg',
					'image_description'        => 'Image Description',
				),
			);
			$data['aboutdata']= $aboutdata;
		
		// Set up the public frame
		$this->frame_public->SetTitle('About us');
		$this->frame_public->SetContentSimple('about/about', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}
?>
