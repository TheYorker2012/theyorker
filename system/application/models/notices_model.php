<?php

/**
 * @file notices_model.php
 * @brief Model for organisation and team notices.
 * @author James Hogan (jh559@cs.york.ac.uk)
 */

/// Model for organisation and team notices.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class Notices_model extends model
{
	/// Default constructor
	function __construct()
	{
		parent::model();
		
		$this->load->model('organisation_model');
	}
	
	// FRONT END
	
	/// Get the notices for an organisation and its teams.
	/**
	 * @param $OrganisationId entity_id Id of organisation/team.
	 * @param $Expired bool Whether to include expired notices.
	 * @param $Levels int Number of levels to go down where 1 is the first set
	 *	of teams only.
	 */
	function GetNoticesForOrganisation($OrganisationId, $UserId = NULL, $Expired = FALSE, $Levels = NULL)
	{
		// Get bits of the query using the organisation_model
		$team_query_data = $this->organisation_model->GetTeams_QueryData(
			$OrganisationId,
			'organisations',
			TRUE,
			$Levels
		);
		
		// Start at each organisation and match those that have a sequence of
		// 0 up to @a $levels joins up to @a $organisation_id
		// Fields
		$sql = '
		SELECT
			notices.notice_id,
			notices.notice_subject,
			notices.notice_content_wikitext,
			notices.notice_content_cache,
			notices.notice_published,
			notices.notice_deleted,
			UNIX_TIMESTAMP(notices.notice_updated)			AS notice_updated,
			UNIX_TIMESTAMP(notices.notice_expires)			AS notice_expires,
			notices.notice_expires <= NOW()					AS notice_expired,
			notices.notice_published AND NOT notice_deleted	AS notice_available,
			notices.notice_published AND NOT notice_deleted
				AND NOT notices.notice_expires <= NOW()		AS notice_visible
		FROM organisations';
		
		// Joins to parent organisations
		$sql .= $team_query_data['joins'];
		
		// Only those of interest to the user
		$inner_bind = array();
		if (NULL !== $UserId) {
			$sql .= '
			INNER JOIN subscriptions
				ON	subscription.subscription_user_entity_id
					= ?
				AND	subscription.subscription_organisation_entity_id
					= organisations.organisation_entity_id
				AND	subscription.subscription_user_confirmed = TRUE
				AND	subscription.subscription_user_deleted = FALSE';
			$inner_bind[] = $UserId;
		}
		
		$sql .= '
		INNER JOIN notices
			ON	notices.notice_organisation_entity_id
				= organisations.organisation_entity_id';
		
		// Conditions for descent from main organisation
		$conjuncts = array();
		$conjuncts[] = 'notices.notice_published = TRUE';
		$conjuncts[] = 'notices.notice_deleted = FALSE';
		if (!$Expired) {
			$conjuncts[] = 'notices.notice_expires > NOW()';
		}
		$conjuncts[] = $team_query_data['where'];
		$sql .= ' WHERE '.implode(' AND ', $conjuncts);
		
		$bind_data = array_merge(
			$team_query_data['join_bind'],
			$inner_bind,
			$team_query_data['where_bind']
		);
		
		// Sort by the descendent name
		$sql .= ' ORDER BY notices.notice_expires';
		
		// Perform the query
		$query = $this->db->query($sql, $bind_data);
		return $query->result_array();
	}
}

?>