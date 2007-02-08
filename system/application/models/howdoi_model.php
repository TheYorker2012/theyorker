<?php
/**
 * This model retrieves data for the How Do I pages.
 *
 * @author Richard Ingle (ri504)
 * 
 */
 
//TODO - prevent erros if no data present
 
class Howdoi_model extends Model
{
	function HowdoiModel()
	{
		//Call the Model Constructor
		parent::Model();
	}
	

	/**
	 * Returns an array of the different category types in the how do i section.
	 * @return An array of arrays containing id, codename and name in the specified order.
	 */
	function GetContentCategories($parent_id)
	{
		$sql = 'SELECT content_type_id,
				content_type_codename,
				content_type_name,
                                content_type_blurb
			FROM content_types
			WHERE content_type_parent_content_type_id = ?
			ORDER BY content_type_section_order';
		$query = $this->db->query($sql,array($parent_id));
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result[$row->content_type_id] = array(
					//'id'=>$row->content_type_id,
					'codename'=>$row->content_type_codename,
					'name'=>$row->content_type_name,
					'blurb'=>$row->content_type_blurb
					);
			}
		}
		return $result;
	}

	/**
	 * Returns an array of the article ids for the category.
	 * Doesn't return unpublished articles
	 * @return An array of article ids.
	 */
	function GetCategoryArticleIDs($content_type_id)
	{
		$sql = 'SELECT article_id
			FROM articles
			INNER JOIN article_contents
			ON article_content_id = article_id
			WHERE article_content_type_id = ? AND
				article_live_content_id IS NOT NULL
			ORDER BY article_content_heading ASC';
		$query = $this->db->query($sql,array($content_type_id));
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result[] = $row->article_id;
			}
		}
		return $result;
	}

	/**
	 * Returns an array of the article ids for the category.
	 * Returns all articles
	 * @return An array of article ids.
	 */
	function GetOfficeCategoryArticleIDs($content_type_id)
	{
		$sql = 'SELECT article_id
			FROM articles
			INNER JOIN article_contents
			ON article_content_id = article_id
			WHERE article_content_type_id = ?
			ORDER BY article_content_heading ASC';
		$query = $this->db->query($sql,array($content_type_id));
		$result = array();
		if ($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
				$result[$row->article_id] = array();
			}
		}
		return $result;
	}
}