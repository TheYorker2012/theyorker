<?php
/**
 * This model retrieves data required for the Preferences Wizard.
 *
 * @author Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Prefs_model extends Model {

    function Prefs_model()
    {
        // Call the Model constructor
        parent::Model();
    }

	/**
	 * @return An array of colleges that are part of York University
	 *  - ['college_id']
	 *	- ['college_name']
	 */
	function GetColleges()
	{
		$sql =
			'SELECT'.
			' college_organisation_entity_id AS college_id,'.
			' college_name '.
			'FROM colleges '.
			'ORDER BY college_name ASC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function GetYears()
	{
		$sql =
			'SELECT'.
			' year_id '.
			'FROM years '.
			'ORDER BY year_id ASC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * @return An array of departments that are part of York University
	 *  - ['department_id']
	 *	- ['department_name']
	 */
	function GetDepartments()
	{
		$sql =
			'SELECT'.
			' organisation_entity_id AS department_id,'.
			' organisation_name AS department_name '.
			'FROM organisations '.
			'WHERE organisation_organisation_type_id = 7 '.
			'ORDER BY department_name ASC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function IsDepartment($DeptId)
	{
		$sql =
			'SELECT'.
			' organisation_entity_id '.
			'FROM organisations '.
			'WHERE organisation_organisation_type_id = 7 '.
			'AND organisation_entity_id = ' . $DeptId;
		$query = $this->db->query($sql);
		if ($this->db->num_rows() == 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function GetModules($DeptId)
	{
		$sql =
			'SELECT'.
			' organisation_entity_id AS module_id,'.
			' organisation_name AS module_name '.
			'FROM organisations '.
			'WHERE organisation_organisation_type_id = 8 '.
			'AND organisation_parent_organisation_entity_id = ' . $DeptId . ' '.
			'ORDER BY module_name ASC';
		$query = $this->db->query($sql);
		return $query->result_array();
	}

}