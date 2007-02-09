<?php

class Dev extends Controller {

	function index()
	{
		if (!CheckPermissions('admin')) return;
		
		$this->pages_model->SetPageCode('admin_status');
		
		$op  = '<a href="/admin/dev/phpinfo">PHP information</a><br />';
		$op .= 'If you think this is wrong then email mg512<br />';
		$op .= 'Info dumps follow:<br /><pre>';
		exec('svn info', $ops);
		$op .= implode("\n",$ops);
		$op .= '<pre />';
		
		$this->main_frame->SetContent(new SimpleView($op));
		$this->main_frame->Load();
	}
	
	function phpinfo()
	{
		if (!CheckPermissions('admin')) return;
		
		phpinfo();
	}
}
?>
