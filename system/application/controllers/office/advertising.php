<?php

class Advertising extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::controller();
	}
	
	/// Default page.
	function index()
	{
		if (!CheckPermissions('vip')) return;
		
		$this->pages_model->SetPageCode('viparea_advertising');
		$this->messages->AddMessage('warning', 'The advertising system isn\'t yet implemented');
		
		$data = array(
		);
		//$this->main_frame->SetContentSimple('viparea/contactpr', $data);
		
		$this->main_frame->Load();
	}
}

?>