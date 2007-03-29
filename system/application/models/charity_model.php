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
	 * retrieves a list of all charities.
	 */
	function GetCharities()
	{
		$sql = 'SELECT	charity_name,
				charity_id,
				charity_current
			FROM	charities
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

	/*****************************************************
	*  PROGRESS REPORTS
	*****************************************************/
	
	/**
	 * Returns an array of the last $count progress report items for the given campaign id.
	 * @return An array of arrays containing campaign id, names and votes.
	 */
	function GetCharityProgressReports($charity_id, $count)
	{
		$sql = 'SELECT	progress_report_articles.progress_report_article_article_id
			FROM	progress_report_articles
			INNER	JOIN articles
			ON	articles.article_id = progress_report_articles.progress_report_article_article_id
			WHERE	progress_report_articles.progress_report_article_charity_id = ?
			AND	progress_report_articles.progress_report_article_campaign_id IS NULL
			ORDER BY articles.article_publish_date DESC
			LIMIT	0,?';
		$query = $this->db->query($sql,array($charity_id, $count));
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