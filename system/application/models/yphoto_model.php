<?php

class yphoto_model extends model {
	function GetTags(){
		$sql = '	SELECT tag_name
					FROM tags
					WHERE tag_deleted = 0';
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result[] = $row->tag_name;
			}
			return $result;
		}else{return array();}		
	}
}

?>
