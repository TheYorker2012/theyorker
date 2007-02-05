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

	/*****************************************************
	*  CAMPAIGNS
	*****************************************************/
	
	/**
	 * Returns an array of the Campaigns that are currently being voted on
	 * in ascending order of name.
	 * @return An array of arrays containing campaign id, names, article id and votes.
	 */
	function GetCampaignList()
	{
		$sql = 'SELECT campaign_name, campaign_votes, campaign_id, campaign_article_id
			FROM campaigns
			WHERE campaign_deleted = false
				AND campaign_timestamp < CURRENT_TIMESTAMP
			ORDER BY campaign_name ASC';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result_item = array(
					'name'=>$row->campaign_name,
					'votes'=>$row->campaign_votes,
					'article'=>$row->campaign_article_id
					);
				$result[$row->campaign_id] = $result_item;
			}
		}
		return $result;
	}
	
	/**
	 * Returns name, signatures and article id of the given campaign id
	 * @return the name as a string.
	 */
	function GetPetitionCampaign($campaign_id)
	{
		$sql = 'SELECT campaign_name, campaign_petition_signatures, campaign_article_id
			FROM campaigns
			WHERE campaign_id = ?';
		$query = $this->db->query($sql,array($campaign_id));
		$row = $query->row();
		return array(
			'name'=>$row->campaign_name,
			'signatures'=>$row->campaign_petition_signatures,
			'article'=>$row->campaign_article_id
			);
	}

	/**
	 * Returns the id of the current petition.
	 * @returns the campaign id of the current petition or FALSE if no campaign is in petition mode.
	 */
	function GetPetitionStatus()
	{
		$sql = 'SELECT campaign_id
			FROM campaigns
			WHERE campaign_petition = true';
		$query = $this->db->query($sql,array());
		$row = $query->row();
		if ($query->num_rows() > 0)
			return $row->campaign_id;
		else
			return FALSE;
	}

	/*****************************************************
	*  PROGRESS REPORTS
	*****************************************************/
	
	/**
	 * Returns an array of the last $count progress report items for the given campaign id.
	 * @return An array of arrays containing campaign id, names and votes.
	 */
	function GetCampaignProgressReports($campaign_id, $count)
	{
		$sql = 'SELECT 
			progress_report_articles.progress_report_article_article_id
			FROM progress_report_articles
			INNER JOIN articles
			ON articles.article_id = progress_report_articles.progress_report_article_article_id
			WHERE progress_report_articles.progress_report_article_campaign_id = ?
			ORDER BY articles.article_publish_date DESC
			LIMIT 0,3';
		$query = $this->db->query($sql,array($campaign_id));
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result[] = $row->progress_report_article_article_id;
			}
		}
		return $result;

	}
}
?>