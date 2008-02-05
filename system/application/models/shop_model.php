<?php

class Shop_model extends Model
{

	function Shop_model()
	{
		parent::Model();
	}
	
	/*
	* description
	* @return	- thing 1
			- thing 2
	*/
	function GetEventIDs()
	{
		$sql = 'SELECT	event_id
				FROM	shops';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item['id'] = $row->event_id;
				$result[] = $result_item;
			}
		}
		return $result;
	}

}