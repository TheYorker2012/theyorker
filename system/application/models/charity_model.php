<?php
/**
 * This model retrieves data for the Campaign pages.
 *
 * @author Richard Ingle (ri504)
 * 
 */
 
//TODO - prevent erros if no data present
 
class Charity_model extends Model
{
	function CharityModel()
	{
		//Call the Model Constructor
		parent::Model();
	}
	
        /**
	 * blah.
	 */
	function GetCharity($charity_id)
	{
		$sql = 'SELECT charity_name, charity_article_id, charity_goal_text, charity_goal, charity_total
			FROM charities
			WHERE charity_id = ?';
		$query = $this->db->query($sql,array($charity_id));
		$row = $query->row();
		return array(
			'name'=>$row->charity_name,
			'article'=>$row->charity_article_id,
			'target_text'=>$row->charity_goal_text,
			'target'=>$row->charity_goal,
			'current'=>$row->charity_total);
	}
	
        /**
	 * retrieves the current charities id.
	 * @return the id of the current charity or false otherwise
	 */
	function GetCurrentCharity()
	{
		$sql = 'SELECT	charity_id
			FROM	charities
			WHERE	charity_current = 1
			AND	charity_deleted = 0
			LIMIT 0,1';
		$query = $this->db->query($sql);
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->charity_id;
		}
		return false;
	}
	
        /**
	 * retrieves a list of all charities.
	 */
	function GetCharities()
	{
		$sql = 'SELECT	charity_name,
				charity_id,
				charity_current
			FROM	charities
			WHERE	charity_deleted = 0
			ORDER BY charity_name ASC';
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result[] = array(
					'id'=>$row->charity_id,
					'name'=>$row->charity_name,
					'iscurrent'=>$row->charity_current
					);
			}
		}
		return $result;
	}

        /**
	 * Adds a new charity to the database.
	 * @param $name the name of the charity to add
	 */
	function CreateCharity($name, $article_id)
	{
		$sql = 'INSERT INTO charities (
				charity_name,
				charity_article_id,
				charity_current)
			VALUES	(?, ?, FALSE)';
		$this->db->query($sql,array($name, $article_id));
	}

        /**
	 * Updates the given charity.
	 * @param $id the id of the charity
	 * @param $name the name of the charity
	 * @param $goal the target goal ammount
	 * @param $goaltext a description of what the charity is aiming for
	 */
	function UpdateCharity($id, $name, $goal, $goaltext)
	{
		$sql = 'UPDATE 	charities
			SET	charity_name = ?,
				charity_goal = ?,
				charity_goal_text = ?
			WHERE	charity_id = ?';
		$this->db->query($sql,array($name, $goal, $goaltext, $id));
	}

        /**
	 * Sets the charity with id to the current one.
	 * @param $id the id of the charity
	 */
	function SetCharityCurrent($id)
	{
		$this->db->trans_start();
		//see if charity has a published article
		$sql = 'SELECT 	articles.article_live_content_id
			FROM	charities
			JOIN	articles
			ON	articles.article_id = charities.charity_article_id
			WHERE	charities.charity_id = ?
			AND	articles.article_live_content_id IS NOT NULL';
		$query = $this->db->query($sql,array($id));
		if ($query->num_rows() == 1)
		{
			//set all charities to non current
			$sql = 'UPDATE 	charities
				SET	charity_current = 0';
			$this->db->query($sql);
			//set the new current charity
			$sql = 'UPDATE 	charities
				SET	charity_current = 1
				WHERE	charity_id = ?';
			$this->db->query($sql,array($id));
			$this->db->trans_complete();
			return true;
		}
		else
		{
			return false;
		}
	}

        /**
	 * Deletes the charity with id.
	 * @param $id the id of the charity
	 */
	function DeleteCharity($id)
	{
		//set the charity to deleted
		$sql = 'UPDATE 	charities
			SET	charity_deleted = 1,
				charity_current = 0
			WHERE	charity_id = ?';
		$this->db->query($sql,array($id));
		$this->db->trans_complete();
	}

	/*****************************************************
	*  PROGRESS REPORTS
	*****************************************************/
	
	/**
	 * Returns an array of the last 3 progress report items if limit is true otherwise return all for the given campaign/charity id.
	 * @return An array of arrays containing campaign id, names and votes.
	 */
	function GetCharityCampaignProgressReports($id, $limit, $is_charity)
	{
		$sql = 'SELECT	progress_report_articles.progress_report_article_article_id
			FROM	progress_report_articles
			INNER	JOIN articles
			ON	articles.article_id = progress_report_articles.progress_report_article_article_id';
		if ($is_charity)
		{
			$sql = $sql.' WHERE	progress_report_articles.progress_report_article_charity_id = ?
				AND	progress_report_articles.progress_report_article_campaign_id IS NULL';
		}
		else
		{
			$sql = $sql.' WHERE	progress_report_articles.progress_report_article_campaign_id = ?
				AND	progress_report_articles.progress_report_article_charity_id IS NULL';
		}			
		$sql = $sql.' ORDER BY articles.article_publish_date DESC
			';
		if ($limit)
			$sql = $sql.'LIMIT	0,3';
		$query = $this->db->query($sql,array($id));
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
	
	/**
	 * Returns the total number of progress reports relating to the given charity
	 * @return an int specifying charity count or false on error.
	 */
	function GetCharityCampaignProgressReportCount($id, $is_charity)
	{
		$sql = 'SELECT	count(*) as pr_count
			FROM	progress_report_articles';
		if ($is_charity)
		{
			$sql = $sql.' WHERE	progress_report_articles.progress_report_article_charity_id = ?
				AND	progress_report_articles.progress_report_article_campaign_id IS NULL';
		}
		else
		{
			$sql = $sql.' WHERE	progress_report_articles.progress_report_article_campaign_id = ?
				AND	progress_report_articles.progress_report_article_charity_id IS NULL';
		}
		$query = $this->db->query($sql,array($id));
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->pr_count;
		}
		return false;
	}
}