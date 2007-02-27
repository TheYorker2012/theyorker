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
	/// associative array All teams indexed by entity id
	protected $mAllTeams;
	
	/// teams in a tree structure including main organisations
	protected $mOrganisation;
	
	/// Default constructor.
	function __construct()
	{
		parent::Controller();
		$this->load->model('directory_model');
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
		// Get teams array from database
		if (NULL === $Depth) {
			$teams_list = $this->members_model->GetTeams(VipOrganisationId());
		} else {
			$teams_list = $this->members_model->GetTeams(VipOrganisationId(), $Depth);
		}
		
		// Reindex teams by entity id
		$this->mAllTeams = array(
			VipOrganisationId() => array(
				'id' 		=> VipOrganisationId(),
				'parent_id'	=> -1,
				'name'		=> $this->user_auth->organisationName,
				'subteams'	=> array(),
			)
		);
		foreach ($teams_list as $team) {
			$team['subteams'] = array();
			$this->mAllTeams[$team['id']] = $team;
		}
		
		// Set up team tree using references
		foreach ($this->mAllTeams as $id => $team) {
			if ($id != VipOrganisationId()) {
				$parent = $team['parent_id'];
				assert('array_key_exists($parent, $this->mAllTeams)');
				$this->mAllTeams[$parent]['subteams'][] = &$this->mAllTeams[$id];
			}
		}
		$this->mOrganisation = &$this->mAllTeams[VipOrganisationId()];
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
				$this->messages->AddDumpMessage('userids',$selected_members);
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
		
		$filter_base = 'members/list/filter';
		$sort_fields = array();
		
		if ($Filter === 'filter') {
			static $field_translator = array(
				'team'			=> 'NULL',
				'user'			=> 'users.user_entity_id',
				'card'			=> 'NULL',
				'paid' 			=> 'subscriptions.subscription_paid',
				'vip'			=> 'subscriptions.subscription_vip',
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
				$filter = $this->_GetFilter(4);
				// Produce sql, the base url for extra filters, and sort fields
				$filter_base .= '/'.implode('/', $this->_ReconstructFilter($filter));
				/*if (vip_url($filter_base) !== $this->uri->uri_string()) {
					// If the generated url is different, redirect to it as it is neater
					return redirect(vip_url($filter_base));
				}*/
				$sql = $this->_GenerateFilterSql($filter, $field_translator);
				$sort_fields = $this->_ReindexFilterSorts($filter);
				// Use db to get members using filter.
				$members = $this->members_model->GetMemberDetails(VipOrganisationId(), NULL, $sql[0], $sql[1]);
			} catch (Exception $e) {
				$this->messages->AddMessage('error','The filter is invalid: '.$e->getMessage());
			}
		} elseif(NULL !== $Filter) {
			return show_404();
		}
		
		if (!isset($members)) {
			$members = $this->members_model->GetMemberDetails(VipOrganisationId());
		}
		
		$this->_GetTeams();
		
		$this->pages_model->SetPageCode('viparea_members_list');
		$data = array(
			'main_text'    => $this->pages_model->GetPropertyWikitext('main_text'),
			'target'       => $this->uri->uri_string(),
			'members'      => $members,
			'organisation' => $this->mOrganisation,
			'filter_base'  => $filter_base,
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
		
		if (NULL !== $Suboption1) {
			if (is_numeric($Suboption1) && array_key_exists((int)$Suboption1,$this->mAllTeams)) {
				$Suboption1 = (int)$Suboption1;
				$this->mOrganisation = &$this->mAllTeams[$Suboption1];
			} else {
				// Show custom error page for no existing team
				$this->load->library('custom_pages');
				$this->main_frame->SetContent(new CustomPageView('vip_members_notteam','error'));
				$this->main_frame->Load();
				return;
			}
		}
		
		$this->pages_model->SetPageCode('viparea_members_teams');
		
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
					$this->members_model->InviteUsers(VipOrganisationId(), $valids);
					
					$invites = $this->members_model->GetUsersStatuses(VipOrganisationId(), $valids);
					$invite_invalids = array_flip($valids); // not in $invites
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
						unset($invite_invalids[$invite['username']]);
					}
					
					$messages = array();
					if (!empty($invite_valids)) {
						$messages[] = 'The following '.count($invite_valids).' users '.
							'are now invited to join your organisation:'.
							'<ul><li>' . implode('</li><li>', $invite_valids) . '</li></ul>';
					}
					if (!empty($invite_member)) {
						$messages[] = 'The following '.count($invite_member).' users '.
							'are already members:'.
							'<ul><li>' . implode('</li><li>', $invite_member) . '</li></ul>';
					}
					if (!empty($invite_invalids)) {
						$messages[] = 'The following '.count($invite_invalids).' users '.
							'could not be found and may not be registered with The Yorker:'.
							'<ul><li>' . implode('</li><li>', array_keys($invite_invalids)) . '</li></ul>';
					}
					if (!empty($invite_deleted)) {
						$messages[] = 'The following '.count($invite_deleted).' users '.
							'are banned and need unbanning before they can be invited:'.
							'<ul><li>' . implode('</li><li>', $invite_deleted) . '</li></ul>';
					}
					$this->messages->AddMessage('information',implode('',$messages));
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
		$bind_data = array();
		foreach ($Filter as $filt => $er) {
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
		$sort_index = array();
		if (NULL === $PreFilter) {
			$filter = array(
				'team' => $default,
				'user' => $default,
				'card' => $default,
			);
		} else {
			$filter = $PreFilter;
			if (array_key_exists('sort', $filter)) {
				foreach ($filter['sort'] as $key => $sort) {
					$sort_index[$sort[1]] = $key;
				}
			}
		}
		// Start at RSegment and read expressions
		$segment_number = $StartRSegment;
		while ($segment_number <= $this->uri->total_rsegments()) {
			// Detect whether this filter item is notted
			$segment = $this->uri->rsegment($segment_number++);
			$include = TRUE;
			if ($segment === 'not') {
				$include = FALSE;
				if ($segment_number > $this->uri->total_rsegments()) {
					throw new Exception('Unexpected end of filter URI segments near \'not\'.');
				}
				$segment = $this->uri->rsegment($segment_number++);
			}
			
			// Get the field to filter
			static $validator_bools = array(
				'paid'     => 'paid',
				'vip'      => 'vip',
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
				'carded' => 'carded',
			);
			$validator_2 = array(
				'sort' => array($sort_directions, $sortable_fields),
			);
			
			if (array_key_exists($segment, $validator_bools)) {
				$filter[$validator_bools[$segment]] = $include;
				
			} elseif (array_key_exists($segment, $validator_1)) {
				// Get the user id
				if ($segment_number > $this->uri->total_rsegments()) {
					throw new Exception('Unexpected end of filter URI segments near \''.$segment.'\'.');
				}
				$id = $this->uri->rsegment($segment_number++);
				if (!$validator_1[$segment]($id)) {
					throw new Exception('Unexpected non '.$validator_1[$segment].' filter URI segment after \''.$segment.'\'.');
				}
				$filter[$segment][$include][$id] = $include;
				// Override any previous contradicting entries
				if (array_key_exists($id, $filter[$segment][!$include])) {
					unset($filter[$segment][!$include][$id]);
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
				if (array_key_exists($parameters[1], $sort_index)) {
					$filter[$segment][$sort_index[$parameters[1]]] = $parameters;
				} else {
					$filter[$segment][] = $parameters;
					$sort_index[$parameters[1]] = key($filter[$segment]);
				}
				
			} else {
				throw new Exception('Unexpected filter URI segment: \''.$segment.'\'.');
			}
		}
		return $filter;
	}
}

?>