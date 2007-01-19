<?php
/**
 * This model retrieves data from the directory.
 *
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class Directory_model extends Model {

	function Directory_model()
	{
		// Call the Model constructor
		parent::Model();
	}

	///	Find the organisations in the directory.
	/**
	 * @return An array of organisations with:
	 *	- ['organisation_name']        (organisations)
	 *	- ['organisation_description'] (organisations)
	 *	- ['organisation_type_name']   (organisation_types)
	 */
	function GetDirectoryOrganisations()
	{
		$sql =
			'SELECT'.
			' organisations.organisation_name,'.
			' organisations.organisation_description,'.
			' organisation_types.organisation_type_name '.
			'FROM organisations '.
			'INNER JOIN organisation_types '.
			'ON organisations.organisation_organisation_type_id=organisation_types.organisation_type_id '.
			'WHERE organisations.organisation_directory=1'.
			' AND organisation_types.organisation_type_directory=1 '.
			'ORDER BY organisation_name';

		$query = $this->db->query($sql);

		return $query->result_array();
	}
	
	/// Get an organisation by name.
	/**
	 */
	function GetDirectoryOrganisationByName($OrganisationName)
	{
		if (1 === preg_match('/^[a-z_\d ]+$/',$OrganisationName)) {
			$sql =
				'SELECT'.
				' organisations.organisation_name,'.
				' organisations.organisation_description,'.
				' organisation_types.organisation_type_name '.
				'FROM organisations '.
				'INNER JOIN organisation_types '.
				'ON organisations.organisation_organisation_type_id=organisation_types.organisation_type_id '.
				'WHERE organisations.organisation_directory=1'.
				' AND organisation_types.organisation_type_directory=1'.
				' AND organisations.organisation_name LIKE "'.$OrganisationName.'" '.
				'ORDER BY organisation_name';
	
			$query = $this->db->query($sql);
	
			return $query->result_array();
		} else {
			return array();
		}
	}
	
	/// Alter an organisation name so it can be used in a URI.
	/**
	 * This function will replace spaces with underscores and change upper
	 *	case letters to lower case.
	 * @param $OrganisationName string Name of organisation.
	 * @return string Altered name.
	 * @note This technically won't actually be any shorter!
	 */
	function ShortenOrganisationName($OrganisationName)
	{
		$OrganisationName = strtolower($OrganisationName);
		$OrganisationName = str_replace(' ','_',$OrganisationName);
		return $OrganisationName;
	}

}
?>