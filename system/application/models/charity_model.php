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
		return array('name'=>$row->charity_name,'article'=>$row->charity_article_id,'target_text'=>$row->charity_goal_text,'target'=>$row->charity_goal,'current'=>$row->charity_total);
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
		$sql = 'SELECT 
			progress_report_articles.progress_report_article_article_id
			FROM progress_report_articles
			INNER JOIN articles
			ON articles.article_id = progress_report_articles.progress_report_article_article_id
			WHERE progress_report_articles.progress_report_article_charity_id = ?
			ORDER BY articles.article_publish_date DESC
			LIMIT 0,3';
		$query = $this->db->query($sql,array($charity_id));
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