<?php

/// About Us page
class About extends Controller
{
	/// Default constructor
	function __construct()
	{
		parent::Controller();
		
		$this->load->helpers('images');
	}

	/// Main page
	function index()
	{
		if (!CheckPermissions('public')) return;
		
		$this->pages_model->SetPageCode('about_us');
		
		// Get the blocks array from page properties.
		$blocks = $this->pages_model->GetPropertyArray('blocks', array(
			// First index is [int]
			array('pre' => '[', 'post' => ']', 'type' => 'int'),
			// Second index is .string
			array('pre' => '.', 'type' => 'enum',
				'enum' => array(
					array('title',	'text'),
					array('blurb',	'wikitext'),
					array('image',	'text'),
				),
			),
		));
		if (FALSE === $blocks) {
			$blocks = array();
		}
		
		// Create data array.
		$data = array();
		$data['textblocks'] = array();
		
		// Process page properties.
		foreach ($blocks as $key => $block) {
			$data['textblocks'][] = array(
				'title'			=> $block['title'],
				'shorttitle'	=> str_replace(' ','_',$block['title']),
				'blurb'			=> $block['blurb'],
				'image'			=> photoLocation($block['image']),
			);
		}
		
		// Load the main frame
		$this->main_frame->SetContentSimple('about/about', $data);
		$this->main_frame->Load();
	}
}
?>
