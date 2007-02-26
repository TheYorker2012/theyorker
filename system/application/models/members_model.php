<?php
class Members_model extends Model {

	function Members_Model()
	{
		parent::Model();
	}
	
	/// Gets a users membership with an organisation.
	/**
	 * @param $organisation_id integer Organisation entity id.
	 * @param $user_id integer User entity id.
	 * @return array of associative arrays of membership attributes.
	 *	- Subscription must be membership
	 *	- Subscription must not be deleted
	 *	- email will only be returned if the member is on the mailing list
	 */
	function GetMemberDetails($organisation_id, $user_id = NULL, $FilterSql = 'TRUE', $BindData = array())
	{
		$bind_data = array();
		$sql = '
			SELECT
				subscriptions.subscription_paid AS paid,
				subscriptions.subscription_email AS on_mailing_list,
				subscriptions.subscription_vip AS vip,
				subscriptions.subscription_interested AS interested,
				users.user_entity_id AS user_id,
				users.user_firstname AS firstname,
				users.user_surname AS surname,
				IF(subscriptions.subscription_email, users.user_email, NULL) AS email,
				users.user_gender AS gender,
				users.user_enrolled_year AS enrol_year,
				users.user_nickname AS nickname
			FROM
				subscriptions
			INNER JOIN users
				ON	subscriptions.subscription_user_entity_id = users.user_entity_id
			WHERE';
		// If there's a restriction on the usert, apply it here
		if (NULL !== $user_id) {
			$sql .= '	subscriptions.subscription_user_entity_id = ? AND ';
			$bind_data[] = $user_id;
		}
		// Final conditions
		$sql .= '	subscriptions.subscription_organisation_entity_id = ?
				AND	subscriptions.subscription_member = TRUE
				AND	subscriptions.subscription_deleted = FALSE
			';
		// Run the query and return the raw results array.
		$bind_data[] = $organisation_id;
		$sql .= ' AND ' . $FilterSql;
		$bind_data = array_merge($bind_data, $BindData);
		$query = $this->db->query($sql, $bind_data);
		return $query->result_array();
	}

	/// Get all teams down to a depth of @a $levels levels.
	/**
	 * @param $organisation_id int Entity id of organisation to get teams of.
	 * @param $levels int Number of levels to go down where 0 is the first set
	 *	of teams only (Default=1, down to team's subteams)
	 * @return array of teams, each with:
	 *	- 'id' (Entity id of team).
	 *	- 'parent_id' (Entity id of parent team, which is either another team
	 *		in the result or is @a $organisation_id).
	 *	- 'name' (Team name).
	 */
	function GetTeams($organisation_id, $levels = 1)
	{
		/// @pre $levels < 5 Any more is just crazy
		assert('$levels < 5');
		
		// Start at each organisation and match those that have a sequence of
		// 0 up to @a $levels joins up to @a $organisation_id
		$bind_data = array();
		$sql = '
			SELECT	team0.organisation_entity_id AS id,
					team0.organisation_parent_organisation_entity_id AS parent_id,
					team0.organisation_name AS name
			FROM	organisations AS team0';
		for ($level_counter = 1; $level_counter <= $levels; ++$level_counter) {
			$sql .= '
				LEFT JOIN organisations AS team'.$level_counter.'
					ON	team'.($level_counter-1).'.organisation_parent_organisation_entity_id
							!= ?
					AND	team'.($level_counter-1).'.organisation_parent_organisation_entity_id
							= team'.$level_counter.'.organisation_entity_id';
			$bind_data[] = $organisation_id;
		}
		$sql .= '
			WHERE	team0.organisation_parent_organisation_entity_id = ?';
		$bind_data[] = $organisation_id;
		for ($level_counter = 1; $level_counter <= $levels; ++$level_counter) {
			$sql .= '
				OR	team'.$level_counter.'.organisation_parent_organisation_entity_id = ?';
			$bind_data[] = $organisation_id;
		}
		$sql .= ' ORDER BY team0.organisation_name';
		
		// Perform the query
		$query = $this->db->query($sql, $bind_data);
		return $query->result_array();
	}
	
