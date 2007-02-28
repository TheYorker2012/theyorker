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
	
	/// Get all teams down to a depth of @a $levels levels.
	/**
	 * @param $OrganisationId int Entity id of organisation to get teams of.
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
	 * @param $OrganisationId int Entity id of organisation to get teams of.
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
		
		$team_aliases = array(0 => $TeamAlias);
		$joins = '';
		$join_bind = array();
		
		$where_disjuncts = array();
		$where_bind = array();
		
		if ($IncludeOrg) {
			// Allow the lowest level to be the organisation
			$where_disjuncts[] .= $team_aliases[0].
				'.organisation_entity_id = ?';
			$where_bind[] = $OrganisationId;
		}
		
		// Start at each organisation and match those that have a sequence of
		// 0 up to @a $levels joins up to @a $organisation_id
		for ($level_counter = 0; $level_counter < $Levels; ++$level_counter) {
			if ($level_counter > 0) {
				$team_aliases[$level_counter] = 'team'.$level_counter;
				$joins .= '
				LEFT JOIN organisations AS '.$team_aliases[$level_counter].'
					ON	'.$team_aliases[$level_counter-1].'.organisation_entity_id
						!= ?
					AND	'.$team_aliases[$level_counter-1].'.organisation_parent_organisation_entity_id
						!= ?
					AND	'.$team_aliases[$level_counter-1].'.organisation_parent_organisation_entity_id
						= '.$team_aliases[$level_counter].'.organisation_entity_id';
				$join_bind[] = $OrganisationId;
				$join_bind[] = $OrganisationId;
			}
			
			$where_disjuncts[] = $team_aliases[$level_counter].
				'.organisation_parent_organisation_entity_id = ?';
			$where_bind[] = $OrganisationId;
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