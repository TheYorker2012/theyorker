<?php

class ContactPR extends Controller
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
		
		$this->pages_model->SetPageCode('viparea_contactpr');
		$this->messages->AddMessage('warning', 'This page needs review');
		
		$data = array(
			'message_pr_target' => vip_url('contactpr'),
		);
		$this->main_frame->SetContentSimple('viparea/contactpr', $data);
		
		$this->main_frame->Load();
	}
}

?>