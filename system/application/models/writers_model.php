<?php

// writers model

class Writers_model extends Model
{

	function Writers_Model()
	{
		parent::Model();
	}
	
	/*
	* this function gets a list of all users who have office access (editor/writer)
	* and it gives their full name not pseudonym's.
	* @return	- id of the user
			- full name of the user 
	*/
	function GetUsersWithOfficeAccess()
	{
		$sql = 'SELECT	user_entity_id,
						user_firstname,
						user_surname
				FROM	users
				WHERE	users.user_office_access = 1
				ORDER BY user_firstname ASC, user_surname ASC';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item['id'] = $row->user_entity_id;
				$result_item['firstname'] = $row->user_firstname;
				$result_item['surname'] = $row->user_surname;
				$result[] = $result_item;
			}
		}
		return $result;
	}

}