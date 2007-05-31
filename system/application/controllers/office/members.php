<?php

/**
 * @file controllers/office/members.php
 * @brief Viparea members controller.
 *
 * @see http://real.theyorker.co.uk/wiki/Functional:Member_Manager
 *
 * @version 20/03/2007 James Hogan (jh559)
 *	- Added tabs
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

	protected $mMembers;
	protected $mIsFilterOn;
	protected $mLastSort;
	protected $mSortFields;
	protected $mFilterDescriptors;
	protected $mFilterBase;

	/// Default constructor.
	function __construct()
	{
		parent::Controller();
		$this->load->model('directory_model');
		$this->load->model('businesscards_model');
		$this->load->model('organisation_model');
		$this->load->model('members_model');
		$this->load->library('organisations');

		$this->mAllTeams = NULL;
		$this->mOrganisation = NULL;
	}

	/// Set up the tabs on the main_frame.
	/**
	 * @param $SelectedPage string Selected Page.
	 * @pre CheckPermissions must have already been called.
	 */
	protected function _SetupTabs($SelectedPage)
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('members', 'Members',
				vip_url('members'));
		$navbar->AddItem('invite', 'New Members',
				vip_url('members/invite'));
		$navbar->AddItem('teams', 'Teams',
				vip_url('members/teams'));
		$navbar->AddItem('compose', 'Compose',
				vip_url('members/compose'));

		$this->main_frame->SetPage($SelectedPage);
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
		if (!CheckPermissions('vip')) return;

		/// @note Redirects to members/list
		redirect(vip_url('members/list'));
	}

	protected function _handle_member_list_member_operation()
	{
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
	}

	/// @return bool Whether to quit
	protected function _handle_member_list($TopTeam, $FilterSegment)
	{
		$this->mLastSort = '';
		$this->mSortFields = array();
		$this->mFilterDescriptors = array();

		$this->mIsFilterOn = FALSE;

		if (!isset($memberships)) {
			$team_list = $this->organisation_model->GetSubteamIds($this->mOrganisation);
			$memberships = $this->members_model->GetMemberDetails($team_list, null, 'TRUE', array(), ('manage' === VipMode()));
		}

		$team_list = array_flip($team_list);

		// Merge duplicated members and produce list of teams they're subscribed to
		$members = array();
		foreach ($memberships as $membership) {
			if (!array_key_exists((int)$membership['user_id'], $members)) {
				$members[(int)$membership['user_id']] = array();
			}
			$members[(int)$membership['user_id']][(int)$membership['team_id']] = $membership;
		}

		foreach ($members as $user_id => $member_teams) {
			$team_subscriptions = array();
			$found = FALSE;
			foreach ($member_teams as $team_id => $member_team) {
				if (!$found && (!isset($team_list) || array_key_exists($team_id, $team_list))) {
					$found = TRUE;
				}
				if ($team_id !== VipOrganisationId()) {
					$team_subscriptions[$team_id] = $member_team;
					$team_subscriptions[$team_id]['team'] = &$this->mAllTeams[$team_id];
				}
			}
			if ($found) {
				if (array_key_exists($TopTeam, $member_teams)) {
					$members[$user_id] = $member_teams[$TopTeam];
				} else {
					$members[$user_id] = end($member_teams);
					unset($members[$user_id]['team_id']);
					unset($members[$user_id]['paid']);
					unset($members[$user_id]['on_mailing_list']);
					unset($members[$user_id]['vip']);
					unset($members[$user_id]['confirmed']);
				}
				$members[$user_id]['teams'] = $team_subscriptions;
			} else {
				unset($members[$user_id]);
			}
		}

		$this->mMembers = &$members;
	}

	/// List of members and member set operations.
	/**
	 * @note This is accessed with list not memberlist (list is reserved word).
	 */
	function memberlist()
	{
		if (!CheckPermissions('vip')) return;
		/// @todo Implement $viparea/members/list/...

		$this->_SetupTabs('members');

		$this->_GetTeams();

		$this->_handle_member_list_member_operation();

		$this->mFilterBase = 'members/list';
		if ($this->_handle_member_list(VipOrganisationId(),3)) return;

		$this->pages_model->SetPageCode('viparea_members_list');
		$data = array(
			'main_text'    => $this->pages_model->GetPropertyWikitext('main_text'),
			'target'       => $this->uri->uri_string(),
			'members'      => $this->mMembers,
			'organisation' => $this->mOrganisation,
			'filter'       => array(
				'enabled'      => $this->mIsFilterOn,
				'last_sort'    => $this->mLastSort,
				'base'         => $this->mFilterBase,
				'descriptors'  => $this->mFilterDescriptors,
			),
			'sort_fields'  => $this->mSortFields,
			'in_team'      => FALSE,
		);
		// Set up the content
		$this->main_frame->SetContentSimple('members/members', $data);

		// Set the title parameters
		$this->main_frame->SetTitleParameters(array(
			'organisation'	=> VipOrganisationName(),
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
		if (!CheckPermissions('vip')) return;

		$this->_SetupTabs('members');

		// If no entity id was provided, redirect back to members list.
		if (NULL === $EntityId) {
			return redirect(vip_url('members/list'));
		}


		// Read the post data for changing office access (MANAGE ONLY)
		if ('manage' === VipMode()) {
			$access_level	= $this->input->post('office_access_level');
			if (FALSE !== $access_level) {
				$access_password			= $this->input->post('password');
				$access_password_confirm	= $this->input->post('confirm_password');

				if ($access_level == 'editor') {
					if ($access_password!=$access_password_confirm) {
						$this->messages->AddMessage('error','Passwords do not match, please confirm your password.');
					} elseif (strlen($access_password) == 0) {
						$this->messages->AddMessage('information','You must assign editors a password.');
					} elseif (strlen($access_password) < 4) {
						$this->messages->AddMessage('error','Office password must be more than 3 characters in length.');
					} else {
						$success_rows = $this->members_model->UpdateAccessLevel('1', null, $EntityId);
						$this->user_auth->setOfficePassword($access_password,  $EntityId);

						$user = $this->members_model->GetUsername($EntityId);

						$to = $user->entity_username.$this->config->Item('username_email_postfix');
						$from = $this->pages_model->GetPropertyText('system_email', true);
						$subject = $this->pages_model->GetPropertyText('office_password_email_subject', true);
						$message = str_replace('%%password%%',$access_password,str_replace('%%nickname%%',$user->nickname,$this->pages_model->GetPropertyText('office_password_email_body', true)));
						if ($to && $subject && $message && $from){
							$from = 'From: '.$from."\r\n".'Reply-To:'.$from."\r\n";

							$this->load->helper('yorkermail');
							try {
								yorkermail($to,$subject,$message,$from);
								$this->main_frame->AddMessage('success',
									'The e-mail containing the password was sent successfully.' );
							} catch (Exception $e) {
								$this->main_frame->AddMessage('error',
									'E-mail Sending Failed: '.$e->getMessage() );
							}
						} else {
							$this->messages->AddMessage('error','E-mail Sending Failed.');
						}
					}
				} elseif ($EntityId == $this->user_auth->entityId) {
					// Ensure that the privilages user isn't trying to demote themselves.
					$this->messages->AddMessage('error', 'You cannot reduce your own access privilages. You must ask another editor to do so for you.');
				} elseif ($access_level == 'writer') {
					$success_rows = $this->members_model->UpdateAccessLevel('1', null, $EntityId);
					if ($success_rows > 0) {
						$this->messages->AddMessage('success','User has been set to "Writer".');
					} else {
						$this->messages->AddMessage('error','Operation Failed. User has not been set to "Writer".');
					}
				} elseif ($access_level == 'none') {
					$success_rows = $this->members_model->UpdateAccessLevel('0', null, $EntityId);
					if ($success_rows > 0) {
						$this->messages->AddMessage('success','User has been set to "No Access".');
					} else {
						$this->messages->AddMessage('error','Operation Failed. User has not been set to "No Access".');
					}
				}
			}
		}

		// Get membership information for the first time
		// This will determine whether the entity is a member.
		$membership = $this->members_model->GetMemberDetails(VipOrganisationId(), $EntityId, 'TRUE', array(), ('manage' === VipMode()));

		if (!empty($membership)) {
			$membership = $membership[0];

			// Read the post data
			$button = $this->input->post('member_byline_reset');
			if ($button === 'Set Default Byline') {
				if ( $this->members_model->SetDefaultByline($EntityId, VipOrganisationId()) ) {
					$this->messages->AddMessage('success','Byline was added successfully.');
				} else {
					$this->messages->AddMessage('error','Byline could not be added, a byline might already exist.');
				}
				return redirect(vip_url('members/info/'.$EntityId));
			}

			// Read the post data
			$button = $this->input->post('member_cmd');
			if ($button === 'Remove') {
				if ( $this->members_model->RemoveSubscription($EntityId, VipOrganisationId()) ) {
					$this->messages->AddMessage('success','Member removed successfully.');
					return redirect(vip_url('members/list'));
				} else {
					$this->messages->AddMessage('error','No changes were made to the membership.');
					return redirect(vip_url('members/info/'.$EntityId));
				}
			} elseif ($button === 'Invite') {
				if ( $this->members_model->InviteMember($EntityId, VipOrganisationId()) ) {
					$this->messages->AddMessage('success','User invited successfully.');
				} else {
					$this->messages->AddMessage('error','No changes were made to the membership.');
				}
				return redirect(vip_url('members/info/'.$EntityId));
			} elseif ($button === 'Withdraw Invite') {
				if ( $this->members_model->WithdrawInvite($EntityId, VipOrganisationId()) ) {
					$this->messages->AddMessage('success','Invite withdrawn successfully.');
					return redirect(vip_url('members/list'));
				} else {
					$this->messages->AddMessage('error','No changes were made to the membership.');
					return redirect(vip_url('members/info/'.$EntityId));
				}
			} elseif ($button === 'Accept') {
				if ( $this->members_model->ConfirmMember($EntityId, VipOrganisationId()) ) {
					$this->messages->AddMessage('success','Member accepted successfully.');
				} else {
					$this->messages->AddMessage('error','No changes were made to the membership.');
				}
				return redirect(vip_url('members/info/'.$EntityId));
			}

			// Read the post data
			$button = $this->input->post('vip_cmd');
			if ($button === 'Demote' || $button === 'Reject') {
				if ( $this->members_model->UpdateVipStatus('none', $EntityId, VipOrganisationId()) ) {
					$this->messages->AddMessage('success','Member demoted successfully.');
				} else {
					$this->messages->AddMessage('error','No changes were made to the membership.');
				}
				return redirect(vip_url('members/info/'.$EntityId));
			} elseif ($button === 'Promote' || $button === 'Accept') {
				if ( $this->members_model->UpdateVipStatus('approved', $EntityId, VipOrganisationId()) ) {
					$this->messages->AddMessage('success','Member promoted successfully.');
				} else {
					$this->messages->AddMessage('error','No changes were made to the membership.');
				}
				return redirect(vip_url('members/info/'.$EntityId));
			}

			// Read the post data
			$button = $this->input->post('member_update');
			if ($button === 'Update') {
				$member_paid	= (FALSE !== $this->input->post('member_paid'));

				$changes = array();
				if ($member_paid !== (bool)$membership['paid']) {
					// Paid has changed
					$membership['paid'] = $changes['paid'] = $member_paid;
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
			if (!$membership['user_confirmed'] && !$membership['org_confirmed']) {
				$membership['status'] = 'Non-member';
				$membership['cmd_string'] = 'This user is <b>not a member</b> of your organisation, click below to invite them.';
				$membership['cmd_action'] = 'Invite';
				$membership['cmd_js'] = '';
			} elseif (!$membership['user_confirmed'] && $membership['org_confirmed']) {
				$membership['status'] = 'Invited';
				$membership['cmd_string'] = 'You have <b>invited</b> this user to join your organisation, but they have not yet replied, click below to withdraw your invitation.';
				$membership['cmd_action'] = 'Withdraw Invite';
				$membership['cmd_js'] = "return confirm('Are you sure that you want to withdraw the invite for this user?');";
			} elseif ($membership['user_confirmed'] && !$membership['org_confirmed']) {
				$membership['status'] = 'Requested to join';
				$membership['cmd_string'] = 'This user has <b>requested</b> to become a member of your organisation, click below to accept their request.';
				$membership['cmd_action'] = 'Accept';
				$membership['cmd_js'] = '';
			} else {
				$membership['status'] = 'Member';
				$membership['cmd_string'] = 'This user is a <b>member</b> of your organisation, click below to remove them.';
				$membership['cmd_action'] = 'Remove';
				$membership['cmd_js'] = "return confirm('Are you sure that you want to remove this member from your organisation?');";
			}



			if ('manage' === VipMode() && (!$membership['has_byline'] || $membership['byline_needs_approval'] || $membership['byline_expired']) ) {
				$membership['byline_reset'] = true;
			}

			$data = array(
				'main_text'    => $this->pages_model->GetPropertyWikitext('main_text'),
				'membership'   => $membership,
			);
			// Set up the content
			$this->main_frame->SetContentSimple('members/editmembers', $data);

			// Set the title parameters
			$this->main_frame->SetTitleParameters(array(
				'organisation'	=> VipOrganisationName(),
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
		if (!CheckPermissions('vip')) return;

		$this->_SetupTabs('teams');

		$this->_GetTeams();

		$team_id = VipOrganisationId();

		$in_team = FALSE;

		if (NULL !== $Suboption1) {
			if (is_numeric($Suboption1) && array_key_exists((int)$Suboption1,$this->mAllTeams)) {
				$team_id = (int)$Suboption1;
				$in_team = TRUE;
				$this->mOrganisation = &$this->mAllTeams[$team_id];
			} else {
				// Show custom error page for no existing team
				$this->load->library('custom_pages');
				$this->main_frame->SetContent(new CustomPageView('vip_members_notteam','error'));
				$this->main_frame->Load();
				return;
			}
		}

		$this->_handle_member_list_member_operation();

		$this->mFilterBase = 'members/teams/'.$team_id.'/members';
		if ($this->_handle_member_list($team_id, 5)) return;

		$this->pages_model->SetPageCode('viparea_members_teams');

		$data = array(
			'main_text'    => $this->pages_model->GetPropertyWikitext('main_text'),
			'target'       => $this->uri->uri_string(),
			'members'      => $this->mMembers,
			'organisation' => $this->mOrganisation,
			'filter'       => array(
				'enabled'      => $this->mIsFilterOn,
				'last_sort'    => $this->mLastSort,
				'base'         => $this->mFilterBase,
				'descriptors'  => $this->mFilterDescriptors,
			),
			'sort_fields'  => $this->mSortFields,
			'in_team'      => $in_team,
		);

		$this->load->model('notices_model');
		$notices = $this->notices_model->GetPublicNoticesForOrganisation($team_id, NULL, FALSE);
		//$this->messages->AddDumpMessage('notices',$notices);

		$this->main_frame->SetContentSimple('members/teams', $data);

		// Set the title parameters
		$this->main_frame->SetTitleParameters(array(
			'organisation'	=> VipOrganisationName(),
			'team'	=> $this->mOrganisation['name'],
		));

		// Load the main frame
		$this->main_frame->Load();
	}

	/// Tags that can be appended to invite text emails
	protected static $sInviteFlags = array(
		'p' => 'paid',
	);

	/// Invite new members to join.
	/**
	 */
	function invite($Stage = NULL)
	{
		if (!CheckPermissions('vip')) return;

		if (!is_numeric($Stage) || $Stage < 1 || $Stage > 4) {
			redirect(vip_url('members/invite/1'));
			return;
		}

		$this->load->helper('string');

		$this->_SetupTabs('invite');

		$default_list = '';

		$this->_GetTeams();

		// Read the post data
		/// @todo require comma or newline between items
		if ($this->input->post('members_invite_button') === 'Continue') {
			$emails = $this->input->post('invite_list');
			if (FALSE !== $emails) {
				// Validate the emails
				$preprocessed_emails = preg_replace('/[^a-zA-Z0-9@\.]+/',' ',$emails);
				$word_list = explode(" ", $preprocessed_emails);
				$valids = array();
				$failures = array();
				$last_email = NULL;
				foreach ($word_list as $word) {
					if (!empty($word)) {
						if (preg_match('/^([a-zA-Z]{2,5}\d{2,4})(@york\.ac\.uk)?$/', $word, $matches)) {
							$last_email = strtolower($matches[1]);
							$valids[$last_email] = array();
						} else {
							$all_flags = ($last_email !== NULL);
							if ($all_flags) {
								foreach (str_split($word) as $flag) {
									if (array_key_exists($flag, self::$sInviteFlags)) {
										$valids[$last_email][self::$sInviteFlags[$flag]] = TRUE;
									} else {
										$all_flags = FALSE;
										break;
									}
								}
							}
							if (!$all_flags) {
								$failures[] = $word;
							}
						}
					}
				}
				if (!empty($failures)) {
					// There were failures!
					$this->messages->AddMessage('error', 'The following words don\'t look like valid york email addresses:<br />'.implode('<br />',$failures));
					$default_list = $emails;

				} elseif (empty($valids)) {
					// There weren't any valids.
					$this->messages->AddMessage('information', 'You didn\'t specify any email addresses.');

				} else {
					// Everything was fine.
					/// @todo display list of invites before inviting.
					$this->messages->AddDumpMessage('valids',$valids);
				}
			}
		} else if ($this->input->post('confirm_invite_button') === 'Confirm Invites') {
			$default_list = $this->_InviteUsers(
				VipOrganisationId(), $valids,
				'username', VipOrganisationName()
			);
			$default_list = implode("\n",$default_list);
		}

		$this->pages_model->SetPageCode('viparea_members_invite');

		$data = array(
			'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
			'what_to_do' => $this->pages_model->GetPropertyWikitext('what_to_do'),
			'target' => vip_url('members/invite'),
			'organisation' => $this->mOrganisation,
			'default_list' => $default_list,
			'step' => 1,
			'State' => $Stage,
		);
		$this->main_frame->SetContentSimple('members/invite', $data);

		// Set the title parameters
		$this->main_frame->SetTitleParameters(array(
			'organisation'	=> VipOrganisationName(),
		));

		// Load the main frame
		$this->main_frame->Load();
	}

	/// Contact members
	/**
	 */
	function compose()
	{
		if (!CheckPermissions('vip')) return;

		$this->pages_model->SetPageCode('viparea_members_compose');
		$this->_SetupTabs('compose');

		/// @todo Implement $viparea/members/compose/...
		$this->messages->AddMessage('information', 'todo: implement email composer');

		$data = array();
		$this->main_frame->SetContentSimple('members/compose', $data);

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
}

?>