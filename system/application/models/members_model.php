<?php
class Members_model extends Model {

	function Members_Model()
	{
		parent::Model();
	}


	function GetAllMemberDetails($organisation_id) {
		# email = on mailing list, awaiting reply = confirmed, 
		$sql = '
				SELECT 
				subscriptions.subscription_paid,
				subscriptions.subscription_email, 
				subscriptions.subscription_vip,
				subscriptions.subscription_user_confirmed,
				subscriptions.subscription_deleted,
				subscriptions.subscription_member,
				subscriptions.subscription_interested,				
				users.user_firstname,
				users.user_surname,
				users.user_email,
				users.user_entity_id,
				users.user_gender,
				users.user_enrolled_year,
				users.user_nickname
				FROM 
				subscriptions
				INNER JOIN users
				ON subscriptions.subscription_user_entity_id = users.user_entity_id
				WHERE subscriptions.subscription_organisation_entity_id = "'.$organisation_id.'"
				';
		$query = $this->db->query($sql);	
		$tmpmembers = array();
		$members = array();
		foreach($query->result() as $row) {
			$tmpmembers['nickname']       = $row->user_nickname;
			$tmpmembers['firstname']      = $row->user_firstname;
			$tmpmembers['surname']        = $row->user_surname;
			$tmpmembers['id']             = $row->user_entity_id;
			$tmpmembers['email']          = $row->user_email;
			$tmpmembers['gender']         = $row->user_gender;
			$tmpmembers['enrol_year']     = $row->user_enrolled_year;
			$tmpmembers['paid']           = $row->subscription_paid;
			$tmpmembers['if_email']       = $row->subscription_email;
			$tmpmembers['vip']            = $row->subscription_vip;
			$tmpmembers['confirmed']      = $row->subscription_user_confirmed;
			$tmpmembers['deleted']        = $row->subscription_deleted;
			$tmpmembers['member']         = $row->subscription_member;
			$tmpmembers['interested']     = $row->subscription_interested;
			$members[] = $tmpmembers;
		}
		return $members;
	}
	
	/// Gets a users membership with an organisation.
	/**
	 * @param $user_id integer User entity id.
	 * @param $organisation_id integer Organisation entity id.
	 * @return FALSE or associative array of membership attributes.
	 *	- Subscription must be membership
	 *	- Subscription must not be deleted
	 *	- email will only be returned if the member is on the mailing list
	 */
	function GetMemberDetails($user_id, $organisation_id)
	{
		$sql = '
			SELECT
				subscriptions.subscription_paid AS paid,
				subscriptions.subscription_email AS on_mailing_list,
				subscriptions.subscription_vip AS vip,
				subscriptions.subscription_interested AS interested,
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
			WHERE	subscriptions.subscription_user_entity_id = ?
				AND	subscriptions.subscription_organisation_entity_id = ?
				AND	subscriptions.subscription_member = TRUE
				AND	subscriptions.subscription_deleted = FALSE
			';
		$bind_data = array($user_id, $organisation_id);
		$query = $this->db->query($sql, $bind_data);
		if (!$query->num_rows()) {
			return FALSE;
		} else {
			$result = $query->result_array();
			return $result[0];
		}
	}

	function GetTeams($organisation_id) {
		$sql = '
				SELECT 
				organisations.organisation_entity_id,
				organisations.organisation_name
				FROM
				organisations
				WHERE organisation_parent_organisation_entity_id = "'.$organisation_id.'"
				';
		$query = $this->db->query($sql);	
		$tmpteams = array();
		$teams = array();
		foreach($query->result() as $row) {
			$tmpteams['organisation_id']    = $row->organisation_entity_id;
			$tmpteams['organisation_name']  = $row->organisation_name;
			$teams[] = $tmpteams;
		}
		return $teams;
				
	}

}	
?>