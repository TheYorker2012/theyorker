<?php
/**
 * This model should control all data to do with bylines.
 *
 * @author Richard Ingle (ri504)
 *
 */
class Businesscards_model extends Model
{

	function __construct()
	{
		// Call the Model Constructor
		parent::Model();
		$this->load->library('wikiparser');
	}
	
	// adds a byline, if the group id refers to a group with null org id
	function NewBusinessCard($user_id, $group_id, $image_id, $name,
			$title, $blurb, $course, $email, $mobile, 
			$phone_internal, $phone_external, $postal_address,
			$order, $start_date, $end_date, $force_publish=0)
	{
		$sql = 'INSERT INTO business_cards(
				business_card_user_entity_id,
				business_card_image_id,
				business_card_name,
				business_card_title,
				business_card_blurb,
				business_card_course,
				business_card_business_card_group_id,
				business_card_email,
				business_card_mobile,
				business_card_phone_internal,
				business_card_phone_external,
				business_card_postal_address,
				business_card_order,
				business_card_start_date,
				business_card_end_date,
				business_card_deleted,
				business_card_approved,
				business_card_timestamp)
			VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0,?,CURRENT_TIMESTAMP)';
		$this->db->query($sql,array($user_id, $image_id, $name, 
						$title, $blurb, $course, 
						$group_id, $email, $mobile, 
						$phone_internal, $phone_external, 
						$postal_address, $order,
						$start_date, $end_date, $force_publish));
		$sql = 'SELECT 	business_card_id
			FROM	business_cards
			WHERE	(business_card_id=LAST_INSERT_ID())';
		$query = $this->db->query($sql);
		return $query;
	}
	
	function UpdateBuisnessCard($user_id, $group_id, $image_id, $name,
			$title, $blurb, $course, $email, $mobile, 
			$phone_internal, $phone_external, $postal_address,
			$order, $start_date, $end_date, $business_card_id, $force_publish=0)
	{
		$sql = "UPDATE `business_cards` SET".
				" `business_card_user_entity_id` = ?,".
				" `business_card_image_id` = ?,".
				" `business_card_name` = ?,".
				" `business_card_title` = ?,".
				" `business_card_blurb` = ?,".
				" `business_card_course` = ?,".
				" `business_card_business_card_group_id` = ?,".
				" `business_card_email` = ?,".
				" `business_card_mobile` = ?,".
				" `business_card_phone_internal` = ?,".
				" `business_card_phone_external` = ?,".
				" `business_card_postal_address` = ?,".
				" `business_card_order` = ?,".
				" `business_card_start_date` = ?,".
				" `business_card_end_date` = ?,".
				" `business_card_approved` = ?".
				" WHERE `business_cards`.`business_card_id` = ?";
				$this->db->query($sql,array($user_id, $image_id, $name, 
							$title, $blurb, $course, 
							$group_id, $email, $mobile, 
							$phone_internal, $phone_external, 
							$postal_address, $order,
							$start_date, $end_date, $force_publish, $business_card_id));
		return ($this->db->affected_rows() > 0);
	}
	function ApproveBusinessCard($Id)
	{
		$sql = "UPDATE `business_cards` SET".
				" `business_card_approved` = 1".
				" WHERE `business_cards`.`business_card_id` = ?";
				$this->db->query($sql,$Id);
		return ($this->db->affected_rows() > 0);
	}
	
	function GetUserIdFromUsername($username)
	{
		$sql =
			'SELECT entities.entity_id as id '.
			'FROM entities '.
			'WHERE entities.entity_username=? ';
		$query = $this->db->query($sql, $username);
		$row = $query->row();
		if(empty($row)){
			return null;
		}else{
			return $row->id;
		}
	}
	function GetUsernameFromUserId($UserId)
	{
		$sql =
			'SELECT entities.entity_username as username '.
			'FROM entities '.
			'WHERE entities.entity_id=? ';
		$query = $this->db->query($sql, $UserId);
		$row = $query->row();
		if(empty($row)){
			return null;
		}else{
			return $row->username;
		}
	}
	
	function DeleteBusinessCard($id)
	{
		$sql = 'UPDATE 	business_cards
			SET	business_card_deleted = 1
			WHERE	(business_card_id=?)';
		$this->db->query($sql,array($id));
		return ($this->db->affected_rows() > 0);
	}
	
	function AddOrganisationCardGroup($Data)
	{
	$sql = 'INSERT INTO business_card_groups 
	(business_card_group_name, business_card_group_organisation_entity_id, business_card_group_order) 
	VALUES (?, ?, ?)';
	$query = $this->db->query($sql, array($Data['group_name'], $Data['organisation_id'], $Data['group_order']));
	return ($this->db->affected_rows() > 0);
	}
	function SelectMaxGroupOrderById($OrgId)
	{
		$sql = 'SELECT MAX(business_card_group_order) as highest_group_number
		FROM business_card_groups
		WHERE business_card_groups.business_card_group_organisation_entity_id=?';
		$query = $this->db->query($sql, $OrgId);
		
		$row = $query->row();
		return $row->highest_group_number;
	}
	
	function RemoveOrganisationCardGroupById($Id)
	{
		// Remove the organisation card group with the given id
		$sql =
			'DELETE FROM business_card_groups '.
			'WHERE	business_card_groups.business_card_group_id=? LIMIT 1';
		$query = $this->db->query($sql, $Id);
		return ($this->db->affected_rows() > 0);
	}
	
	/// Get all bylines
	/**
	 * @return array[bylines].
	 */
	function GetBylines()
	{
		$sql =
			'SELECT'.
			' business_cards.business_card_user_entity_id,'.
			' business_cards.business_card_id,'.
			' business_cards.business_card_name '.
			'FROM business_cards '.
			'INNER JOIN business_card_groups '.
			' ON business_card_groups.business_card_group_id = business_cards.business_card_business_card_group_id '.
			'WHERE business_cards.business_card_deleted = 0 '.
			'AND business_card_groups.business_card_group_organisation_entity_id IS NULL '.
			'ORDER BY business_cards.business_card_name';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$result_item['user_id'] = $row->business_card_user_entity_id;
				$result_item['id'] = $row->business_card_id;
				$result_item['name'] = $row->business_card_name;
				$result[] = $result_item;
			}			
		}
		return $result;
	}
}

?>