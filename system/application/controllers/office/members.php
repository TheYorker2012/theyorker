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
		
		if ($Filter === 'filter') {
			try {
				$filter = $this->_GetFilter(4);
				$this->messages->AddDumpMessage('filter',$this->_GenerateFilterSql($filter));
			} catch (Exception $e) {
				$this->messages->AddMessage('error','The filter is invalid: '.$e->getMessage());
			}
		} elseif(NULL !== $Filter) {
			return show_404();
		}
		
		$this->pages_model->SetPageCode('viparea_members_list');
		$data = array(
			'main_text'    => $this->pages_model->GetPropertyWikitext('main_text'),
			'members'      => $this->members_model->GetMemberDetails(VipOrganisationId()),
			'team'         => $this->members_model->GetTeams($this->user_auth->organisationLogin)
		);
		// Set up the content
		$this->main_frame->SetContentSimple('viparea/members', $data);
		
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
			
			if (NULL === $Page) {
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
				$this->main_frame->SetContentSimple('viparea/editmembers', $data);
				
				// Set the title parameters
				$this->main_frame->SetTitleParameters(array(
					'organisation'	=> $this->user_auth->organisationName,
					'firstname'		=> $membership['firstname'],
					'surname'		=> $membership['surname'],
				));
			
			} elseif ($Page === 'post') {
				// SAVE MEMBERSHIP INFORMATION ------------------------------ //
				// Find the changes
				$confirm = $this->input->post('member_update'); // = 'Update'
				if ($confirm === 'Update') {
					$member_paid	= (FALSE !== $this->input->post('member_paid'));
					$member_vip		= (FALSE !== $this->input->post('member_vip'));
					
					$changes = array();
					if ($member_paid !== (bool)$membership['paid']) {
						// Paid has changed
						$changes['paid'] = $member_paid;
					}
					if ($member_vip !== (bool)$membership['vip']) {
						// Vip status has changed
						$changes['vip'] = $member_vip;
					}
					
					// If changes save them
					// If no changes don't save them
					if (empty($changes)) {
						$this->messages->AddMessage('information','No changes were made to the membership.');
					} else {
						/// @todo Save member updated to database
						$this->messages->AddMessage('success',
								'The membership\'s '.implode(', ',array_keys($changes)).' flags were successfully updated');
						$this->messages->AddMessage('error',
								'The membership\'s '.implode(', ',array_keys($changes)).' flags could not be updated');
					}
				}
				
				// Redirect
				return redirect(vip_url('members/info/'.$EntityId));
				
			} else {
				// No other pages available
				return show_404();
			}
			
		} else {
			// The entity isn't a member of the organisation
			$this->load->library('custom_pages');
			$this->main_frame->SetContent(new CustomPageView('vip_members_notmember','error'));
		}
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
		
		$this->load->helper('images');
		
		$sql = array('TRUE',array());
		if ($Suboption1 === 'filter') {
			try {
				$filter = $this->_GetFilter(4);
				$sql = $this->_GenerateFilterSql($filter);
			} catch (Exception $e) {
				$this->messages->AddMessage('error','The filter is invalid: '.$e->getMessage());
			}
		}
		$business_cards = $this->members_model->GetBusinessCards(
				VipOrganisationId(),
				$sql[0], $sql[1]);
		
		foreach ($business_cards as $key => $business_card) {
			if($business_card['image_id'] == NULL)
			{
				$image = '/images/prototype/directory/members/no_image.png';
			} else {
				$image = photoLocation($business_card['image_id']);
			}
			$business_cards[$key]['image'] = $image;
		}
		
		// DISPLAY BUSINESS CARDS ----------------------------------- //
		
		$this->pages_model->SetPageCode('viparea_members_cards');
		
		$data = array(
			'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
			'business_cards' => $business_cards,
		);
		
		// Set up the content
		$this->main_frame->SetContentSimple('viparea/members_cards', $data);
		
		// Set the title parameters
		$this->main_frame->SetTitleParameters(array(
			'organisation'	=> $this->user_auth->organisationName,
		));
		
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
	
	/// Generate SQL from the filter produced by _GetFilter.
	/**
	 * @param $Filter array Filter produced by _GetFilter.
	 * @param $Conditions array SQL condition strings.
	 * @return array(sql, bind_data).
	 */
	protected function _GenerateFilterSql($Filter, $Conditions = array())
	{
		$sortable = FALSE;
		$post_search = NULL;
		$bind_data = array();
		foreach ($Filter as $filt => $er) {
			if (is_bool($er)) {
				if ($filt !== 'search') {
					$Conditions[] = '('.$filt.'='.($er?'1':'0').')';
				} else {
					$post_search = $er;
				}
			} elseif ($filt !== 'sort') {
				$disjuncts = array();
				foreach ($er[TRUE] as $id => $dummy) {
					$disjuncts[] = '('.$filt.'=?)';
					$bind_data[] = $id;
				}
				if (!empty($disjuncts)) {
					$Conditions[] = '('.implode(' OR ', $disjuncts).')';
				}
				foreach ($er[FALSE] as $id => $dummy) {
					$Conditions[] = '('.$filt.'!=?)';
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
			foreach ($Filter['sort'] as $sorter) {
				$sorts[] = $sorter[1].' '.$sorter[0];
			}
			$sql .= ' ORDER BY '.implode(', ',$sorts);
		}
		return array($sql, $bind_data);
	}
	
	/// Get member filter from url.
	protected function _GetFilter($StartRSegment)
	{
		$default = array(
				TRUE  => array(),
				FALSE => array(),
			);
		$filter = array(
			'team' => $default,
			'user' => $default,
			'card' => $default,
		);
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
				'asc'  => 'ASC',
				'desc' => 'DESC',
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
				$filter[$segment][] = $parameters;
				
			} else {
				throw new Exception('Unexpected filter URI segment: \''.$segment.'\'.');
			}
		}
		return $filter;
	}
}

?>