	function GetBusinessCards($OrganisationId, $FilterSql = 'TRUE', $BindData = array())
	{
		$bind_data = array($OrganisationId);
		$sql = '
			SELECT	business_cards.business_card_user_entity_id AS user_id,
					business_cards.business_card_id AS id,
					business_cards.business_card_business_card_group_id AS group_id,
					business_cards.business_card_image_id AS image_id,
					business_cards.business_card_name AS name,
					business_cards.business_card_title AS title,
					business_cards.business_card_course AS course,
					business_cards.business_card_blurb AS blurb,
					business_cards.business_card_email AS email,
					business_cards.business_card_mobile AS mobile,
					business_cards.business_card_phone_internal AS phone_internal,
					business_cards.business_card_phone_external AS phone_external,
					business_cards.business_card_postal_address AS postal_address,
					business_card_groups.business_card_group_name AS group_name 
			FROM		business_cards
			INNER JOIN	business_card_groups
					ON	business_cards.business_card_business_card_group_id
							= business_card_groups.business_card_group_id
			LEFT JOIN	subscriptions
					ON	subscriptions.subscription_user_entity_id
							= business_cards.business_card_user_entity_id
					AND	subscriptions.subscription_deleted	= FALSE
					AND subscriptions.subscription_member	= TRUE
			LEFT JOIN	users
					ON	users.user_entity_id
							= business_cards.business_card_user_entity_id
			WHERE		business_card_groups.business_card_group_organisation_entity_id
							= ?
					AND	business_cards.business_card_deleted = 0
					AND	' . $FilterSql;
		$bind_data = array_merge($bind_data, $BindData);
		$query = $this->db->query($sql, $bind_data);
		return $query->result_array();
	}
	
	
	# returns whether a member is subscribed and confirmed
	function IsSubscribed($UserId,$OrganisationId) {
		$sql = '
				SELECT subscriptions.subscription_user_entity_id
				FROM   subscriptions
				WHERE  subscriptions.subscription_user_entity_id = "'.$UserId.'"
					   AND subscriptions.subscription_organisation_entity_id = "'.$OrganisationId.'"
					   AND subscriptions.subscription_user_confirmed = "1"
					   AND subscriptions.subscription_member = "1"
				';
		$query = $this->db->query($sql);
		$result_array = $query->result_array();
		if(count($result_array) == 0) {
			return false;
		} else {
			return true;
		}
	}
	
	# sets vip to $status
	function UpdateVipStatus($Status,$UserId,$OrgId) {
		$sql = '
				UPDATE subscriptions
				SET    subscription_vip = "'.$Status.'"				
				WHERE  subscription_user_entity_id = "'.$UserId.'"
				       AND subscription_organisation_entity_id = "'.$OrgId.'"
				';
		$query = $this->db->query($sql);
		return $this->db->affected_rows();
	}
	
	# sets paid to $status
	function UpdatePaidStatus($Status,$UserId,$OrgId) {
		$sql = '
				UPDATE subscriptions
				SET subscription_paid = "'.$Status.'"				
				WHERE  subscription_user_entity_id = "'.$UserId.'"
				       AND subscription_organisation_entity_id = "'.$OrgId.'"				
				';
		$this->db->query($sql);
		return $this->db->affected_rows();
	}	
	
	# Assumes not already in DB (use AlreadyMember to check)
	# Also needs to inform the member of the invitations, is this done in prefs?
	function InviteMember($UserId,$OrgId) {
		$sql = '
				INSERT INTO subscriptions
							(
							subscription_organisation_entity_id,
							subscription_user_entity_id,
							)
						VALUES
							{
							\''.$OrgId.'\',
							\''.$UserId.'\'
							}
				';
		$this->db->query($sql);
	}
	
	function RemoveSubscription($UserId,$OrgId) {
		$sql = '
				UPDATE subscriptions
				SET subscription_member = "0"
				WHERE  subscription_user_entity_id = "'.$UserId.'"
				       AND subscription_organisation_entity_id = "'.$OrgId.'"								
				';
		$this->db->query($sql);		
	}
	
	# sets member=1
	function ConfirmMember($UserId,$OrgId) {
		$sql = '
				UPDATE subscriptions
				SET subscription_member = "1"
				WHERE  subscription_user_entity_id = "'.$UserId.'"
				       AND subscription_organisation_entity_id = "'.$OrgId.'"				
				';
		$this->db->query($sql);		
	}
	
	#sets confirmed=1
	function ConfirmSubscription($UserId,$OrgId) {
		$sql = '
				UPDATE subscriptions
				SET subscription_user_confirmed = "1"
				WHERE  subscription_user_entity_id = "'.$UserId.'"
				       AND subscription_organisation_entity_id = "'.$OrgId.'"				
				';
		$this->db->query($sql);			
	}
	
	# A check against an organisation inviting a user twice	
	function AlreadyMember($UserId,$OrgId) {
		$sql = '
				SELECT subscriptions.subscription_user_entity_id
				FROM   subscriptions
				WHERE  subscriptions.subscription_user_entity_id = "'.$UserId.'"
					   AND subscriptions.subscription_organisation_entity_id = "'.$OrganisationId.'"
				';
		$query = $this->db->query($sql);
		$result_array = $query->result_array();
		if(count($result_array) == 0) {
			return false;
		} else {
			return true;
		}
	}

}	
?>