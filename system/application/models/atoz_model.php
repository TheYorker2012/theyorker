<?php
/**
 * This model retrieves data for the A to Z page.
 *
 * \author Nick Evans
 */
class Atoz_model extends Model {

    function Atoz_model()
    {
		// Call the Model constructor
        parent::Model();
    }
   
   	/**
	* This function queries the organisation table for the names of organisations of a particular type
	* \param organisation_type_id Is the type of organisations to return
	* \return An array of organisation names
	*/
    function get_all_organisations_of_type($organisation_type_id)
    {
		if (!is_numeric($organisation_type_id)) {
			$organisation_type_id = 2; //Default organisation type is 2
		}
		
		$sql = "SELECT name FROM organisation WHERE organisation_type_id = ? ORDER BY name"; 

		$query = $this->db->query($sql, array($organisation_type_id)); //Using binds is safer.
		
        return $query->result_array();
    }
	
}
?>