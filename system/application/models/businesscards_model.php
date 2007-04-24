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
			$order, $start_date, $end_date)
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
				business_card_timestamp)
			VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0,CURRENT_TIMESTAMP)';
		$this->db->query($sql,array($user_id, $image_id, $name, 
						$title, $blurb, $course, 
						$group_id, $email, $mobile, 
						$phone_internal, $phone_external, 
						$postal_address, $order,
						$start_date, $end_date));
		$sql = 'SELECT 	business_card_id
			FROM	business_cards
			WHERE	(business_card_id=LAST_INSERT_ID())';
		$query = $this->db->query($sql);
		return $query
	}
	
	function DeleteBusinessCard($id)
	{
		$sql = 'UPDATE 	business_cards
			SET	business_card_deleted = 1
			WHERE	(business_card_id=?)';
		$this->db->query($sql,array($id));
	}
	
	function AddOrganisationCardGroup($Data)
	{
	$sql = 'INSERT INTO business_card_groups 
	(business_card_group_name, business_card_group_organisation_entity_id, business_card_group_order) 
	VALUES (?, ?, ?)';
	$query = $this->db->query($sql, array($Data['group_name'], $Data['organisation_id'], $Data['group_order']));
	return ($this->db->affected_rows() > 0);
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

	/// retrieves generic bylines for use by anyone
	function GetGeneralBylines()
	{
		$sql = 'SELECT 	bylines.byline_id,
				bylines.byline_display_name,
				bylines.byline_image_id
			FROM	bylines
			WHERE	bylines.byline_user_entity_id IS NULL
			AND	bylines.byline_deleted = 0';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$result_item['id'] = $row->byline_id;
				$result_item['name'] = $row->byline_display_name;
				$result_item['image'] = $row->byline_image_id;
				$result[] = $result_item;
			}			
		}
		return $result;
	}

	/// retrieves a users bylines
	function GetUsersBylines($user_id)
	{
		$sql = 'SELECT 	bylines.byline_id,
				bylines.byline_display_name,
				bylines.byline_image_id
			FROM	bylines
			WHERE	bylines.byline_user_entity_id = ?
			AND	bylines.byline_deleted = 0';
		$query = $this->db->query($sql, array($user_id));
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$result_item['id'] = $row->byline_id;
				$result_item['name'] = $row->byline_display_name;
				$result_item['image'] = $row->byline_image_id;
				$result[] = $result_item;
			}			
		}
		return $result;
	}
}

?>