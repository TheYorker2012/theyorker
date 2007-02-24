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
	function GetMemberDetails($organisation_id, $user_id = NULL)
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
		$query = $this->db->query($sql, $bind_data);
		return $query->result_array();
	}

	function GetTeams($organisation_id) {
		$sql = '
			SELECT
				organisations.organisation_entity_id AS organisation_id,
				organisations.organisation_name AS organisation_name
			FROM
				organisations
			WHERE organisation_parent_organisation_entity_id = ?
				';
		$query = $this->db->query($sql, $organisation_id);
		return $query->result_array();
	}

}	
?>