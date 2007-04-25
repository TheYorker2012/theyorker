<?php
/**
 * This model retrieves data required for the My Account page in the Vip Area
 * Also has functions for updating the same data
 *
 * @author Owen Jones ((oj502 - oj502@york.ac.uk)
 */

class Orgaccount_model extends Model {

    function Orgaccount_model()
    {
        // Call the Model constructor
        parent::Model();
    }

	/**
	 *	Function gets an organisations maintainers details
	*	@param $DirectoryEntryName
	*	@returns Array of maintainers details along with if the maintainer is a student.
	 */
	function GetDirectoryOrganisationMaintainer($DirectoryEntryName)
	{
		$sql =
			'SELECT'.
			' organisations.organisation_entity_id,'.
			' organisations.organisation_maintainer_email,'.
			' organisations.organisation_maintainer_user_entity_id,'.
			' organisations.organisation_maintainer_name,'.
			' users.user_firstname,'.
			' users.user_surname,'.
			' entities.entity_username'.
			' FROM organisations '.
			'LEFT JOIN users '.
			'ON users.user_entity_id = organisations.organisation_maintainer_user_entity_id '.
			'LEFT JOIN entities '.
			'ON entities.entity_id = organisations.organisation_maintainer_user_entity_id '.
			'WHERE organisations.organisation_directory_entry_name=? ';

		$query = $this->db->query($sql, $DirectoryEntryName);
		return $query->result_array();
	}
	function UpdateDirectoryOrganisationMaintainer($DirectoryEntryName, $Data)
	{
		$sql =
			'UPDATE organisations SET'.
			' organisations.organisation_maintainer_email=? ,'.
			' organisations.organisation_maintainer_user_entity_id=? ,'.
			' organisations.organisation_maintainer_name=? '.
			'WHERE organisations.organisation_directory_entry_name=? ';
		$query = $this->db->query($sql, array($Data['maintainer_email'], $Data['maintainer_user_entity_id'], $Data['maintainer_name'], $DirectoryEntryName));
		return true;
	}
}