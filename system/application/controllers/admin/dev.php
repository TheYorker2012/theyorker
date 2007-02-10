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
	
	function log() {
		if (!CheckPermissions('admin')) return;
		
		$this->load->helper('url');
		$bulk = '';
		switch ($this->uri->rsegment(4, 0)) {
			case 0:
				$bulk = 'valid logs are: log/web and log/irc';
			break;
			case 'web':
				$web = dir('../log');
				while (false !== ($entry = $web->read())) {
					if ($entry != '.' or $entry != '..') {
						$bulk.= '<p>'.anchor('admin/dev/log/web/'.$entry, $entry).'</p>';
					}
				}
				if ($this->uri->rsegment(5, FALSE)) {
					$bulk.= nl2br(file_get_contents('../log/'.$this->uri->segment(4)));
				}
			break;
			case 'irc':
				$irc = dir('../supybot/logs/ChannelLogger/afsmg/#theyorker');
				while (false !== ($entry = $irc->read())) {
					if ($entry != '.' or $entry != '..') {
						$bulk.= '<p>'.anchor('admin/dev/log/irc/'.$entry, $entry).'</p>';
					}
				}
				if ($this->uri->rsegment(5, FALSE)) {
					$bulk.= nl2br(file_get_contents('../supybot/logs/ChannelLogger/afsmg/#theyorker/'.$this->uri->segment(4)));
				}
			break;
		}
		$this->main_frame->SetContent(new SimpleView($bulk));
		$this->main_frame->SetTitle('Log Viewer');
		$this->main_frame->Load();
	}
	
}
?>
