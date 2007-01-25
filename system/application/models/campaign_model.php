<?php
/**
 * This model retrieves data for the Campaign pages.
 *
 * @author Richard Ingle (ri504)
 * 
 */
 
//TODO - prevent erros if no data present
 
class Campaign_model extends Model
{
	function CampaignModel()
	{
		//Call the Model Constructor
		parent::Model();
	}          
	
	/**
	 * Returns an array of the Campaigns that are currently being voted on
	 * in ascending order of name.
	 * @return An array containing arrays with names and votes.
	 */
	function GetCampaignNamesAndVotes()
	{
		//foreach($collection as $key => $value) {
		//}

		$sql = "SELECT campaign_name, campaign_votes, campaign_id
			FROM campaigns
			WHERE campaign_deleted = false
				AND campaign_timestamp < CURRENT_TIMESTAMP
			ORDER BY campaign_name ASC";
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item = array('id'=>$row->campaign_id,'name'=>$row->campaign_name,'votes'=>$row->campaign_votes);
				$result[] = $result_item;
			}
		}
		return $result;
	}
}
?>