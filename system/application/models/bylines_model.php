<?php
/**
 * This model should control all data to do with bylines.
 *
 * @author Richard Ingle (ri504)
 *
 */
class Bylines_model extends Model
{

	function __construct()
	{
		// Call the Model Constructor
		parent::Model();
		$this->load->library('wikiparser');
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
				$result_item['name'] = $row->byline_display_name;
				$result_item['image'] = $row->byline_image_id;
				$result[$row->byline_id] = $result_item;
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
				$result_item['name'] = $row->byline_display_name;
				$result_item['image'] = $row->byline_image_id;
				$result[$row->byline_id] = $result_item;
			}			
		}
		return $result;
	}
}

?>