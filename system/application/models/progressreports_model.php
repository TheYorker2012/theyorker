<?php
/**
 * This model deals with progress reports.
 *
 * @author Richard Ingle (ri504)
 * 
 */
 
class Progressreports_model extends Model
{
	function ProgressreportsModel()
	{
		//Call the Model Constructor
		parent::Model();
	}
	
	/**
	 * Returns an array of the last 3 progress report items if limit is true otherwise return all for the given campaign/charity id.
	 * @return An array of arrays containing campaign id, names and votes.
	 */
	function GetCharityCampaignProgressReports($id, $limit, $is_charity)
	{
		$sql = 'SELECT	progress_report_articles.progress_report_article_article_id
				FROM	progress_report_articles
				INNER	JOIN articles
				ON		articles.article_id = progress_report_articles.progress_report_article_article_id';
		if ($is_charity)
		{
			$sql = $sql.'	WHERE	progress_report_articles.progress_report_article_charity_id = ?
							AND		progress_report_articles.progress_report_article_campaign_id IS NULL
							AND		progress_report_articles.progress_report_article_deleted = 0';
		}
		else
		{
			$sql = $sql.'	WHERE	progress_report_articles.progress_report_article_campaign_id = ?
							AND		progress_report_articles.progress_report_article_charity_id IS NULL
							AND		progress_report_articles.progress_report_article_deleted = 0';
		}			
		$sql = $sql.' ORDER BY articles.article_publish_date DESC';
		if ($limit)
			$sql = $sql.' LIMIT	0,3';
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
				FROM	progress_report_articles
				INNER JOIN articles
				ON		articles.article_id = progress_report_articles.progress_report_article_article_id';
		if ($is_charity)
		{
			$sql = $sql.'	WHERE	progress_report_articles.progress_report_article_charity_id = ?
							AND		progress_report_articles.progress_report_article_campaign_id IS NULL
							AND		progress_report_articles.progress_report_article_deleted = 0
							AND		articles.article_live_content_id IS NOT NULL';
		}
		else
		{
			$sql = $sql.'	WHERE	progress_report_articles.progress_report_article_campaign_id = ?
							AND		progress_report_articles.progress_report_article_charity_id IS NULL
							AND		progress_report_articles.progress_report_article_deleted = 0
							AND		articles.article_live_content_id IS NOT NULL';
		}
		$query = $this->db->query($sql,array($id));
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->pr_count;
		}
		return FALSE;
	}
	
	/**
	 * Adds a link between a progress report article and its relevant chairty/campaign
	 * @return nothing
	 */
	function AddCharityCampaignProgressReportLink($article_id, $is_charity, $id)
	{
		$sql = 'INSERT INTO	progress_report_articles(
							progress_report_article_article_id,
							progress_report_article_campaign_id,
							progress_report_article_charity_id)
				VALUES		(?,?,?)';
		if ($is_charity == TRUE)
		{
			$this->db->query($sql,array($article_id,NULL,$id));
		}
		else
		{
			$this->db->query($sql,array($article_id,$id,NULL));
		}
		return TRUE;
	}
	
	/**
	 * Adds a link between a progress report article and its relevant chairty/campaign
	 * @return nothing
	 */
	function DeleteCharityCampaignProgressReportLink($article_id, $is_charity, $id)
	{
		$sql = 'UPDATE	progress_report_articles
				SET		progress_report_article_deleted = 1
				WHERE	progress_report_article_article_id = ?';
		if ($is_charity == TRUE)
		{
			$sql = $sql . ' AND progress_report_article_charity_id = ?';
		}
		else
		{
			$sql = $sql . ' AND progress_report_article_campaign_id = ?';
		}
		$this->db->query($sql,array($article_id,$id));
		return TRUE;
	}
}