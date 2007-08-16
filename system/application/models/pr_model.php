<?php

// Pr Model

class Pr_model extends Model {

	function Pr_Model()
	{
		parent::Model();
	}

	///	Return list of all organisations and thier Name of Place, Date of Last Review, Number of Reviews, and Info Complete status
	function GetContentTypeId($content_type_codename)
	{
		$sql = 'SELECT content_type_id FROM content_types WHERE content_type_codename = ?';

		$query = $this->db->query($sql, $content_type_codename );

		if ($query->num_rows() != 0) {

			$query = $query->result_array();

			$query = $query[0];

			$content_type_id = $query['content_type_id'];

			return $content_type_id;
		} else {
			return 0;
		}
	}

	///	Return list of all organisations and thier Name of Place, Date of Last Review, Number of Reviews, and Info Complete status
	function GetReviewContextListFromId($content_type, $urlpath='directory/', $urlpostfix='')
	{

		$sql =
		'
		SELECT
			organisations.organisation_name as name,
			organisations.organisation_directory_entry_name as shortname,
			CONCAT(?, organisations.organisation_directory_entry_name, ?) as link,
			review_contexts.review_context_assigned_user_entity_id as assigned_user_id,
			CONCAT(users.user_firstname, " ", users.user_surname) as assigned_user_name,

			(
			 review_context_contents.review_context_content_blurb IS NOT NULL AND
			 review_context_contents.review_context_content_quote IS NOT NULL AND
			 review_context_contents.review_context_content_average_price IS NOT NULL AND
			 review_context_contents.review_context_content_recommend_item IS NOT NULL AND
			 review_context_contents.review_context_content_rating IS NOT NULL
			) as info_complete,

			(
			 SELECT MAX(article_publish_date)
			 FROM articles
			 WHERE
				articles.article_content_type_id = ?
			  AND
				articles.article_organisation_entity_id = organisations.organisation_entity_id
			  AND
			  	articles.article_deleted = 0
			  AND
			  	articles.article_live_content_id IS NOT NULL
			) as date_of_last_review,

			(
			 SELECT COUNT(*)
			 FROM articles
			 WHERE
				articles.article_content_type_id = ?
			  AND
				articles.article_organisation_entity_id = organisations.organisation_entity_id
			  AND
			  	articles.article_deleted = 0
			  AND
			  	articles.article_live_content_id IS NOT NULL
			) as review_count

		FROM organisations
		INNER JOIN review_contexts
		  ON
			review_contexts.review_context_organisation_entity_id = organisations.organisation_entity_id
		  AND
			review_contexts.review_context_content_type_id = ?
		  AND
			review_contexts.review_context_deleted = 0

		LEFT JOIN users
		  ON
		   	users.user_entity_id =  review_contexts.review_context_assigned_user_entity_id

		LEFT JOIN review_context_contents
		  ON
			review_contexts.review_context_live_content_id = review_context_contents.review_context_content_id

		WHERE organisation_parent_organisation_entity_id IS NULL

		ORDER BY info_complete, date_of_last_review ASC
		';

		$query = $this->db->query($sql, array($urlpath, $urlpostfix, $content_type, $content_type, $content_type) );

		return $query->result_array();

	}
	
