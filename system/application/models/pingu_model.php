<?php
/**
 * This model should add articles to the database. NOT yet complete.
 *
 * @author Richard Ingle (ri504)
 *
 */
class Pingu_model extends Model
{
	function PinguArticleModel()
	{
		//Call the Model Constructor
		parent::Model();
	}

	/*****************************************************
	*  PROGRESS REPORT ARTICLES
	*****************************************************/

	/**
	 * Inserts a new progress report article into the database.
	 */
	function InsertProgressReportArticle($article_id, $campaign_id)
	{
		$sql = 'INSERT INTO progress_report_articles (
				progress_report_article_article_id,
				progress_report_article_campaign_id)
			VALUES (?, ?)';
		$this->db->query($sql, array($article_id, $campaign_id));
	}

	/**
	 * Deletes an existing progress report article from the database.
	 */
	function DeleteProgressReportArticle($article_id, $campaign_id)
	{
		$sql = 'DELETE FROM progress_report_articles
			WHERE progress_report_article_article_id = ? AND
				progress_report_article_campaign_id = ?';
		$this->db->query($sql, array($article_id, $campaign_id));
	}
	
	/*****************************************************
	*  CONTENT TYPES
	*****************************************************/

	/**
	 * Inserts a new content type into the database.
	 */
	function InsertContentType($codename, $parent_id, $name, $archive, $blurb, $has_reviews, $section)
	{
		$sql = 'INSERT INTO content_types (
				content_type_codename,
				content_type_parent_content_type_id,
				content_type_name,
				content_type_archive,
				content_type_blurb,
				content_type_has_reviews,
				content_type_section)
			VALUES (?, ?, ?, ?, ?, ?, ?)';
		$this->db->query($sql, array($codename,$parent_id,$name,$archive,$blurb,$has_reviews,$section));
	}

	/**
	 * Inserts a new content type into the database.
	 */
	function UpdateContentType($id, $codename, $parent_id, $name, $archive, $blurb, $has_reviews, $section)
	{
		$sql = 'UPDATE content_types
			SET content_type_codename = ?,
				content_type_parent_content_type_id = ?,
				content_type_name = ?,
				content_type_archive = ?,
				content_type_blurb = ?,
				content_type_has_reviews = ?,
				content_type_section = ?
			WHERE content_type_id = ?';
		$this->db->query($sql, array($codename,$parent_id,$name,$archive,$blurb,$has_reviews,$section,$id));
	}

        /**
	 * Gets the content types from the database which have the given parent id.
	 * @return
	 */
	function GetContentTypes($parent_id, $is_null)
	{
		if ($is_null == TRUE)
		{
		$sql = 'SELECT content_type_id, content_type_codename, content_type_name
			FROM content_types
			WHERE content_type_parent_content_type_id is NULL
			ORDER BY content_type_name ASC';
		}
		else
		{
		$sql = 'SELECT content_type_id, content_type_codename, content_type_name
			FROM content_types
			WHERE content_type_parent_content_type_id = '.$parent_id.'
			ORDER BY content_type_name ASC';
		}
		$query = $this->db->query($sql);
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result[$row->content_type_id] = array('name'=>$row->content_type_name,'codename'=>$row->content_type_codename);
			}
		}
		return $result;
		
	}
}
?>