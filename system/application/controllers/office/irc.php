<?php

/// IRC controller
class Irc extends Controller
{
	/// Main index page
	function index()
	{
		$this->office();
	}
	
	/// Office IRC
	function office()
	{
		if (!CheckPermissions('office')) return;
		$this->_irc_channel('OfficeChat', '#theyorkeroffice', 'main');
	}
	
	/// Dev IRC
	function dev()
	{
		if (!CheckPermissions('office')) return;
		$this->_irc_channel('DevChat', '#theyorker', 'dev');
	}
	
	/// Show an irc channel page
	function _irc_channel($chan, $channel, $mode)
	{
		$this->pages_model->SetPageCode('office_irc');
		$this->main_frame->SetTitleParameters(array(
			'chan' => $chan,
			'channel' => $channel,
		));
		$data = array(
			'Server' => 'irc.afsmg.co.uk',
			'Channel' => $channel,
			'Username' => $this->user_auth->username,
			'Fullname' => $this->user_auth->firstname.' '.$this->user_auth->surname,
			'Help' => $this->pages_model->GetPropertyWikitext("help_$mode"),
			'IrcHelp' => $this->pages_model->GetPropertyWikitext("irc_help"),
		);
		$this->main_frame->SetContentSimple('office/irc/irc', $data);
		$this->main_frame->Load();
	}
}


?>
