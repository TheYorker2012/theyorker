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
	*  CAMPAIGN VOTING
	*****************************************************/

	/**
	 * Sets the users vote in the vote table.
	 * @returns nothing.
	 */
	function SetUserVote($campaign_id, $user_id)
	{
		$cur_campaign_id = self::GetUserVote($user_id);
		if ($cur_campaign_id == FALSE)
			self::AddNewVote($campaign_id, $user_id);
		else
			self::UpdateUserVote($cur_campaign_id, $campaign_id, $user_id);
	}
	
	/**
	 * Returns the id of the current users vote.
	 * @returns the campaign id of the current users vore or FALSE if no vote has been cast.
	 */
	function GetUserVote($user_id)
	{
        	$sql = 'SELECT campaign_user_campaign_id
			FROM campaign_users
			WHERE campaign_user_user_entity_id = ?';
		$query = $this->db->query($sql,array($user_id));
		$row = $query->row();
		if ($query->num_rows() > 0)
			return $row->campaign_user_campaign_id;
		else
			return FALSE;
	}
	
	/**
	 * Updates the users vote to be for a different campaign.
	 * @returns nothing.
	 */
	function UpdateUserVote($cur_campaign_id, $campaign_id, $user_id)
	{
		$this->db->trans_start();
		$sql = 'UPDATE campaign_users
			SET campaign_user_campaign_id = ?
			WHERE campaign_user_user_entity_id = ?';
		$this->db->query($sql,array($campaign_id, $user_id));
		$sql = 'UPDATE campaigns
			SET campaign_votes = campaign_votes + 1
			WHERE campaign_id = ?';
		$this->db->query($sql,array($campaign_id));
		$sql = 'UPDATE campaigns
			SET campaign_votes = campaign_votes - 1
			WHERE campaign_id = ?';
		$this->db->query($sql,array($cur_campaign_id));
		$this->db->trans_complete();
	}

	/**
	 * Inserts a new vote into the database.
	 * @returns nothing.
	 */
	function AddNewVote($campaign_id, $user_id)
	{
		$this->db->trans_start();
		$sql = 'INSERT INTO campaign_users (
				campaign_user_campaign_id,
				campaign_user_user_entity_id)
			VALUES (?, ?)';
		$this->db->query($sql,array($campaign_id, $user_id));
		$sql = 'UPDATE campaigns
			SET campaign_votes = campaign_votes + 1
			WHERE campaign_id = ?';
		$this->db->query($sql,array($campaign_id));
		$this->db->trans_complete();
	}

	/**
	 * Removes the users current vote.
	 * @returns nothing.
	 */
	function WithdrawVote($user_id)
	{
		$this->db->trans_start();
		$campaign_id = self::GetUserVote($user_id);
		$sql = 'UPDATE campaigns
			SET campaign_votes = campaign_votes - 1
			WHERE campaign_id = ?';
		$this->db->query($sql, array($campaign_id));
		$sql = 'DELETE FROM campaign_users
			WHERE campaign_user_user_entity_id = ?';
		$this->db->query($sql, array($user_id));
		$this->db->trans_complete();
	}

	/**
	 * Wipes the votes table.
	 * @returns nothing.
	 */
	function ClearVotes()
	{
		$this->db->trans_start();
		$sql = 'DELETE FROM campaign_users';
		$this->db->query($sql);
		$sql = 'UPDATE campaigns
			SET campaign_votes = 0';
		$this->db->query($sql);
		$this->db->trans_complete();
	}

	/*****************************************************
	*  CAMPAIGN PETITIONING
	*****************************************************/
	
	function GetPetitionID()
	{
		$sql = 'SELECT campaign_id
			FROM campaigns
			WHERE campaign_deleted = FALSE
			AND campaign_petition = TRUE';
		$query = $this->db->query($sql);
		$row = $query->row();
		if ($query->num_rows() == 1)
			return $row->campaign_id;
		else
			return FALSE;
	}

	function StartPetition()
	{
		$this->db->trans_start();
		$sql = 'SELECT max( campaign_votes ) AS max_campaign_votes
			FROM campaigns';
		$query = $this->db->query($sql);
		$row = $query->row();
		$sql = 'SELECT campaign_id
			FROM campaigns
			WHERE campaign_votes = ?
			AND campaign_deleted = FALSE';
		$query = $this->db->query($sql,array($row->max_campaign_votes));
		$row = $query->row();
		$this->db->trans_complete();
		if ($query->num_rows() == 1)
		{
			$this->db->trans_start();
                	$sql = 'UPDATE campaigns
				SET campaign_petition = TRUE,
					campaign_petition_signatures = 0
				WHERE campaign_id = ?';
			$this->db->query($sql,array($row->campaign_id));
			self::ClearVotes();
			$this->db->trans_complete();
			return TRUE;
		}
		else
			return FALSE;
	}

	/**
	 * Removes the users current signature.
	 * @returns nothing.
	 */
	function WithdrawSignature($user_id)
	{
		$this->db->trans_start();
		$campaign_id = self::GetUserVote($user_id);
		$sql = 'UPDATE campaigns
			SET campaign_petition_signatures = campaign_petition_signatures - 1
			WHERE campaign_id = ?';
		$this->db->query($sql, array($campaign_id));
		$sql = 'DELETE FROM campaign_users
			WHERE campaign_user_user_entity_id = ?';
		$this->db->query($sql, array($user_id));
		$this->db->trans_complete();
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