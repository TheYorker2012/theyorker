<?php

/**
 * @file controllers/office/members.php
 * @brief Viparea members controller.
 */

/// Viparea members controller.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @author Dave Huscroft (dgh500@york.ac.uk)
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
	/// associative array All teams indexed by entity id
	protected $mAllTeams;
	
	/// teams in a tree structure including main organisations
	protected $mOrganisation;
	
	/// Default constructor.
	function __construct()
	{
		parent::Controller();
		$this->load->model('directory_model');
		$this->load->model('organisation_model');
		$this->load->model('members_model');
		$this->load->library('organisations');
		
		$this->mAllTeams = NULL;
		$this->mOrganisation = NULL;
	}
	
	/// Load the teams up to a specific depth.
	/**
	 * @param $Depth int To pass into members_model::GetTeams.
	 * @note Results stored in mAllTeams and mOrganisation.
	 */
	protected function _GetTeams($Depth = NULL)
	{
		list($this->mAllTeams, $this->mOrganisation)
			= $this->organisation_model->GetTeamsTree(VipOrganisationId());
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
		
		$this->_GetTeams();
		
		// Check for post data
		if (!empty($_POST)) {
			$selected_members = $this->input->post('members_selected');
			// Reindex selected members
			if (is_array($selected_members)) {
				foreach ($selected_members as $key => $member_name) {
					if (preg_match('/^user(\d+)$/',$member_name,$matches)) {
						$selected_members[$key] = (int)$matches[1];
					}
				}
			} else {
				$selected_members = array();
			}
			
			// Check which button was pressed
			$invite_check        = $this->input->post('members_select_invite_button');
			$request_cards_check = $this->input->post('members_select_request_cards_button');
			$unsubscribe_check   = $this->input->post('members_select_unsubscribe_button');
			if ($invite_check === 'Invite') {
				if (empty($selected_members)) {
					$this->messages->AddMessage('information',
						'No members selected, please use the check boxes to select the members that you wish to invite into a team.');
				} else {
					$invite_team = $this->input->post('invite_team');
					if (FALSE !== $invite_team && is_numeric($invite_team)) {
						$invite_team = (int)$invite_team;
						if (array_key_exists($invite_team, $this->mAllTeams)) {
							$this->_InviteUsers(
								$invite_team, $selected_members,
								'id', $this->mAllTeams[$invite_team]['name']
							);
						} else {
							$this->messages->AddMessage('error', 'Invalid post data, not a valid invite team.');
						}
					} else {
						$this->messages->AddMessage('error', 'Invalid post data, no recognised invite team.');
					}
				}
				
			} elseif ($request_cards_check === 'Request business cards') {
				if (empty($selected_members)) {
					$this->messages->AddMessage('information',
						'No members selected, please use the check boxes to select the members that you wish to request business cards from.');
				} else {
					
				}
				
			} elseif ($unsubscribe_check === 'Unsubscribe') {
				if (empty($selected_members)) {
					$this->messages->AddMessage('information',
						'No members selected, please use the check boxes to select the members that you wish to unsubscribe.');
				} else {
					
				}
				
			} else {
				$this->messages->AddMessage('error', 'Invalid post data, no recognised button signiture.');
			}
		}
		
		$filter_base = 'members/list';
		$last_sort = '';
		$sort_fields = array();
		$filter_descriptors = array();
		
		$filter_on = FALSE;
		if (NULL !== $Filter) {
			static $field_translator = array(
				'team'			=> 'NULL',
				'user'			=> 'users.user_entity_id',
				'card'			=> 'NULL',
				'paid' 			=> 'subscriptions.subscription_paid',
				'vip'			=> 'subscriptions.subscription_vip',
				'confirmed'		=> 'subscriptions.subscription_user_confirmed',
				'carded'		=> 'NULL',
				'carding'		=> 'NULL',
				'cardable'		=> 'NULL',
				'mailable'		=> 'subscriptions.subscription_email',
				'search'		=> 'NULL',
				'firstname'		=> 'users.user_firstname',
				'surname'		=> 'users.user_surname',
				'nickname'		=> 'users.user_nickname',
				'enrol_year'	=> 'users.user_enrolled_year',
			);
			try {
				// Process the filter url
				$filter = $this->_GetFilter(3);
				
				// Produce sql, the base url for extra filters, and sort fields
				$filter_base .= '/'.implode('/', $this->_ReconstructFilter($filter));
				/*if (vip_url($filter_base) !== $this->uri->uri_string()) {
					// If the generated url is different, redirect to it as it is neater
					return redirect(vip_url($filter_base));
				}*/
				$sql = $this->_GenerateFilterSql($filter, $field_translator);
				$sort_fields = $this->_ReindexFilterSorts($filter);
				$filter_descriptors = $this->_DescribeFilters($filter);
				// Use db to get members using filter.
				$members = $this->members_model->GetMemberDetails(VipOrganisationId(), NULL, $sql[0], $sql[1]);
				$filter_on = TRUE;
				$last_sort = $filter['_data']['last_sort'];
			} catch (Exception $e) {
				$this->messages->AddMessage('error','The filter is invalid: '.$e->getMessage());
			}
		}
		
		if (!isset($members)) {
			$members = $this->members_model->GetMemberDetails(VipOrganisationId());
		}
		
		$this->pages_model->SetPageCode('viparea_members_list');
		$data = array(
			'main_text'    => $this->pages_model->GetPropertyWikitext('main_text'),
			'target'       => $this->uri->uri_string(),
			'members'      => $members,
			'organisation' => $this->mOrganisation,
			'filter'       => array(
				'enabled'      => $filter_on,
				'last_sort'    => $last_sort,
				'base'         => $filter_base,
				'descriptors'  => $filter_descriptors,
			),
			'sort_fields'  => $sort_fields,
		);
		// Set up the content
		$this->main_frame->SetContentSimple('members/members', $data);
		
		// Set the title parameters
		$this->main_frame->SetTitleParameters(array(
			'organisation'	=> $this->user_auth->organisationName,
		));
		
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
		
		// If no entity id was provided, redirect back to members list.
		if (NULL === $EntityId) {
			return redirect(vip_url('members/list'));
		}
		
		// Get membership information
		// This will determine whether the entity is a member.
		$membership = $this->members_model->GetMemberDetails(VipOrganisationId(), $EntityId);
		
		if (!empty($membership)) {
			$membership = $membership[0];
			
			// Read the post data
			$button = $this->input->post('member_update');
			if ($button === 'Update') {
				$member_paid	= (FALSE !== $this->input->post('member_paid'));
				$member_vip		= (FALSE !== $this->input->post('member_vip'));
				
				$changes = array();
				if ($member_paid !== (bool)$membership['paid']) {
					// Paid has changed
					$membership['paid'] = $changes['paid'] = $member_paid;
				}
				if ($member_vip !== (bool)$membership['vip']) {
					// Vip status has changed
					$membership['vip'] = $changes['vip'] = $member_vip;
				}
				
				// If changes save them
				// If no changes don't save them
				if (empty($changes)) {
					$this->messages->AddMessage('information','No changes were made to the membership.');
				} else {
					/// @todo Do in single update to db
					$successes = array();
					$failures = array();
					if (array_key_exists('paid', $changes)) {
						$num_changes = $this->members_model->UpdatePaidStatus(
							$changes['paid']?'1':'0',
							$EntityId, VipOrganisationId());
						if ($num_changes) {
							$successes[] = 'paid';
						} else {
							$failures[] = 'paid';
						}
					}
					if (array_key_exists('vip', $changes)) {
						$num_changes = $this->members_model->UpdateVipStatus(
							$changes['vip']?'1':'0',
							$EntityId, VipOrganisationId());
						if ($num_changes) {
							$successes[] = 'vip';
						} else {
							$failures[] = 'vip';
						}
					}
					if (!count($failures)) {
						$this->messages->AddMessage('success',
								'The membership\'s '.implode(', ',$successes).' flags were successfully updated');
					} elseif (!count($successes)) {
						$this->messages->AddMessage('error',
								'The membership\'s '.implode(', ',$failures).' flags could not be updated');
					} else {
						$this->messages->AddMessage('error',
								'The membership\'s '.implode(', ',$failures).' flags could not be updated (the flags '.
								implode(', ',$successes).' were successfully updated)');
					}
				}
			} 
			
			// DISPLAY USER INFORMATION --------------------------------- //
			$this->pages_model->SetPageCode('viparea_members_info');
			
			// Stringify gender
			$membership['gender'] =  (($membership['gender']=='m')?('male')
									:(($membership['gender']=='f')?('female')
									:('unknown')));
			// Stringify status
			if (!$membership['confirmed']) {
				$membership['status'] = 'Invited but unconfirmed';
			} elseif ($membership['vip']) {
				$membership['status'] = 'VIP member';
			} elseif ($membership['paid']) {
				$membership['status'] = 'Paying member';
			} else {
				$membership['status'] = 'Member';
			}
			
			$data = array(
				'main_text'    => $this->pages_model->GetPropertyWikitext('main_text'),
				'membership'   => $membership,
			);
			// Set up the content
			$this->main_frame->SetContentSimple('members/editmembers', $data);
			
			// Set the title parameters
			$this->main_frame->SetTitleParameters(array(
				'organisation'	=> $this->user_auth->organisationName,
				'firstname'		=> $membership['firstname'],
				'surname'		=> $membership['surname'],
			));
			
		} else {
			// The entity isn't a member of the organisation
			$this->load->library('custom_pages');
			$this->main_frame->SetContent(new CustomPageView('vip_members_notmember','error'));
		}
		$this->main_frame->Load();
	}
	
	/// Team management.
	/**
	 * @param $Suboption1 [string/integer] Operation code or team id.
	 *	- 'new'
	 * @param $Suboption2 [string] Sub operation code.
	 *	- 'edit'
	 */
	function teams(	$Suboption1 = NULL,
					$Suboption2 = NULL)
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$this->_GetTeams();
		
		$team_id = VipOrganisationId();
		
		if (NULL !== $Suboption1) {
			if (is_numeric($Suboption1) && array_key_exists((int)$Suboption1,$this->mAllTeams)) {
				$team_id = (int)$Suboption1;
				$this->mOrganisation = &$this->mAllTeams[$team_id];
			} else {
				// Show custom error page for no existing team
				$this->load->library('custom_pages');
				$this->main_frame->SetContent(new CustomPageView('vip_members_notteam','error'));
				$this->main_frame->Load();
				return;
			}
		}
		
		$this->pages_model->SetPageCode('viparea_members_teams');
		
		$this->load->model('notices_model');
		$notices = $this->notices_model->GetPublicNoticesForOrganisation($team_id, NULL, FALSE);
		$this->messages->AddDumpMessage('notices',$notices);
		
		$data = array(
			'main_text'    => $this->pages_model->GetPropertyWikitext('main_text'),
			'organisation' => $this->mOrganisation,
		);
		
		$this->main_frame->SetContentSimple('members/teams', $data);
		
		// Set the title parameters
		$this->main_frame->SetTitleParameters(array(
			'organisation'	=> $this->user_auth->organisationName,
		));
		
		// Load the main frame
		$this->main_frame->Load();
	}
	
	/// Business card management.
	/**
	 * @param $Suboption1 [string/integer] Operation code or business card id.
	 *	- 'filter'
	 *	- 'request'
	 *	- 'new'
	 * @param $Suboption2 [string] Sub operation code.
	 *	- 'filter'
	 *	- 'send'
	 *	- 'post'
	 *	- 'edit'
	 * @param $Suboption3 [string] Another sub operation code.
	 */
	function cards(	$Suboption1 = NULL,
					$Suboption2 = NULL,
					$Suboption3 = NULL)
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$this->load->helper('images');
		
		$mode = 'view';
		
		$sql = array('TRUE',array());
		if ($Suboption1 === 'filter') {
			static $field_translator = array(
				'team'			=> 'NULL',
				'user'			=> 'business_cards.business_card_user_entity_id',
				'card'			=> 'business_cards.business_card_id',
				'paid' 			=> 'subscriptions.subscription_paid',
				'vip'			=> 'subscriptions.subscription_vip',
				'confirmed'		=> '1',
				'carded'		=> 'NULL',
				'carding'		=> 'NULL',
				'cardable'		=> 'NULL',
				'mailable'		=> 'subscriptions.subscription_email',
				'search'		=> 'NULL',
				'firstname'		=> 'users.user_firstname',
				'surname'		=> 'users.user_surname',
				'nickname'		=> 'users.user_nickname',
				'enrol_year'	=> 'users.user_enrolled_year',
			);
			try {
				$filter = $this->_GetFilter(4);
				$sql = $this->_GenerateFilterSql($filter, $field_translator);
			} catch (Exception $e) {
				$this->messages->AddMessage('error','The filter is invalid: '.$e->getMessage());
			}
		} elseif (is_numeric($Suboption1)) {
			$sql[0] = 'business_cards.business_card_id=?';
			$sql[1] = array($Suboption1);
			if ($Suboption2 === 'edit') {
				$mode = 'edit';
			}
		}
		$business_cards = $this->members_model->GetBusinessCards(
				VipOrganisationId(),
				$sql[0], $sql[1]);
		
		// DISPLAY BUSINESS CARDS ----------------------------------- //
		if ($mode === 'view') {
			$this->pages_model->SetPageCode('viparea_members_cards');
			
			$data = array(
				'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
				'business_cards' => $business_cards,
			);
			
			// Set up the content
			$this->main_frame->SetContentSimple('members/members_cards', $data);
			
			// Set the title parameters
			$this->main_frame->SetTitleParameters(array(
				'organisation'	=> $this->user_auth->organisationName,
			));
			
		} elseif ($mode === 'edit') {
			if (!count($business_cards)) {
				$this->messages->AddMessage('error','Business card '.$Suboption1.' could not be found');
				redirect(vip_url('members/cards'));
			}
			$this->pages_model->SetPageCode('viparea_members_card_edit');
			
			$this->load->model('directory_model');
		
			// translate into nice names for view
			$data = array(
				'business_card' => $business_cards[0],
				'business_card_goups' => array(),
			);
			
			// Business Card Groups
			$groups = $this->directory_model->GetDirectoryOrganisationCardGroups(VipOrganisation());
			foreach ($groups as $group) {
				$data['business_card_goups'][] = array(
					'name' => $group['business_card_group_name'],
					'id' => $group['business_card_group_id'],
					'href' => vip_url('members/cards/filter/cardgroup/'.$group['business_card_group_id'])
				);
			}
			
			// Set the title parameters
			$this->main_frame->SetTitleParameters(array(
				'organisation'	=> $this->user_auth->organisationName,
				'name'			=> $business_cards[0]['name'],
			));
			$this->main_frame->SetContentSimple('directory/viparea_directory_contacts', $data);
		}
		
		// Load the main frame
		$this->main_frame->Load();
	}
	
	/// Invite new members to join.
	/**
	 */
	function invite()
	{
		if (!CheckPermissions('vip+pr')) return;
		
		$default_list = '';
		
		$this->_GetTeams();
		
		// Read the post data
		$button = $this->input->post('members_invite_button');
		if ($button === 'Invite Members') {
			$emails = $this->input->post('invite_list');
			if (FALSE !== $emails) {
				// Validate the emails
				$email_list = explode("\n", $emails);
				$valids = array();
				$failures = array();
				foreach ($email_list as $key => $email) {
					if (!empty($email)) {
						if (preg_match('/^([a-zA-Z0-9]{3,8})(@york\.ac\.uk)?$/', $email, $matches)) {
							$valids[] = strtolower($matches[1]);
						} else {
							$failures[] = $email;
						}
					}
				}
				if (!empty($failures)) {
					// There were failures!
					$this->messages->AddMessage('error', 'The following lines don\'t look like valid york email addresses:<br />'.implode('<br />',$failures));
					$default_list = $emails;
					
				} elseif (empty($valids)) {
					// There weren't any valids.
					$this->messages->AddMessage('information', 'You didn\'t specify any email addresses.');
					
				} else {
					// Everything was fine.
					$default_list = $this->_InviteUsers(
						VipOrganisationId(), $valids,
						'username', $this->user_auth->organisationName
					);
					$default_list = implode("\n",$default_list);
				}
			}
		}
		
		$this->pages_model->SetPageCode('viparea_members_invite');
		
		$data = array(
			'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
			'what_to_do' => $this->pages_model->GetPropertyWikitext('what_to_do'),
			'target' => vip_url('members/invite'),
			'organisation' => $this->mOrganisation,
			'default_list' => $default_list,
		);
		$this->main_frame->SetContentSimple('members/members_invite', $data);
	
		// Set the title parameters
		$this->main_frame->SetTitleParameters(array(
			'organisation'	=> $this->user_auth->organisationName,
		));
		
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
	
	/// General invite users using members_model->@a $Method.
	/**
	 * @param $OrganisationId entity_id of Organisation/team.
	 * @param $Users array of user specifiers.
	 * @param $Method string Method of identifying users
	 *	- 'username'
	 *	- 'id
	 * @param $OrganisationName string Name of organisation.
	 * @return array Remaining users.
	 */
	protected function _InviteUsers($OrganisationId, $Users, $Method, $OrganisationName)
	{
		$this->members_model->InviteUsers($OrganisationId, $Users, $Method);
		
		$invites = $this->members_model->GetUsersStatuses($OrganisationId, $Users, $Method);
		
		$invite_invalids = array_flip($Users); // not in $invites
		$invite_valids = array(); // not member, not deleted
		$invite_members = array(); // is member
		$invite_deleted = array(); // is deleted
		foreach ($invites as $invite) {
			if ($invite['deleted'] == 1) {
				$invite_deleted[] = $invite['username'];
			} elseif ($invite['member'] == 1) {
				$invite_member[] = $invite['username'];
			} else {
				$invite_valids[] = $invite['username'];
			}
			unset($invite_invalids[$invite[$Method]]);
		}
		$invite_invalids = array_keys($invite_invalids);
		
		if (!empty($invite_valids)) {
			$this->messages->AddMessage('success',
				'The following '.count($invite_valids).' users '.
				'are now invited to join '.$OrganisationName.':'.
				'<ul><li>' . implode('</li><li>', $invite_valids) . '</li></ul>');
		}
		if (!empty($invite_member)) {
			$this->messages->AddMessage('information',
				'The following '.count($invite_member).' users '.
				'are already members of '.$OrganisationName.':'.
				'<ul><li>' . implode('</li><li>', $invite_member) . '</li></ul>');
		}
		if (!empty($invite_invalids)) {
			$this->messages->AddMessage('warning',
				'The following '.count($invite_invalids).' users '.
				'could not be found and may not be registered with The Yorker:'.
				'<ul><li>' . implode('</li><li>', $invite_invalids) . '</li></ul>');
		}
		if (!empty($invite_deleted)) {
			$this->messages->AddMessage('warning',
				'The following '.count($invite_deleted).' users '.
				'are banned and need unbanning before they can be invited to '.$OrganisationName.':'.
				'<ul><li>' . implode('</li><li>', $invite_deleted) . '</li></ul>');
		}
		return ($invite_deleted + $invite_invalids);
	}
	
	/// Reindex the sorts in a filter.
	/**
	 * @param $Filter array Filter produced by _GetFilter.
	 * @return array($fields => {TRUE (asc), FALSE (desc)}) sort fields.
	 */
	protected function _ReindexFilterSorts($Filter)
	{
		$result = array();
		if (array_key_exists('sort', $Filter)) {
			foreach ($Filter['sort'] as $sort) {
				$result[$sort[1]] = ($sort[0] === 'asc');
			}
		}
		return $result;
	}
	
	/// Produce descpriptors of the filter.
	/**
	 * @param $Filter array Filter produced by _GetFilter.
	 * @return array(descriptors) where descriptors contain:
	 *	- 'description' (textural description)
	 *	- 'link_remove' (link to append to negate the filter)
	 *	- 'link_invert' (link to append to invert the filter)
	 */
	protected function _DescribeFilters($Filter)
	{
		$result = array();
		
		$sortable = FALSE;
		foreach ($Filter as $filt => $er) {
			if ($filt === '_data') {
				continue;
			}
			if (is_bool($er)) {
				if ($filt !== 'search') {
					$single_result = array();
					if (!$er) {
						$single_result['description'] = 'member is not '.$filt;
						$single_result['link_invert'] = $filt;
					} else {
						$single_result['description'] = 'member is '.$filt;
						$single_result['link_invert'] = 'not/'.$filt;
					}
					$single_result['link_remove'] = 'scrap/'.$filt;
					$result[] = $single_result;
				} else {
					//$result[] = 'search';
				}
			} elseif ($filt !== 'sort') {
				foreach ($er[TRUE] as $id => $dummy) {
					$single_result = array();
					$single_result['description'] = 'include '.$filt.' '.$id;
					$single_result['link_invert'] = 'not/'.$filt.'/'.$id;
					$single_result['link_remove'] = 'scrap/'.$filt.'/'.$id;
					$result[] = $single_result;
				}
				foreach ($er[FALSE] as $id => $dummy) {
					$single_result = array();
					$single_result['description'] = 'exclude '.$filt.' '.$id;
					$single_result['link_invert'] = $filt.'/'.$id;
					$single_result['link_remove'] = 'scrap/'.$filt.'/'.$id;
					$result[] = $single_result;
				}
			} else {
				$sortable = TRUE;
			}
		}
		if ($sortable) {
			static $invert_sort = array(
				'asc' => 'desc',
				'desc' => 'asc',
			);
			foreach ($Filter['sort'] as $sorter) {
				$single_result = array();
				$single_result['description'] = 'sorted by '.$sorter[1].' ('.$sorter[0].')';
				$single_result['link_invert'] = 'sort/'.$invert_sort[$sorter[0]].'/'.$sorter[1];
				$single_result['link_remove'] = 'scrap/sort/asc/'.$sorter[1];
				$result[] = $single_result;
			}
		}
		
		return $result;
	}
	
	/// Generate SQL from the filter produced by _GetFilter.
	/**
	 * @param $Filter array Filter produced by _GetFilter.
	 * @param $Conditions array SQL condition strings.
	 * @return array(sql, bind_data).
	 */
	protected function _GenerateFilterSql($Filter, $field_translator, $Conditions = array())
	{
		$sortable = FALSE;
		$post_search = NULL;
		$bind_data = array();
		foreach ($Filter as $filt => $er) {
			if ($filt === '_data') {
				continue;
			}
			if (is_bool($er)) {
				if ($filt !== 'search') {
					$Conditions[] = '('.$field_translator[$filt].'='.($er?'1':'0').')';
				} else {
					$post_search = $er;
				}
			} elseif ($filt !== 'sort') {
				$disjuncts = array();
				foreach ($er[TRUE] as $id => $dummy) {
					$disjuncts[] = '('.$field_translator[$filt].'=?)';
					$bind_data[] = $id;
				}
				if (!empty($disjuncts)) {
					$Conditions[] = '('.implode(' OR ', $disjuncts).')';
				}
				foreach ($er[FALSE] as $id => $dummy) {
					$Conditions[] = '('.$field_translator[$filt].'!=?)';
					$bind_data[] = $id;
				}
			} else {
				$sortable = TRUE;
			}
		}
		// Get post search
		if (is_bool($post_search)) {
			// Do search expression from POST data.
		}
		if (!empty($Conditions)) {
			$sql = '('.implode(' AND ', $Conditions).')';
		} else {
			$sql = 'TRUE';
		}
		if ($sortable) {
			$sorts = array();
			foreach (array_reverse($Filter['sort']) as $sorter) {
				$sorts[] = $field_translator[$sorter[1]].' '.$sorter[0];
			}
			$sql .= ' ORDER BY '.implode(', ',$sorts);
		}
		return array($sql, $bind_data);
	}
	
	/// Reconstruct the uri filter from the filter object.
	protected function _ReconstructFilter($Filter)
	{
		$result = array();
		
		$sortable = FALSE;
		foreach ($Filter as $filt => $er) {
			if ($filt === '_data') {
				continue;
			}
			if (is_bool($er)) {
				if ($filt !== 'search') {
					if (!$er) {
						$result[] = 'not';
					}
					$result[] = $filt;
				} else {
					//$result[] = 'search';
				}
			} elseif ($filt !== 'sort') {
				foreach ($er[TRUE] as $id => $dummy) {
					$result[] = $filt;
					$result[] = $id;
				}
				foreach ($er[FALSE] as $id => $dummy) {
					$result[] = 'not';
					$result[] = $filt;
					$result[] = $id;
				}
			} else {
				$sortable = TRUE;
			}
		}
		if ($sortable) {
			foreach ($Filter['sort'] as $sorter) {
				$result[] = 'sort';
				$result[] = $sorter[0];
				$result[] = $sorter[1];
			}
		}
		
		return $result;
	}
	
	/// Get member filter from url.
	protected function _GetFilter($StartRSegment, $OverrideSegments = NULL, $PreFilter = NULL)
	{
		$default = array(
				TRUE  => array(),
				FALSE => array(),
			);
		if (NULL === $PreFilter) {
			$filter = array(
				'team' => $default,
				'user' => $default,
				'card' => $default,
				'_data' => array(
					'last_sort' => '',
				),
			);
		} else {
			$filter = $PreFilter;
		}
		// Start at RSegment and read expressions
		$segment_number = $StartRSegment;
		while ($segment_number <= $this->uri->total_rsegments()) {
			// Detect whether this filter item is notted
			$segment = $this->uri->rsegment($segment_number++);
			$include = TRUE;
			$remove_condition = FALSE;
			if ($segment === 'not') {
				$include = FALSE;
				if ($segment_number > $this->uri->total_rsegments()) {
					throw new Exception('Unexpected end of filter URI segments near \'not\'.');
				}
				$segment = $this->uri->rsegment($segment_number++);
				
			} elseif ($segment === 'scrap') {
				$remove_condition = TRUE;
				if ($segment_number > $this->uri->total_rsegments()) {
					throw new Exception('Unexpected end of filter URI segments near \'scrap\'.');
				}
				$segment = $this->uri->rsegment($segment_number++);
			}
			
			// Get the field to filter
			static $validator_bools = array(
				'paid'     => 'paid',
				'vip'      => 'vip',
				'confirmed' => 'confirmed',
				'carded'   => 'carded',
				'carding'  => 'carding',
				'cardable' => 'cardable',
				'mailable' => 'mailable',
				'search'   => 'search',
			);
			static $validator_1 = array(
				'team' => 'is_string',
				'user' => 'is_numeric',
				'card' => 'is_numeric',
			);
			static $sort_directions = array(
				'asc'  => 'asc',
				'desc' => 'desc',
			);
			static $sortable_fields = array(
				'firstname'  => 'firstname',
				'surname'    => 'surname',
				'nickname'   => 'nickname',
				'enrol_year' => 'enrol_year',
				'paid'   => 'paid',
				'vip'    => 'vip',
				'mailable' => 'mailable',
				'confirmed' => 'confirmed',
				'carded' => 'carded',
			);
			$validator_2 = array(
				'sort' => array($sort_directions, $sortable_fields),
			);
			
			if (array_key_exists($segment, $validator_bools)) {
				if ($remove_condition) {
					unset($filter[$validator_bools[$segment]]);
				} else {
					$filter[$validator_bools[$segment]] = $include;
				}
				
			} elseif (array_key_exists($segment, $validator_1)) {
				// Get the user id
				if ($segment_number > $this->uri->total_rsegments()) {
					throw new Exception('Unexpected end of filter URI segments near \''.$segment.'\'.');
				}
				$id = $this->uri->rsegment($segment_number++);
				if (!$validator_1[$segment]($id)) {
					throw new Exception('Unexpected non '.$validator_1[$segment].' filter URI segment after \''.$segment.'\'.');
				}
				// Override any previous contradicting entries
				if (array_key_exists($id, $filter[$segment][!$include])) {
					unset($filter[$segment][!$include][$id]);
				}
				if ($remove_condition) {
					// Remove any traces of this object in filter
					unset($filter[$segment][$include][$id]);
				} else {
					// Add new filter
					$filter[$segment][$include][$id] = $include;
				}
				
			} elseif (array_key_exists($segment, $validator_2)) {
				if (!array_key_exists($segment, $filter)) {
					$filter[$segment] = array();
				}
				$parameters = array();
				foreach ($validator_2[$segment] as $validator) {
					if ($segment_number > $this->uri->total_rsegments()) {
						throw new Exception('Unexpected end of filter URI segments near \''.$segment.'\'.');
					}
					$parameter = $this->uri->rsegment($segment_number++);
					if (is_string($validator) && !$validator($parameter)) {
						throw new Exception('Unexpected non '.$validator.' filter URI segment after \''.$segment.'\'.');
					}
					if (is_array($validator)) {
						if (!array_key_exists($parameter, $validator)) {
							throw new Exception('Unexpected non {'.implode(', ',$validator).'} filter URI segment after \''.$segment.'\'.');
						} else {
							$parameter = $validator[$parameter];
						}
					}
					$parameters[] = $parameter;
				}
				
				if (array_key_exists($parameters[1], $filter[$segment])) {
					unset($filter[$segment][$parameters[1]]);
				}
				if ($remove_condition) {
					// Cleanup after old sort instead of recreating
					if (empty($filter[$segment])) {
						unset($filter[$segment]);
					}
					$filter['_data']['last_sort'] = '';
					
				} else {
					// [Re]create [old] sort field
					$filter[$segment][$parameters[1]] = $parameters;
					$filter['_data']['last_sort'] = $parameters[1];
				}
				
			} else {
				throw new Exception('Unexpected filter URI segment: \''.$segment.'\'.');
			}
		}
		return $filter;
	}
}

?>