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
	
	function CampaignExists($campaign_id)
	{
		$sql = 'SELECT	campaign_id
				FROM	campaigns
				WHERE	campaign_deleted = 0
				AND		campaign_id = ?';
		$query = $this->db->query($sql,array($campaign_id));
		if ($query->num_rows() == 1)
		{
			return TRUE;
		}
		else
			return FALSE;
	}
	
	function AddNewCampaign($name, $article_id)
	{
		$this->db->trans_start();
			$sql = 'INSERT INTO campaigns(
								campaign_name,
								campaign_article_id)
					VALUES (?, ?)';
			$this->db->query($sql,array($name, $article_id));
			$sql = 'SELECT 	campaign_id
					FROM	campaigns
					WHERE	campaign_id = LAST_INSERT_ID()';
			$query = $this->db->query($sql);
			$id = $query->row()->campaign_id;
		$this->db->trans_complete();
		return $id;
	}
	
	function GetCampaignArticleID($campaign_id)
	{
		$sql = 'SELECT	campaign_article_id
				FROM	campaigns
				WHERE	campaign_deleted = 0
				AND		campaign_id = ?';
		$query = $this->db->query($sql,array($campaign_id));
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->campaign_article_id;
		}
		else
			return NULL;
	}
	
	function GetCampaignNameID($campaign_id)
	{
		$sql = 'SELECT	campaign_name
				FROM	campaigns
				WHERE	campaign_deleted = 0
				AND		campaign_id = ?';
		$query = $this->db->query($sql,array($campaign_id));
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->campaign_name;
		}
		else
			return NULL;
	}
	
	function GetCampaignStatusID($campaign_id)
	{
		$sql = 'SELECT	campaign_status
				FROM	campaigns
				WHERE	campaign_deleted = 0
				AND		campaign_id = ?';
		$query = $this->db->query($sql,array($campaign_id));
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->campaign_status;
		}
		else
			return NULL;
	}
	
	function SetCampaignName($campaign_id, $campaign_name)
	{
		$sql = 'UPDATE	campaigns
				SET		campaign_name = ?
				WHERE	campaign_deleted = 0
				AND		campaign_id = ?';
		$this->db->query($sql,array($campaign_name, $campaign_id));
		return TRUE;
	}
	
	function SetCampaignStatus($campaign_id, $status)
	{
		$sql = 'UPDATE	campaigns
				SET		campaign_status = ?
				WHERE	campaign_deleted = 0
				AND		campaign_id = ?';
		$this->db->query($sql,array($status, $campaign_id));
	}
	
	function SetCampaignDeleted($campaign_id)
	{
		$sql = 'UPDATE	campaigns
				SET		campaign_deleted = 1
				WHERE	campaign_id = ?';
		$this->db->query($sql,array($campaign_id));
	}
	
	/**
	 * Returns an array of the all the Campaigns
	 * in ascending order of name.
	 * @return An array of arrays containing campaign id, names, article id and votes.
	 */
	function GetFullCampaignList()
	{
		$sql = 'SELECT	campaign_name,
						campaign_votes,
						campaign_id,
						campaign_article_id,
						campaign_petition,
						campaign_status
				FROM	campaigns
				WHERE	campaign_deleted = 0
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
					'article'=>$row->campaign_article_id,
					'has_been_petitioned'=>$row->campaign_petition,
					'status'=>$row->campaign_status
					);
				$result[$row->campaign_id] = $result_item;
			}
		}
		return $result;
	}
	
	/**
	 * Returns an array of the Campaigns that are currently being voted on
	 * in ascending order of name.
	 * @return An array of arrays containing campaign id, names, article id and votes.
	 */
	function GetLiveCampaignList()
	{
		$sql = 'SELECT	campaign_name,
						campaign_votes,
						campaign_id,
						campaign_article_id,
						campaign_petition
				FROM	campaigns
				WHERE	campaign_deleted = 0
				AND		campaign_status = "live"
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
					'article'=>$row->campaign_article_id,
					'has_been_petitioned'=>$row->campaign_petition
					);
				$result[$row->campaign_id] = $result_item;
			}
		}
		return $result;
	}
	
	/**
	 * Returns an array of the all the campaigns that are going to be published in the future
	 * in ascending order of name.
	 * @return An array of arrays containing campaign id, names, article id, votes, publish date and expired.
	 */
	function GetFutureCampaignList()
	{
		$sql = 'SELECT	campaign_name,
						campaign_votes,
						campaign_id,
						campaign_article_id,
						campaign_petition
				FROM	campaigns
				WHERE	campaign_deleted = 0
				AND		campaign_status = "future"
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
					'article'=>$row->campaign_article_id,
					'has_been_petitioned'=>$row->campaign_petition
					);
				$result[$row->campaign_id] = $result_item;
			}
		}
		return $result;
	}
	
	/**
	 * Returns an array of the all the campaigns that are going to be published in the future
	 * in ascending order of name.
	 * @return An array of arrays containing campaign id, names, article id, votes, publish date and expired.
	 */
	function GetUnpublishedCampaignList()
	{
		$sql = 'SELECT	campaign_name,
						campaign_votes,
						campaign_id,
						campaign_article_id,
						campaign_petition
				FROM	campaigns
				WHERE	campaign_deleted = 0
				AND		campaign_status = "unpublished"
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
					'article'=>$row->campaign_article_id,
					'has_been_petitioned'=>$row->campaign_petition
					);
				$result[$row->campaign_id] = $result_item;
			}
		}
		return $result;
	}
	
	/**
	 * Returns an array of the all the campaigns that are currently expired
	 * in ascending order of name.
	 * @return An array of arrays containing campaign id, names, article id, votes, publish date and expired.
	 */
	function GetExpiredCampaignList()
	{
		$sql = 'SELECT	campaign_name,
						campaign_votes,
						campaign_id,
						campaign_article_id,
						campaign_petition
				FROM	campaigns
				WHERE	campaign_deleted = 0
				AND		campaign_status = "expired"
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
					'article'=>$row->campaign_article_id,
					'has_been_petitioned'=>$row->campaign_petition
					);
				$result[$row->campaign_id] = $result_item;
			}
		}
		return $result;
	}
	
	function GetLiveCampaignCount()
	{
		$sql = 'SELECT	COUNT(campaign_id) AS live_count
				FROM	campaigns
				WHERE	campaign_deleted = 0
				AND		campaign_status = "live"';
		$query = $this->db->query($sql);
		if ($query->num_rows() == 1)
		$row = $query->row();
		return $row->live_count;
	}
	
	function GetFutureCampaignCount()
	{
		$sql = 'SELECT	COUNT(campaign_id) AS future_count
				FROM	campaigns
				WHERE	campaign_deleted = 0
				AND		campaign_status = "future"';
		$query = $this->db->query($sql);
		if ($query->num_rows() == 1)
		$row = $query->row();
		return $row->future_count;
	}

	function CanStartPetition()
	{
		$this->db->trans_start();
			//find the largest
			$sql = 'SELECT	max( campaign_votes ) AS max_campaign_votes
					FROM	campaigns
					WHERE	campaign_status = "live"';
			$query = $this->db->query($sql);
			$row = $query->row();
			//find campaigns with this value
			$sql = 'SELECT	campaign_id
					FROM	campaigns
					WHERE	campaign_votes = ?
					AND		campaign_deleted = FALSE
					AND		campaign_status = "live"';
			$query = $this->db->query($sql,array($row->max_campaign_votes));
			echo($row->max_campaign_votes);
		$this->db->trans_complete();
		if ($query->num_rows() == 1) //return true if there is only one campaign which has the max no of votes
			return TRUE;
		else
			return FALSE;
	}
	
	/**
	 * Returns name, signatures and article id of the given campaign id
	 * @return the name as a string.
	 */
	function GetPetitionCampaign($campaign_id)
	{
		$sql = 'SELECT	campaign_name,
						campaign_petition_signatures,
						campaign_article_id
				FROM	campaigns
				WHERE	campaign_id = ?';
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
		$sql = 'SELECT	campaign_id
				FROM	campaigns
				WHERE	campaign_petition = 1
				AND		campaign_deleted = 0
				AND		campaign_status = "live"';
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
	 * Returns the id of the current users vote.
	 * @returns the campaign id of the current users vore or FALSE if no vote has been cast.
	 */
	function GetUserVoteSignature($user_id)
	{
		$sql = 'SELECT	campaign_user_campaign_id
				FROM	campaign_users
				WHERE	campaign_user_user_entity_id = ?';
		$query = $this->db->query($sql,array($user_id));
		$row = $query->row();
		if ($query->num_rows() > 0)
			return $row->campaign_user_campaign_id;
		else
			return FALSE;
	}

	/**
	 * Sets the users vote in the vote table.
	 * @returns nothing.
	 */
	function SetUserVote($campaign_id, $user_id)
	{
		$cur_campaign_id = self::GetUserVoteSignature($user_id);
		if ($cur_campaign_id == FALSE)
			self::AddNewVote($campaign_id, $user_id);
		else
			self::UpdateUserVote($cur_campaign_id, $campaign_id, $user_id);
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
			$campaign_id = self::GetUserVoteSignature($user_id);
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

	/**
	 * Sets the users signature in the signature table.
	 * @returns nothing.
	 */
	function SetUserSignature($campaign_id, $user_id)
	{
		$cur_campaign_id = self::GetUserVoteSignature($user_id);
		if ($cur_campaign_id == FALSE)
			self::AddNewSignature($campaign_id, $user_id);
		else
			self::UpdateUserSignature($cur_campaign_id, $campaign_id, $user_id);
	}
	
	/**
	 * Updates the users signature to be for a different campaign.
	 * @returns nothing.
	 */
	function UpdateUserSignature($cur_campaign_id, $campaign_id, $user_id)
	{
		$this->db->trans_start();
			$sql = 'UPDATE	campaign_users
					SET		campaign_user_campaign_id = ?
					WHERE	campaign_user_user_entity_id = ?';
			$this->db->query($sql,array($campaign_id, $user_id));
			$sql = 'UPDATE	campaigns
					SET		campaign_petition_signatures = campaign_petition_signatures + 1
					WHERE	campaign_id = ?';
			$this->db->query($sql,array($campaign_id));
			$sql = 'UPDATE	campaigns
					SET		campaign_petition_signatures = campaign_petition_signatures - 1
					WHERE	campaign_id = ?';
			$this->db->query($sql,array($cur_campaign_id));
		$this->db->trans_complete();
	}

	/**
	 * Inserts a new signature into the database.
	 * @returns nothing.
	 */
	function AddNewSignature($campaign_id, $user_id)
	{
		$this->db->trans_start();
			$sql = 'INSERT INTO campaign_users (
								campaign_user_campaign_id,
								campaign_user_user_entity_id)
					VALUES (?, ?)';
			$this->db->query($sql,array($campaign_id, $user_id));
			$sql = 'UPDATE	campaigns
					SET		campaign_petition_signatures = campaign_petition_signatures + 1
					WHERE	campaign_id = ?';
			$this->db->query($sql,array($campaign_id));
		$this->db->trans_complete();
	}
	
	function GetPetitionID()
	{
		$sql = 'SELECT	campaign_id
				FROM	campaigns
				WHERE	campaign_deleted = FALSE
				AND		campaign_petition = TRUE';
		$query = $this->db->query($sql);
		$row = $query->row();
		if ($query->num_rows() == 1)
			return $row->campaign_id;
		else
			return FALSE;
	}

	/**
	 * Starts the campaign with the most votes, if possible (no two campaigns have the same votes)
	 * @returns the campaign id of the current users vore or FALSE if no vote has been cast.
	 */
	function StartPetition()
	{
		$this->db->trans_start();			
			//find the largest
			$sql = 'SELECT	max( campaign_votes ) AS max_campaign_votes
					FROM	campaigns
					WHERE	campaign_status = "live"';
			$query = $this->db->query($sql);
			$row = $query->row();
			//find campaigns with this value
			$sql = 'SELECT	campaign_id
					FROM	campaigns
					WHERE	campaign_votes = ?
					AND		campaign_deleted = FALSE
					AND		campaign_status = "live"';
			$query = $this->db->query($sql,array($row->max_campaign_votes));
			$row = $query->row();
			if ($query->num_rows() == 1)
			{
				$sql = 'UPDATE	campaigns
						SET		campaign_status = "expired"
						WHERE NOT campaign_id = ?
						AND		campaign_status = "live"';
				$this->db->query($sql,array($row->campaign_id));
				$sql = 'UPDATE	campaigns
						SET		campaign_petition = TRUE,
								campaign_petition_signatures = 0
						WHERE	campaign_id = ?';
				$this->db->query($sql,array($row->campaign_id));
				self::ClearVotes();
				return TRUE;
			}
			else
				return FALSE;
		$this->db->trans_complete();
	}
	
	function EndPetition()
	{
		$this->db->trans_start();
			$sql = 'UPDATE	campaigns
					SET		campaign_status = "expired"
					WHERE 	campaign_status = "live"
					AND		campaign_petition = 1';
			$this->db->query($sql);
			$sql = 'UPDATE	campaigns
					SET		campaign_status = "live",
							campaign_petition = 0
					WHERE 	campaign_status = "future"';
			$this->db->query($sql);
		$this->db->trans_complete();
		return TRUE;
	}

	/**
	 * Removes the users current signature.
	 * @returns nothing.
	 */
	function WithdrawSignature($user_id)
	{
		$this->db->trans_start();
			$campaign_id = self::GetUserVoteSignature($user_id);
			$sql = 'UPDATE	campaigns
					SET		campaign_petition_signatures = campaign_petition_signatures - 1
					WHERE	campaign_id = ?';
			$this->db->query($sql, array($campaign_id));
			$sql = 'DELETE FROM	campaign_users
					WHERE		campaign_user_user_entity_id = ?';
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
	/*function GetCampaignProgressReports2($campaign_id, $count)
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
	}*/
}
?>