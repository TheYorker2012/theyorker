<?php

class Dev extends Controller {

	function index()
	{
		if (!CheckPermissions('admin')) return;
		
		$this->pages_model->SetPageCode('admin_status');
		
		$op  = '<a href="/admin/dev/phpinfo">PHP information</a><br />';
		$op .= 'If you think this is wrong then email mg512<br />';
		$op .= 'Info dumps follow:<br /><pre>';
		exec('svn info ..', $ops);
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
	
	function retrieve($what)
	{
		if ($what === 'images') {
			header('Content-type: application/x-gzip');
			$bulk = system('tar czO images/images images/photos --exclude .svn');
			$this->load->view('test/echo',array('content' => $bulk));
		} else {
			show_404();
		}
	}
	
	function log() {
		if (!CheckPermissions('admin')) return;
		
		$this->load->helper('url');
		$bulk = 'Valid logs are '.anchor('admin/dev/log/web/', 'log/web').' and '.anchor('admin/dev/log/irc/', 'log/irc').'.';
		$segments = $this->uri->segment_array();
		switch ($this->uri->segment(4)) {
			case "web":
				$web = dir('../log');
				while (false !== ($entry = $web->read())) {
					if ($entry != '.' or $entry != '..') {
						$bulk.= '<p>'.anchor('admin/dev/log/web/'.$entry, $entry).'</p>';
					}
				}
				if ($this->uri->segment(5)) {
					$bulk.= nl2br(file_get_contents('../log/'.$this->uri->segment(5)));
				}
			break;
			case "irc":
				$irc = dir('../supybot/logs/ChannelLogger/afsmg/#theyorker');
				while (false !== ($entry = $irc->read())) {
						$entry = trim($entry, '#');
						$bulk.= '<p>'.anchor('admin/dev/log/irc/'.$entry, $entry).'</p>';
				}
				if ($this->uri->segment(5)) {
					$bulk.= nl2br(htmlentities(file_get_contents('../supybot/logs/ChannelLogger/afsmg/#theyorker/#'.$this->uri->segment(5))));
				}
			break;
		}
		$this->main_frame->SetContent(new SimpleView($bulk));
		$this->main_frame->SetTitle('Log Viewer');
		$this->main_frame->Load();
	}
	
}
?>
