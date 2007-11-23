<?php
/**
 * This model should manages tags office reviews stuff
 * Only for add/edit/remove tags and tag groups.
 *
 *@author Owen Jones (oj502) 
 *
 */
class Tags_model extends Model
{

	function __construct()
	{
		// Call the Model Constructor
		parent::Model();
	}
	///////////Tags
	//Checking tags
	function GetAllTags($get_deleted=0){
		$sql ="SELECT 
				tags.tag_id as id,
				tags.tag_name as name,
				tags.tag_order as tag_order,
				tags.tag_banner_name as banner_name,
				tags.tag_archive as archive, 
				tag_groups.tag_group_id as group_id,
				tag_groups.tag_group_name as group_name,
				tag_groups.tag_group_ordered as group_ordered,
				content_types.content_type_id,
				content_types.content_type_name 
				FROM tags 
				INNER JOIN tag_groups ON 
					tags.tag_tag_group_id = tag_groups.tag_group_id 
				INNER JOIN content_types ON
					tag_groups.tag_group_content_type_id = content_types.content_type_id
				WHERE tags.tag_deleted=? AND tags.tag_type='grouped' 
				ORDER BY content_types.content_type_name ASC, tag_groups.tag_group_id ASC, tags.tag_order ASC, tags.tag_name ASC";
		$query = $this->db->query($sql,array($get_deleted));
		return $query->result_array();
	}
	function GetTag($id){
		$sql ="SELECT 
				tags.tag_id,
				tags.tag_name,
				tags.tag_order,
				tags.tag_banner_name,
				tags.tag_archive, 
				tag_groups.tag_group_id,
				tag_groups.tag_group_name,
				content_types.content_type_id,
				content_types.content_type_name 
				FROM tags 
				INNER JOIN tag_groups ON 
					tags.tag_tag_group_id = tag_groups.tag_group_id 
				INNER JOIN content_types ON
					tag_groups.tag_group_content_type_id = content_types.content_type_id
				WHERE tags.tag_id=? 
				LIMIT 1";
		$query = $this->db->query($sql,array($id));
		$result = $query->result_array();
		return $result[0];
	}
	//Modifying tags
	function AddTag ($name, $group_id, $type='grouped'){
		//find out if group forces ordering.
		$sql="SELECT tag_group_ordered FROM tag_groups WHERE tag_group_id=? LIMIT 1";
		$query = $this->db->query($sql,array($group_id));
		if($query->num_rows() > 0 ){
			if($query->row()->tag_group_ordered == 0){
				$order=null;
			}else{
				//Get max order position
				$sql="SELECT MAX(tag_order) as max_order FROM tags WHERE tag_tag_group_id=?";
				$query = $this->db->query($sql,array($group_id));
				if($query->num_rows() > 0 ){
					$order = $query->row()->max_order + 1;
				}else{
					$order = 1;
				}
			}
			$sql ="INSERT INTO tags (tag_name, tag_type, tag_tag_group_id, tag_order) VALUES (?, ?, ?, ?)";
			$query = $this->db->query($sql,array($name, $type, $group_id, $order));
		}else{
			//invalid group id, do nothing.
		}
	}
	
	function UpdateTag ($id, $name, $group_id, $type='grouped'){
		$sql ="UPDATE tags SET 
			tag_name=?,
			tag_type=?,
			tag_tag_group_id=?
			WHERE tag_id=? LIMIT 1";
		$query = $this->db->query($sql,array($name, $type, $group_id, $id));
	}
	
	function DoesOrderPositionExist($id, $order_number)
	{
		$sql = 'SELECT tag_id FROM tags  
				WHERE 	tag_tag_group_id = ? AND tag_order=? LIMIT 1';
		$query = $this->db->query($sql,array($id, $order_number));
		return ($query->num_rows() > 0);
	}
	
