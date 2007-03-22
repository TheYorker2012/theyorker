<?php

/**
 * @file controllers/office/notices.php
 * @brief Notices controller.
 *
 * @see http://real.theyorker.co.uk/wiki/Functional:Notices Functional Specification
 *
 * @version 21/03/2007 James Hogan (jh559)
 *	- Created.
 */

/*
views
	noticeboard($Vip = FALSE, $Notices)
	sendnotice($Teams)
	emailsettings
model
	notices model for normal notice stuff, emails, db
	mailing model for mailing from authenticated email addresses + managing those addresses
*/

/// Notices VIP/PR controller.
class Notices extends controller
{
	/// Default constructor.
	function __construct()
	{
		parent::controller();
	}
	
	/// Set up the tabs on the main_frame.
	/**
	 * @param $SelectedPage string Selected Page.
	 * @pre CheckPermissions must have already been called.
	 */
	protected function _SetupTabs($SelectedPage)
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('board', 'The Board',
				vip_url('notices'));
		$navbar->AddItem('compose', 'Compose',
				vip_url('notices/compose'));
		$navbar->AddItem('settings', 'Settings',
				vip_url('notices/settings'));
		
		$this->main_frame->SetPage($SelectedPage);
	}
	
	/// Main function.
	function index()
	{
		$this->board();
	}
	
	/// Display the notice board.
	function board()
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$this->load->helper('string');
		
		$this->pages_model->SetPageCode('viparea_notices');
		$this->_SetupTabs('board');
		
		$data = array(
			'Title' => 'Main '.VipOrganisationName().' Notice Board',
			'Notices' => array(
				array(
					'from_name' => 'James Hogan',
					'from_link' => site_url('login/main'),
					'subject' => 'Cabbages',
					'post_time' => 'yesterday',
					'body' => '<p>It turns out they don\'t have any intellegence after all</p>',
					'delete_link' => site_url('dummy'),
				),
				array(
					'from_name' => 'Joe Hogan',
					'from_link' => site_url('login/main'),
					'subject' => 'Cabbages',
					'post_time' => 'monday',
					'body' => '<p>It turns out they don\'t have any intellegence after all. This lot will be wikitext :) I reckon in wikitext we should parse smilies.</p>',
					'delete_link' => NULL,
				),
				array(
					'from_name' => 'Luke Hogan',
					'from_link' => site_url('login/main'),
					'subject' => 'Cabbages',
					'post_time' => 'last tuesday',
					'body' => '<p>It turns out they don\'t have any intellegence after all</p>',
					'delete_link' => site_url('dummy'),
				),
			),
			'Menu' => array(
				array(
					'name' => 'link name 1',
					'link' => vip_url('notices/board'),
					'quantity' => 3,
					'children' => array(
						array(
							'name' => 'link name 1a',
							'link' => vip_url('notices/board'),
							'quantity' => 13,
						),
						array(
							'name' => 'link name 1b',
							'link' => vip_url('notices/board'),
							'quantity' => 31,
						),
					),
				),
				array(
					'name' => 'link name 2',
					'link' => vip_url('notices/board'),
					'quantity' => 131,
				),
			),
		);
		
		$this->main_frame->SetContentSimple('notices/board',$data);
		
		$this->main_frame->SetTitleParameters(array(
			'organisation' => VipOrganisationName(),
		));
		
		$this->main_frame->Load();
	}
	
	/// Compose a notice.
	function compose()
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$this->pages_model->SetPageCode('viparea_notices_compose');
		$this->_SetupTabs('compose');
		
		$data = array();
		
		$this->main_frame->SetContentSimple('notices/compose',$data);
		
		$this->main_frame->SetTitleParameters(array(
			'organisation' => VipOrganisationName(),
		));
		
		$this->main_frame->Load();
	}
	
	/// Notices settings.
	function settings()
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$this->pages_model->SetPageCode('viparea_notices_settings');
		$this->_SetupTabs('settings');
		
		$data = array();
		
		$this->main_frame->SetContentSimple('notices/settings',$data);
		
		$this->main_frame->SetTitleParameters(array(
			'organisation' => VipOrganisationName(),
		));
		
		$this->main_frame->Load();
	}
}

?>