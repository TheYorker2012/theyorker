<?php

/**
 * @file controllers/office/members.php
 * @brief Viparea members controller.
 */

/// Viparea members controller.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @author Dave Huscroft
 *
 * Several of the URI's for this controller can have filter data appended.
 * The controller walks through the segments using a state machine to set
 *	filter options.
 *
 * Each filter option follows this format:
 *	- '/not' optional (excludes instead of including the matching members)
 *
 * [Followed by] one of the following.
 *	- '/team/$team_name'  (team $team_name)
 *	- '/paid'             (paid members)
 *	- '/vip'              (vips)
 *	- '/carded'           (with business cards)
 *	- '/carding'          (card requested)
 *	- '/cardable'         (card recieved but not published)
 *	- '/mailable'         (on mailing list)
 *	- '/user/$entity_id'  (specific user)
 *	- '/card/$card_id'    (user with specific card)
 *	- '/search'           (match posted search)
 *
 * Each filter option without '/not' is added as a disjunct.
 * Each filter option which has '/not' is added as a conjunct on top of the
 *	disjunction of '/not'less filter options.
 *
 * e.g. to get users in tech and news team who haven't submitted cards but have
 *	paid the membership fee:
 *	- /team/theyorker_tech/team/theyorker_news/not/carded/not/cardable/paid
 *
 *
 * The structure of the URI's will be described here (probably including a text
 *	file so it doesn't clutter).
 */
class Members extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();
		$this->load->model('directory_model');
		$this->load->model('members_model');
		$this->load->library('organisations');
		$this->load->helper('wikilink');
	}
	
	// FIRST LEVEL OF ROUTING
	// These are public functions representing the first segment after members
	
	/// Index page.
	function index()
	{
		if (!CheckPermissions('vip+pr')) return;
		
		/// @note Redirects to members/list
		redirect(vip_url('members/list'));
	}
	
	/// List of members and member set operations.
	/**
	 * @param $Filter [string] 'filter' (filter options follow).
	 * @note This is accessed with list not memberlist (list is reserved word).
	 */
	function memberlist($Filter = NULL)
	{
		if (!CheckPermissions('vip+pr')) return;
		/// @todo Implement $viparea/members/list/...
		
		$this->pages_model->SetPageCode('viparea_members');		
		
		$data = array(
			'main_text'    => $this->pages_model->GetPropertyWikitext('main_text'),
			'user'         => $this->user_auth->entityId,
			'organisation' => $this->members_model->GetAllMemberDetails(VipOrganisationId()),
			'team'         => $this->members_model->GetTeams($this->user_auth->organisationLogin)
		);
		// Set up the content
		$this->main_frame->SetContentSimple('viparea/members', $data);
		
		// Load the main frame
		$this->main_frame->Load();
	}
	
	/// Specific member information.
	/**
	 * @param $EntityId integer Entity id.
	 * @param $Page [string] Page name.
	 */
	function info($EntityId = NULL, $Page = NULL)
	{
		if (!CheckPermissions('vip+pr')) return;
		/// @todo Implement $viparea/members/info/...
		
		
		$this->pages_model->SetPageCode('viparea_members');	
			
		$data = array(
			'main_text'    => $this->pages_model->GetPropertyWikitext('main_text'),
			'organisation' => $this->members_model->GetAllMemberDetails(VipOrganisationId()),
			'member'       => $this->members_model->GetMemberDetails($EntityId)

		);
		// Set up the content
		$this->main_frame->SetContentSimple('viparea/editmembers', $data);
		
		// Load the main frame
		$this->main_frame->Load();
	}
	
	/// Business card management.
	/**
	 * @param $Suboption1 [string/integer] Operation code or business card id.
	 * @param $Suboption2 [string] Sub operation code.
	 * @param $Suboption3 [string] Another sub operation code.
	 */
	function cards(	$Suboption1 = NULL,
					$Suboption2 = NULL,
					$Suboption3 = NULL)
	{
		if (!CheckPermissions('vip+pr')) return;
		/// @todo Implement $viparea/members/cards/...
		$this->messages->AddMessage('information', 'todo: implement business cards');
		
		// Load the main frame
		$this->main_frame->Load();
	}
	
	/// Invite new members to join.
	/**
	 * @param $Suboption [string] Sub operation:
	 *	- 'post'
	 */
	function invite($Suboption = NULL)
	{
		if (!CheckPermissions('vip+pr')) return;
		/// @todo Implement $viparea/members/invite/...
		$this->messages->AddMessage('information', 'todo: implement invite');
		
		// Load the main frame
		$this->main_frame->Load();
	}
	
	/// Contact members
	/**
	 * @param $Method [string] Method to use.
	 *	- 'notify'
	 *	- 'email'
	 * @param $Operation [string] Operation.
	 *	- 'filter'
	 *	- 'post'
	 */
	function contact($Method = NULL, $Operation = NULL)
	{
		if (!CheckPermissions('vip+pr')) return;
		/// @todo Implement $viparea/members/contact/...
		$this->messages->AddMessage('information', 'todo: implement member contact');
		
		// Load the main frame
		$this->main_frame->Load();
	}
	
	
}

?>