	//Swap orders
	//Assumes group has an ordering
	//Assumes group has a good ordering
	function SwapTagOrder($tagorder_id_1, $tagorder_id_2, $tag_group_id)
	{
		$this->db->trans_start();
		$sql = 'SELECT	tag_id
			FROM	tags
			WHERE	tag_order = ?
			AND		tag_tag_group_id = ?';
		$query = $this->db->query($sql,array($tagorder_id_1, $tag_group_id));
		$row = $query->row();
		$tag_id_1 = $row->tag_id;

		$sql = 'SELECT	tag_id
			FROM	tags
			WHERE	tag_order = ?
			AND		tag_tag_group_id = ?';
		$query = $this->db->query($sql,array($tagorder_id_2, $tag_group_id));
		$row = $query->row();
		$tag_id_2 = $row->tag_id;

		$sql = 'UPDATE	tags
			SET	tag_order = ?
			WHERE tag_id = ?';
		$query = $this->db->query($sql,array($tagorder_id_2, $tag_id_1));

		$sql = 'UPDATE	tags
			SET	tag_order = ?
			WHERE tag_id = ?';
		$query = $this->db->query($sql,array($tagorder_id_1, $tag_id_2));
		$this->db->trans_complete();
	}
	function IsTagInUse($tag_id)
	{
		$sql= 'SELECT organisation_tag_organisation_entity_id
		FROM organisation_tags WHERE organisation_tag_tag_id=?';
		$query = $this->db->query($sql,array($tag_id));
		return ($query->num_rows() > 0);
	}
	function RemoveTagFromGroup($tag_group_id, $tag_id)
	{
		$this->db->trans_start();
		//Check if group has an ordering or not.
		$sql="SELECT tag_group_ordered FROM tag_groups WHERE tag_group_id=? LIMIT 1";
		$query = $this->db->query($sql,array($tag_group_id));
		if($query->num_rows() > 0 ){
			if($query->row()->tag_group_ordered == 1){
				/////////////start reordering to be able to delete it
				$sql = 'SELECT	tag_order
					FROM	tags
					WHERE tag_tag_group_id = ? AND tag_id=?';
				$query = $this->db->query($sql,array($tag_group_id, $tag_id));
				$row = $query->row();
				$delete_tag_order = $row->tag_order;//Its order number
		
				$sql = 'SELECT	MAX(tag_order) as max_tag_order
					FROM tags
					WHERE tag_tag_group_id = ?';
				$query = $this->db->query($sql,array($tag_group_id));
				$row = $query->row();
				$max_tag_order = $row->max_tag_order;//The highest order number
		
				for($i = $delete_tag_order; $i < $max_tag_order; $i++)
				{
					self::SwapTagOrder($i, $i + 1, $tag_group_id);//keep swaping untill the highest
				}
			}
		//can delete now its the highest
		$sql = 'DELETE FROM tags 
				WHERE  tags.tag_tag_group_id = ? AND tags.tag_id=?
				LIMIT 1';
		$query = $this->db->query($sql,array($tag_group_id, $tag_id));
		}
		$this->db->trans_complete();
	}

	///////////Tag Groups
	//Checking tag groups
	function IsTagGroupEmpty($group_id){
		$sql="SELECT tags.tag_id FROM tags WHERE tags.tag_tag_group_id=?";
		$query = $this->db->query($sql,array($group_id));
		return ($query->num_rows() == 0);
	}
	function GetAllTagGroups(){
		$sql="SELECT
				tag_group_id as group_id,
				tag_group_name as group_name,
				tag_group_ordered as group_ordered,
				tag_group_order as group_order,
				content_types.content_type_id,
				content_types.content_type_name 				
				FROM tag_groups 
				INNER JOIN content_types ON
					tag_groups.tag_group_content_type_id = content_types.content_type_id
				ORDER BY content_types.content_type_name ASC, tag_group_order ASC, tag_group_name ASC
				";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function GetTagGroup($group_id){
		$sql="SELECT
				tag_group_id,
				tag_group_name,
				tag_group_ordered,
				tag_group_order, 
				content_types.content_type_id,
				content_types.content_type_name 				
				FROM tag_groups 
				INNER JOIN content_types ON
					tag_groups.tag_group_content_type_id = content_types.content_type_id
				WHERE tag_group_id=? LIMIT 1";
		$query = $this->db->query($sql,array($group_id));
		$result = $query->result_array();
		return $result[0];
	}
	
