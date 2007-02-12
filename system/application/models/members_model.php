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
	
	function GetMemberDetails($member_id) {
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
				WHERE users.user_entity_id = "'.$member_id.'"
				LIMIT 1
				';
		$query = $this->db->query($sql);	
		$tmpmember = array();
		$member = array();
		foreach($query->result() as $row) {
			$tmpmember['nickname']       = $row->user_nickname;
			$tmpmember['firstname']      = $row->user_firstname;
			$tmpmember['surname']        = $row->user_surname;
			$tmpmember['id']             = $row->user_entity_id;
			$tmpmember['email']          = $row->user_email;
			$tmpmember['gender']        = $row->user_gender;
			$tmpmember['enrol_year']    = $row->user_enrolled_year;			
			$tmpmember['paid']           = $row->subscription_paid;
			$tmpmember['if_email']       = $row->subscription_email;
			$tmpmember['vip']            = $row->subscription_vip;
			$tmpmember['confirmed']      = $row->subscription_user_confirmed;
			$tmpmember['deleted']        = $row->subscription_deleted;
			$tmpmember['member']         = $row->subscription_member;
			$tmpmember['interested']     = $row->subscription_interested;
			$member[] = $tmpmember;
		}
		return $member;				
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