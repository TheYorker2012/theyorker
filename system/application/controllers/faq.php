<?php

class Faq extends Controller {

	function __construct()
	{
		parent::Controller();
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;
		
		$this->pages_model->SetPageCode('faq');
		
		$data = array();
		
		$data['faq'] = $this->pages_model->GetPropertyArray('faq', array(
			// First index is [int]
			array('pre' => '[', 'post' => ']', 'type' => 'int'),
			// Second index is .string
			array('pre' => '.', 'type' => 'enum',
				'enum' => array(
					array('question',	'text'),
					array('answer',		'wikitext'),
				),
			),
		));
		if (FALSE === $data['faq']) {
			$data['faq'] = array();
		}
		
		// Set up the public frame
		$this->main_frame->SetContentSimple('faq/faq', $data);
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
    }
}
?>
