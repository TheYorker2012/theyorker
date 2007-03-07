<?php

/**
 * @file organisation_model.php
 * @brief Model for organisations.
 * @author James Hogan (jh559@cs.york.ac.uk)
 */

/// Model for organisations.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class Organisation_model extends model
{
	/// Default constructor
	function __construct()
	{
		parent::model();
	}
	
	// HIGH LEVEL FUNCTIONS (FOR CONTROLLERS)
	
	/// Get all subteams of a particular team.
	/**
	 * @param $Team array with subteams and id.
	 * @return array(team id).
	 */
	function GetSubteamIds($Team, $result = array())
	{
		assert('is_array($Team)');
		if (array_key_exists('id', $Team)) {
			$result[] = (int)$Team['id'];
		}
		foreach ($Team['subteams'] as $subteam) {
			$result = $this->GetSubteamIds($subteam, $result);
		}
		return $result;
	}
	
	/// Get all teams down to a depth of @a $levels levels in a tree.
	/**
	 * @param $Depth int To pass into members_model::GetTeams.
	 * @return array(team_list, top_team).
	 */
	function GetTeamsTree($OrganisationId, $Depth = NULL)
	{
		// Get teams array from database
		$teams_list = $this->organisation_model->GetTeams($OrganisationId, TRUE, $Depth);
		
		// Reindex teams by entity id
		$all_teams = array();
		foreach ($teams_list as $team) {
			$team['subteams'] = array();
			$all_teams[(int)$team['id']] = $team;
		}
		
		// Set up team tree using references
		$real_top_id = FALSE;
		foreach ($all_teams as $id => $team) {
			$parent = (int)$team['parent_id'];
			if (array_key_exists($parent, $all_teams)) {
				$all_teams[$parent]['subteams'][] = &$all_teams[$id];
			} else {
				$real_top_id = $id;
			}
		}
		$top_teams = &$all_teams[$real_top_id];
		
		return array(&$all_teams, &$top_teams);
	}
	
	/// Get all teams down to a depth of @a $levels levels.
	/**
	 * @param $OrganisationId int/string or directory entry Entity id of organisation to get teams of.
	 * @param $IncludeOrg bool Whether to include the organisation itself.
	 * @param $Levels int Number of levels to go down where 1 is the first set
	 *	of teams only.
	 * @return array of teams, each with:
	 *	- 'id' (Entity id of team).
	 *	- 'parent_id' (Entity id of parent team, which is either another team
	 *		in the result or is @a $OrganisationId).
	 *	- 'name' (Team name).
	 */
	function GetTeams($OrganisationId, $IncludeOrg, $Levels = NULL)
	{
		// Use lower level function to get bits of the query
		$team_query_data = $this->GetTeams_QueryData(
			$OrganisationId,
			'team',
			$IncludeOrg,
			$Levels
		);
		
		// Start at each organisation and match those that have a sequence of
		// 0 up to @a $levels joins up to @a $organisation_id
		// Fields
		$sql = '
		SELECT	team.organisation_entity_id AS id,
				team.organisation_parent_organisation_entity_id AS parent_id,
				team.organisation_name AS name
		FROM	organisations AS team';
		
		// Joins to parent organisations
		$sql .= $team_query_data['joins'];
		
		// Conditions for descent from main organisation
		$sql .= '
		WHERE	'. $team_query_data['where'];
		
		$bind_data = array_merge(
			$team_query_data['join_bind'],
			$team_query_data['where_bind']
		);
		
		// Sort by the descendent name
		$sql .= ' ORDER BY team.organisation_name';
		
		// Perform the query
		$query = $this->db->query($sql, $bind_data);
		return $query->result_array();
	}
	
	// LOW LEVEL FUNCTIONS (FOR OTHER MODELS)
	
	/// Generate the bits of a query relating to the team hierarchy.
	/**
	 * @param $OrganisationId int/string Entity id or directory entry of organisation to get teams of.
	 * @param $TeamAlias string Alias of main (lowest) team in organisations.
	 * @param $IncludeOrg bool Whether to include the organisation itself.
	 * @param $Levels int Number of levels to go down where 1 is the first set
	 *	of teams only.
	 * @return associative array of query data:
	 *	- 'team_aliases'	associative array	Index level to SQL alias.
	 *	- 'joins'			string				Joins to parent teams.
	 *	- 'join_bind'		array				Bind data for joins.
	 *	- 'where'			string				Where condition.
	 *	- 'where_bind'		array				Bind data for where condition.
	 */
	function GetTeams_QueryData($OrganisationId, $TeamAlias, $IncludeOrg, $Levels = NULL)
	{
		// Initialise default value for @a $levels
		if (NULL === $Levels) {
			/// @note Default value of @a $Levels is 2
			$Levels = 2;
			
		} elseif ($Levels > 5) {
			/// @pre $levels <= 5 (Any more is just crazy)
			$Levels = 5;
		}
		
		// Find whether input organisation is entity_id or directory_entry_name
		$is_directory_entry_name = !is_numeric($OrganisationId);
		
		$team_aliases = array(0 => $TeamAlias);
		$joins = '';
		$join_bind = array();
		
		$where_disjuncts = array();
		$where_bind = array();
		
		// Generalise the entity id depending on how it was input
		if ($is_directory_entry_name) {
			// Get the entity id from a quick lookup using the directory entry.
			$joins .= '
			INNER JOIN organisations as org_id_lookup
				ON	org_id_lookup.organisation_directory_entry_name = ?';
			$join_bind[] = $OrganisationId;
			$org_entity_id = 'org_id_lookup.organisation_entity_id';
		} else {
			$org_entity_id = $this->db->escape($OrganisationId);
		}
		
		// Allow the lowest level to be the organisation
		if ($IncludeOrg) {
			$where_disjuncts[] .= $team_aliases[0].
				'.organisation_entity_id = '.$org_entity_id;
		}
		
		// Start at each organisation and match those that have a sequence of
		// 0 up to @a $levels joins up to @a $organisation_id
		for ($level_counter = 0; $level_counter < $Levels; ++$level_counter) {
			if ($level_counter > 0) {
				$team_aliases[$level_counter] = 'team'.$level_counter;
				$joins .= '
				LEFT JOIN organisations AS '.$team_aliases[$level_counter].'
					ON	'.$team_aliases[$level_counter-1].'.organisation_entity_id
						!= '.$org_entity_id.'
					AND	'.$team_aliases[$level_counter-1].'.organisation_parent_organisation_entity_id
						!= '.$org_entity_id.'
					AND	'.$team_aliases[$level_counter-1].'.organisation_parent_organisation_entity_id
						= '.$team_aliases[$level_counter].'.organisation_entity_id';
			}
			
			$where_disjuncts[] = $team_aliases[$level_counter].
				'.organisation_parent_organisation_entity_id = '.$org_entity_id;
		}
		
		$where = '('.implode(' OR ', $where_disjuncts).')';
		
		return array(
			'team_aliases'	=> $team_aliases,
			'joins'			=> $joins,
			'join_bind'		=> $join_bind,
			'where'			=> $where,
			'where_bind'	=> $where_bind,
		);
	}
}

?>