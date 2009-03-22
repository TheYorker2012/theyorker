<?php

class Dev extends Controller {

	function index()
	{
		if (!CheckPermissions('admin')) return;
		
		$this->pages_model->SetPageCode('admin_status');
		
		$op  = '<a href="/admin/dev/phpinfo">PHP information</a><br />';
		$commands = array(
			'Branch info' => 'git branch',
			'Recent commits' => 'git log HEAD~5..HEAD',
		);
		foreach ($commands as $name => $command) {
			$ops = array();
			$return = -1;
			exec($command.' 2>&1', $ops, $return);
			$op .= '<hr /><h2>'.xml_escape($name).' (`'.xml_escape($command).'`='.$return.')</h2><p>';
			// Join and escape
			$text = xml_escape(implode("\n",$ops));
			// Turn any git hashes into links to gitweb
			$text = preg_replace('/([a-f0-9]{40})/','<a href="http://dev2.theyorker.co.uk/gitweb?p=theyorker;a=commit;h=$1">$1</a>', $text);
			// Newlines into html
			$text = str_replace("\n",'<br />', $text);
			$op .= $text;
			$op .= '</p>';
		}
		
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
		if (!CheckPermissions('admin')) return;
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
					$bulk.= nl2br(xml_escape(file_get_contents('../supybot/logs/ChannelLogger/afsmg/#theyorker/#'.$this->uri->segment(5))));
				}
			break;
		}
		$this->main_frame->SetContent(new SimpleView($bulk));
		$this->main_frame->SetTitle('Log Viewer');
		$this->main_frame->Load();
	}
	
}
?>