	// gets a list of all organisations which are suggestions for the directory
	function GetSuggestedOrganisations()
	{
		$sql = 'SELECT	organisations.organisation_entity_id,
						organisations.organisation_name,
						organisations.organisation_directory_entry_name,
						organisations.organisation_suggesters_name,
						organisations.organisation_timestamp
				FROM	organisations
				INNER JOIN organisation_types
				ON		organisations.organisation_organisation_type_id = organisation_types.organisation_type_id
				AND		organisation_type_directory = 1
				WHERE	organisations.organisation_needs_approval = 1
				AND		organisations.organisation_pr_rep = 0
				AND		organisations.organisation_deleted = 0
				ORDER BY organisations.organisation_name ASC';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item['org_id'] = $row->organisation_entity_id;
				$result_item['org_name'] = $row->organisation_name;
				$result_item['org_dir_entry_name'] = $row->organisation_directory_entry_name;
				$result_item['user_name'] = $row->organisation_suggesters_name;
				$result_item['suggested_time'] = $row->organisation_timestamp;
				$result[] = $result_item;
			}
		}
		return $result;
	}
	
	// gets a list of all organisations which are accepted suggestions for the directory, but are in the unassigned state
	function GetUnassignedOrganisations()
	{
		$sql = 'SELECT	organisations.organisation_entity_id,
						organisations.organisation_name,
						organisations.organisation_directory_entry_name
				FROM	organisations
				INNER JOIN organisation_types
				ON		organisations.organisation_organisation_type_id = organisation_types.organisation_type_id
				AND		organisation_types.organisation_type_directory = 1
				WHERE	organisations.organisation_needs_approval = 0
				AND		organisations.organisation_pr_rep = 0
				AND		organisations.organisation_deleted = 0
				ORDER BY organisation_name ASC';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item['org_id'] = $row->organisation_entity_id;
				$result_item['org_name'] = $row->organisation_name;
				$result_item['org_dir_entry_name'] = $row->organisation_directory_entry_name;
				$result[] = $result_item;
			}
		}
		return $result;
	}
	
	// returns a list, in the same order as GetUnassignedOrganistions(), of all reps which have asked to look after the organisation
	function GetUnassignedOrganisationsReps()
	{
		$sql = 'SELECT	subscriptions.subscription_organisation_entity_id,
						organisations.organisation_directory_entry_name,
						subscriptions.subscription_user_entity_id,
						users.user_firstname,
						users.user_surname
				FROM	subscriptions
				INNER JOIN organisations
				ON		organisations.organisation_entity_id = subscriptions.subscription_organisation_entity_id
				INNER JOIN organisation_types
				ON		organisations.organisation_organisation_type_id = organisation_types.organisation_type_id
				AND		organisation_type_directory = 1
				INNER JOIN users
				ON		subscriptions.subscription_user_entity_id = users.user_entity_id
				WHERE	subscriptions.subscription_pr_rep = 1
				AND		subscriptions.subscription_pr_rep_chosen = "suggestion"
				AND		organisations.organisation_deleted = 0
				ORDER BY organisation_name ASC';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item['org_id'] = $row->subscription_organisation_entity_id;
				$result_item['org_dir_name'] = $row->organisation_directory_entry_name;
				$result_item['user_id'] = $row->subscription_user_entity_id;
				$result_item['user_firstname'] = $row->user_firstname;
				$result_item['user_surname'] = $row->user_surname;
				$result[] = $result_item;
			}
		}
		return $result;
	}
	
	// returns a list of all reps which have asked to look after the organisation specified
	function GetOrganisationReps($shortname)
	{
		$sql = 'SELECT	subscriptions.subscription_user_entity_id,
						users.user_firstname,
						users.user_surname
				FROM	subscriptions
				INNER JOIN organisations
				ON		organisations.organisation_entity_id = subscriptions.subscription_organisation_entity_id
				AND		organisations.organisation_deleted = 0
				AND		organisation_directory_entry_name = ?
				INNER JOIN organisation_types
				ON		organisations.organisation_organisation_type_id = organisation_types.organisation_type_id
				AND		organisation_types.organisation_type_directory = 1
				INNER JOIN users
				ON		subscriptions.subscription_user_entity_id = users.user_entity_id
				WHERE	subscriptions.subscription_pr_rep = 1
				AND		subscriptions.subscription_pr_rep_chosen = "suggestion"
				ORDER BY organisations.organisation_name ASC';
		$query = $this->db->query($sql, array($shortname));
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item['user_id'] = $row->subscription_user_entity_id;
				$result_item['user_firstname'] = $row->user_firstname;
				$result_item['user_surname'] = $row->user_surname;
				$result[] = $result_item;
			}
		}
		return $result;
	}
	
	// gets a list of all organisations which are accepted suggestions for the directory, but are in the pending state
	function GetPendingOrganisations()
	{
		$sql = 'SELECT	organisations.organisation_entity_id,
						organisations.organisation_name,
						organisations.organisation_directory_entry_name,
						subscriptions.subscription_user_entity_id,
						users.user_firstname,
						users.user_surname
				FROM	organisations
				INNER JOIN organisation_types
				ON		organisations.organisation_organisation_type_id = organisation_types.organisation_type_id
				AND		organisation_type_directory = 1
				INNER JOIN subscriptions
				ON		subscriptions.subscription_organisation_entity_id = organisations.organisation_entity_id
				AND		subscriptions.subscription_pr_rep = 1
				AND		subscriptions.subscription_pr_rep_chosen = "choosing"
				INNER JOIN users
				ON		users.user_entity_id = subscriptions.subscription_user_entity_id
				WHERE	organisations.organisation_needs_approval = 0
				AND		organisations.organisation_pr_rep = 1
				AND		organisations.organisation_deleted = 0
				ORDER BY organisation_name ASC';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item['org_id'] = $row->organisation_entity_id;
				$result_item['org_name'] = $row->organisation_name;
				$result_item['org_dir_entry_name'] = $row->organisation_directory_entry_name;
				$result_item['user_id'] = $row->subscription_user_entity_id;
				$result_item['user_firstname'] = $row->user_firstname;
				$result_item['user_surname'] = $row->user_surname;
				$result[] = $result_item;
			}
		}
		return $result;
	}
	
	//returns the status of an organisation in the pr system
	//can be: suggestion, unassigned, pending and assigned
	function GetOrganisationStatus($shortname)
	{
		$sql = 'SELECT	organisations.organisation_pr_rep,
						organisations.organisation_needs_approval,
						organisations.organisation_entity_id
				FROM	organisations
				INNER JOIN organisation_types
				ON		organisations.organisation_organisation_type_id = organisation_types.organisation_type_id
				AND		organisation_type_directory = 1
				WHERE	organisations.organisation_directory_entry_name = ?';
		$query1 = $this->db->query($sql,array($shortname));
		$row1 = $query1->row();
		if ($query1->num_rows() == 1)
		{
			if ($row1->organisation_needs_approval == 1)
				return 'suggestion';
			else
			{
				if ($row1->organisation_pr_rep == 0)
					return 'unassigned';
				else
				{
					$sql = 'SELECT	subscriptions.subscription_pr_rep_chosen
							FROM	organisations
							INNER JOIN subscriptions
							ON		subscriptions.subscription_organisation_entity_id = ?
							AND		subscriptions.subscription_deleted = 0
							AND		subscriptions.subscription_pr_rep = 1
							WHERE	organisations.organisation_entity_id = ?';
					$query2 = $this->db->query($sql,array($row1->organisation_entity_id, $row1->organisation_entity_id));
					$row2 = $query2->row();
					if ($query2->num_rows() == 1)
					{
						if ($row2->subscription_pr_rep_chosen == 'choosing')
							return 'pending';
						else if ($row2->subscription_pr_rep_chosen == 'chosen')
							return 'assigned';
						else
							return FALSE;
					}
					else
						return FALSE;
				}
			}
		}
		else
			return FALSE;
	}
	
	//assumes organisation is in pending status
	function GetPendingOrganisationRep($shortname)
	{
		$sql = 'SELECT	organisations.organisation_entity_id
				FROM	organisations
				WHERE	organisations.organisation_directory_entry_name = ?';
		$query1 = $this->db->query($sql,array($shortname));
		$row1 = $query1->row();
		if ($query1->num_rows() == 1)
		{
			$sql = 'SELECT	subscriptions.subscription_user_entity_id,
							users.user_firstname,
							users.user_surname
					FROM	subscriptions
					INNER JOIN users
					ON		users.user_entity_id = subscriptions.subscription_user_entity_id
					WHERE	subscriptions.subscription_organisation_entity_id = ?
					AND		subscriptions.subscription_deleted = 0
					AND		subscriptions.subscription_pr_rep_chosen = "choosing"
					AND		subscriptions.subscription_pr_rep = 1';
			$query2 = $this->db->query($sql,array($row1->organisation_entity_id));
			$row2 = $query2->row();
			if ($query2->num_rows() == 1)
			{
				$result['user_id'] = $row2->subscription_user_entity_id;
				$result['user_firstname'] = $row2->user_firstname;
				$result['user_surname'] = $row2->user_surname;
				return $result;
			}
			else
				return FALSE;
		}
		else
			return FALSE;
	}
	
	
	//this deletes the organisation and its contents with the given shortname
	function SetOrganisationDeleted($shortname)
	{
		$sql = 'SELECT	organisations.organisation_entity_id
				FROM	organisations
				WHERE	organisations.organisation_directory_entry_name = ?';
		$query = $this->db->query($sql,array($shortname));
		$row = $query->row();
		if ($query->num_rows() == 1)
		{
			$sql = 'UPDATE	organisations
					SET		organisation_deleted = 1
					WHERE	organisations.organisation_entity_id = ?';
			$this->db->query($sql,array($row->organisation_entity_id));
			$sql = 'UPDATE	organisation_contents
					SET		organisation_content_deleted = 1
					WHERE	organisation_contents.organisation_content_organisation_entity_id = ?';
			$this->db->query($sql,array($row->organisation_entity_id));
			return TRUE;
		}
		else
			return FALSE;
	}
	
	function SetOrganisationUnassigned($shortname)
	{
		$sql = 'UPDATE	organisations
				SET		organisations.organisation_needs_approval = 0,
						organisations.organisation_pr_rep = 0
				WHERE	organisations.organisation_directory_entry_name = ?
				AND		organisations.organisation_deleted = 0';
		$query = $this->db->query($sql,array($shortname));
	}
	
	//NOTE: must also make sure there is only one non deleted rep subscription to this org with "choosing" rep state
	function SetOrganisationPending($shortname, $user_id)
	{
		//find the org and if it exists
		$sql = 'SELECT	organisations.organisation_entity_id
				FROM	organisations
				WHERE	organisations.organisation_directory_entry_name = ?
				AND		organisation_deleted = 0';
		$query1 = $this->db->query($sql,array($shortname));
		$row1 = $query1->row();
		if ($query1->num_rows() == 1)
		{
			//update the organisation to pending status
			$sql = 'UPDATE	organisations
					SET		organisations.organisation_needs_approval = 0,
							organisations.organisation_pr_rep = 1
					WHERE	organisations.organisation_directory_entry_name = ?
					AND		organisation_deleted = 0';
			$this->db->query($sql,array($shortname));
			//set pr_rep = false for all org subscriptions that are not $user_id
			$sql = 'UPDATE	subscriptions
					SET		subscriptions.subscription_pr_rep = 0
					WHERE	subscriptions.subscription_organisation_entity_id = ?
					AND		subscriptions.subscription_user_entity_id != ?
					AND		subscriptions.subscription_deleted = 0';
			$this->db->query($sql,array($row1->organisation_entity_id, $user_id));
			//does a subscription exist for user id / org id?
			$sql = 'SELECT	subscriptions.subscription_user_entity_id
					FROM	subscriptions
					WHERE	subscriptions.subscription_organisation_entity_id = ?
					AND		subscriptions.subscription_user_entity_id = ?';
			$query2 = $this->db->query($sql,array($row1->organisation_entity_id, $user_id));
			if ($query2->num_rows() == 1)
			{
				//if yes, set pr_rep = 1 and pr_chosen to "choosing"
				$sql = 'UPDATE	subscriptions
						SET		subscriptions.subscription_pr_rep = 1,
								subscriptions.subscription_pr_rep_chosen = "choosing"
						WHERE	subscriptions.subscription_organisation_entity_id = ?
						AND		subscriptions.subscription_user_entity_id = ?
						AND		subscriptions.subscription_deleted = 0';
				$this->db->query($sql,array($row1->organisation_entity_id, $user_id));
			}
			else
			{
				//if no, create a new subscription with pr_rep = 1 and pr_chosen to "choosing"
				$sql = 'INSERT INTO subscriptions(
									subscription_organisation_entity_id,
									subscription_user_entity_id,
									subscription_calendar,
									subscription_todo,
									subscription_pr_rep,
									subscription_pr_rep_chosen)
						VALUES (?,?,0,0,1,"choosing")';
				$this->db->query($sql,array($row1->organisation_entity_id, $user_id));
			}
		}
		else
			return FALSE;
	}
	
	//@pre assumes organisation is in pending state
	//@post organisation is in assigned state
	function SetOrganisationAssigned($shortname, $user_id)
	{
		//find the org and if it exists
		$sql = 'SELECT	organisations.organisation_entity_id
				FROM	organisations
				WHERE	organisations.organisation_directory_entry_name = ?
				AND		organisation_deleted = 0';
		$query1 = $this->db->query($sql,array($shortname));
		$row1 = $query1->row();
		if ($query1->num_rows() == 1)
		{
			//if there is a subscription unset the pr rep flag
			$sql = 'UPDATE	subscriptions
					SET		subscriptions.subscription_pr_rep_chosen = "chosen"
					WHERE	subscriptions.subscription_organisation_entity_id = ?
					AND		subscriptions.subscription_user_entity_id = ?
					AND		subscriptions.subscription_deleted = 0';
			$this->db->query($sql,array($row1->organisation_entity_id, $user_id));
			return TRUE;
		}
		else
			return FALSE;
	}
	
	function RequestRepToUnassignedOrganisation($shortname, $user_id)
	{
		//find the org and if it exists
		$sql = 'SELECT	organisations.organisation_entity_id
				FROM	organisations
				WHERE	organisations.organisation_directory_entry_name = ?
				AND		organisation_deleted = 0';
		$query1 = $this->db->query($sql,array($shortname));
		$row1 = $query1->row();
		if ($query1->num_rows() == 1)
		{
			//does a subscription exist for user id / org id?
			$sql = 'SELECT	subscriptions.subscription_user_entity_id
					FROM	subscriptions
					WHERE	subscriptions.subscription_organisation_entity_id = ?
					AND		subscriptions.subscription_user_entity_id = ?
					AND		subscriptions.subscription_deleted = 0';
			$query2 = $this->db->query($sql,array($row1->organisation_entity_id, $user_id));
			if ($query2->num_rows() == 1)
			{
				//if yes, set pr_rep = 1 and pr_chosen to "suggestion"
				$sql = 'UPDATE	subscriptions
						SET		subscriptions.subscription_pr_rep = 1,
								subscriptions.subscription_pr_rep_chosen = "suggestion"
						WHERE	subscriptions.subscription_organisation_entity_id = ?
						AND		subscriptions.subscription_user_entity_id = ?
						AND		subscriptions.subscription_deleted = 0';
				$this->db->query($sql,array($row1->organisation_entity_id, $user_id));
			}
			else
			{
				//if no, create a new subscription with pr_rep = 1 and pr_chosen to "suggestion"
				$sql = 'INSERT INTO subscriptions(
									subscription_organisation_entity_id,
									subscription_user_entity_id,
									subscription_calendar,
									subscription_todo,
									subscription_pr_rep,
									subscription_pr_rep_chosen)
						VALUES (?,?,0,0,1,"suggestion")';
				$this->db->query($sql,array($row1->organisation_entity_id, $user_id));
			}
		}
		else
			return FALSE;
	}
	
	function WithdrawRepFromUnassignedOrganisation($shortname, $user_id)
	{
		//find the org and if it exists
		$sql = 'SELECT	organisations.organisation_entity_id
				FROM	organisations
				WHERE	organisations.organisation_directory_entry_name = ?
				AND		organisation_deleted = 0';
		$query1 = $this->db->query($sql,array($shortname));
		$row1 = $query1->row();
		if ($query1->num_rows() == 1)
		{
			//does a subscription exist for user id / org id?
			$sql = 'SELECT	subscriptions.subscription_user_entity_id
					FROM	subscriptions
					WHERE	subscriptions.subscription_organisation_entity_id = ?
					AND		subscriptions.subscription_user_entity_id = ?
					AND		subscriptions.subscription_deleted = 0';
			$query2 = $this->db->query($sql,array($row1->organisation_entity_id, $user_id));
			if ($query2->num_rows() == 1)
			{
				//if there is a subscription unset the pr rep flag
				$sql = 'UPDATE	subscriptions
						SET		subscriptions.subscription_pr_rep = 0
						WHERE	subscriptions.subscription_organisation_entity_id = ?
						AND		subscriptions.subscription_user_entity_id = ?
						AND		subscriptions.subscription_deleted = 0';
				$this->db->query($sql,array($row1->organisation_entity_id, $user_id));
				return TRUE;
			}
			else //else there is nothing to withdraw
				return FALSE;
		}
		else
			return FALSE;
	}
	
	function WithdrawRepFromPendingOrganisation($shortname, $user_id)
	{
		//find the pending org 
		$sql = 'SELECT	organisations.organisation_entity_id
				FROM	organisations
				WHERE	organisations.organisation_directory_entry_name = ?
				AND		organisation_deleted = 0
				AND		organisations.organisation_needs_approval = 0
				AND		organisations.organisation_pr_rep = 1';
		$query1 = $this->db->query($sql,array($shortname));
		$row1 = $query1->row();
		if ($query1->num_rows() == 1) //if it exists
		{
			//unset the pr rep flag of the pending subscription rep
			$sql = 'UPDATE	subscriptions
					SET		subscriptions.subscription_pr_rep = 0
					WHERE	subscriptions.subscription_organisation_entity_id = ?
					AND		subscriptions.subscription_user_entity_id = ?
					AND		subscriptions.subscription_deleted = 0';
			$this->db->query($sql,array($row1->organisation_entity_id, $user_id));
			//set the organisation back to unassigned
			$sql = 'UPDATE	organisations
					SET		organisations.organisation_needs_approval = 0,
							organisations.organisation_pr_rep = 0
					WHERE	organisations.organisation_directory_entry_name = ?
					AND		organisations.organisation_deleted = 0';
			$this->db->query($sql,array($shortname));
			return TRUE;
		}
		else
			return FALSE;
	}
}