	function GetAllReviewContentTypes(){
		$sql="SELECT
			content_types.content_type_id as type_id,
			content_types.content_type_name as type_name 				
			FROM content_types 
			WHERE content_types.content_type_has_reviews=1
			ORDER BY content_types.content_type_name ASC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	//Modifying tag groups
	function AddTagGroup ($name,$content_type_id,$ordered=1){
		//Get max order position
		$sql="SELECT MAX(tag_group_order) as max_order FROM tag_groups WHERE tag_group_content_type_id=?";
		$query = $this->db->query($sql,array($content_type_id));
		if($query->num_rows() > 0){
			$order = $query->row()->max_order + 1;
		}else{
			$order = 1;
		}
		$sql ="INSERT INTO tag_groups
				(tag_group_name,
				 tag_group_content_type_id,
				 tag_group_ordered,
				 tag_group_order) VALUES (?, ?, ?, ?)";
		$query = $this->db->query($sql,array($name,$content_type_id,$ordered,$order));
	}
	
	function UpdateTagGroup ($id, $name, $content_type_id, $ordered){
		$this->db->trans_start();
		$sql = "SELECT tag_group_ordered FROM tag_groups WHERE tag_group_id=? LIMIT 1";
		$query = $this->db->query($sql,array($id));
		$old_ordered = $query->row()->tag_group_ordered;
		
		//if group used to be ordered, and wants to remove the ordering,
		//overwrite the ordering with nulls.
		if($ordered==0 && $old_ordered==1){
			$sql="UPDATE tags SET tag_order=NULL WHERE tag_tag_group_id=?";
			$query = $this->db->query($sql,array($id));
		}
		//if the group had no ordering, but now needs one an ordering
		//needs to be created so ordering functions will work.
		if($ordered==1 && $old_ordered==0){
			$sql="UPDATE tags SET tag_order=NULL WHERE tag_tag_group_id=?";
			$query = $this->db->query($sql,array($id));
			
			$sql="SELECT count(tag_id) as group_size FROM tags WHERE tag_tag_group_id=?";
			$query = $this->db->query($sql,array($id));
			if(!empty($query)){
				$num_rows = $query->row()->group_size;
			}else{
				//invalid group id
				$num_rows=0;
			}
			
			for($count=1;$count<=$num_rows;$count++){
				$sql="UPDATE tags SET tag_order=? WHERE tag_tag_group_id=? AND tag_order IS NULL ORDER BY tag_name ASC LIMIT 1";
				$query = $this->db->query($sql,array($count, $id));
			}
		}
		$sql ="UPDATE tag_groups SET 
			tag_group_name=?,
			tag_group_content_type_id=?,
			tag_group_ordered=?
			WHERE tag_group_id=? LIMIT 1";
		$query = $this->db->query($sql,array($name, $content_type_id, $ordered, $id));
		$this->db->trans_complete();
	}
	
	function DoesGroupOrderPositionExist($content_type_id, $order_number)
	{
		$sql = 'SELECT tag_group_id FROM tag_groups  
				WHERE 	tag_group_content_type_id = ? AND tag_group_order=? LIMIT 1';
		$query = $this->db->query($sql,array($content_type_id, $order_number));
		return ($query->num_rows() > 0);
	}
	
	//Swap orders
	function SwapTagGroupOrder($group_order_id_1, $group_order_id_2, $content_type_id)
	{
		$this->db->trans_start();
		$sql = 'SELECT	tag_group_id
			FROM	tag_groups
			WHERE	tag_group_order = ?
			AND		tag_group_content_type_id = ?';
		$query = $this->db->query($sql,array($group_order_id_1, $content_type_id));
		$row = $query->row();
		$tag_group_id_1 = $row->tag_group_id;
		
		$sql = 'SELECT	tag_group_id
			FROM	tag_groups
			WHERE	tag_group_order = ?
			AND		tag_group_content_type_id = ?';
		$query = $this->db->query($sql,array($group_order_id_2, $content_type_id));
		$row = $query->row();
		$tag_group_id_2 = $row->tag_group_id;
		
		$sql = 'UPDATE	tag_groups
			SET	tag_group_order = ?
			WHERE	tag_group_content_type_id  = ?
			AND	tag_group_id = ?';
		$query = $this->db->query($sql,array($group_order_id_2, $content_type_id, $tag_group_id_1));

		$sql = 'UPDATE	tag_groups
			SET	tag_group_order = ?
			WHERE	tag_group_content_type_id  = ?
			AND	tag_group_id = ?';
		$query = $this->db->query($sql,array($group_order_id_1, $content_type_id, $tag_group_id_2));

		$this->db->trans_complete();
	}
	
	//assumes a good ordering
	function RemoveTagGroup($content_type_id, $tag_group_id)
	{
		$this->db->trans_start();
		/////////////start reordering to be able to delete it
		$sql = 'SELECT	tag_group_order
			FROM	tag_groups
			WHERE tag_group_content_type_id = ? AND tag_group_id=?';
		$query = $this->db->query($sql,array($content_type_id, $tag_group_id));
		$row = $query->row();
		$delete_group_order = $row->tag_group_order;//Its order number

		$sql = 'SELECT	MAX(tag_group_order) as max_group_order
			FROM tag_groups
			WHERE tag_group_content_type_id = ?';
		$query = $this->db->query($sql,array($content_type_id));
		$row = $query->row();
		$max_group_order = $row->max_group_order;//The highest order number

		for($i = $delete_group_order; $i < $max_group_order; $i++)
		{
			self::SwapTagGroupOrder($i, $i + 1, $content_type_id);//keep swaping untill the highest
		}
		
		//can delete now its the highest
		$sql = 'DELETE FROM tag_groups 
				WHERE  tag_groups.tag_group_content_type_id = ? AND tag_groups.tag_group_id=?
				LIMIT 1';
		$query = $this->db->query($sql,array($content_type_id, $tag_group_id));
		$this->db->trans_complete();
	}
}
?>
