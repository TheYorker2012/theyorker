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
	 * Returns the content id of the how do i section.
	 * @return An array of arrays containing id, codename and name in the specified order.
	 */
	function GetHowdoiTypeID()
	{
		$sql = 'SELECT content_type_id
			FROM content_types
			WHERE content_type_codename = "howdoi"';
		$query = $this->db->query($sql);
		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			return $row->content_type_id;
		}
		else
			return FALSE;
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
	 * Returns an array of the different categories in the how do i section.
	 * @return An array of arrays containing ids, codename and name in the specified order.
	 */
	function GetCategoryNames($parent_id)
	{
		$sql = 'SELECT content_type_id,
				content_type_codename,
				content_type_name,
				content_type_section_order
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
					'section_order'=>$row->content_type_section_order
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
		/*
		$sql = 'SELECT article_id
			FROM articles
			INNER JOIN article_contents
			ON article_content_id = article_id
			WHERE article_content_type_id = ? AND
				article_live_content_id IS NOT NULL
			ORDER BY article_content_heading ASC';
		*/
		$sql = 'SELECT	article_id
			FROM	articles
			
			JOIN	article_contents
			ON	article_contents.article_content_id = articles.article_live_content_id
			
			WHERE	article_suggestion_accepted = 1
			AND	article_content_type_id = ?
			AND	article_live_content_id IS NOT NULL
			AND	article_deleted = 0
			AND	article_pulled = 0
			ORDER BY article_contents.article_content_heading';
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

	/**
	 * Returns an array of the different category types in the how do i section.
	 * @return An array of arrays containing id, codename and name in the specified order.
	 */
	function GetContentCategory($category_id)
	{
		$sql = 'SELECT content_type_codename,
				content_type_name,
                                content_type_blurb
			FROM content_types
			WHERE content_type_id = ?';
		$query = $this->db->query($sql,array($category_id));
		$result = array();
		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$result = array('codename'=>$row->content_type_codename,
					'name'=>$row->content_type_name,
					'blurb'=>$row->content_type_blurb
					);
		}
		return $result;
	}
	
	function DeleteCategory($category_id)
	{
		$howdoi_type_id = self::GetHowdoiTypeID();

		$this->db->trans_start();

		$sql = 'SELECT	content_type_section_order
			FROM	content_types
			WHERE	content_type_id = ?';
		$query = $this->db->query($sql,array($category_id));
		$row = $query->row();
		$delete_section_order = $row->content_type_section_order;

		$sql = 'SELECT	MAX(content_type_section_order) as max_section_order
			FROM	content_types
			WHERE	content_type_parent_content_type_id = ?';
		$query = $this->db->query($sql,array($howdoi_type_id));
		$row = $query->row();
		$max_section_order = $row->max_section_order;

		for($i = $delete_section_order; $i < $max_section_order; $i++)
		{
			self::SwapCategoryOrder($i, $i + 1);
		}
		
		//update questions to have parent content type id

		$sql = 'DELETE FROM content_types
			WHERE	content_type_id = ?';
		$query = $this->db->query($sql,array($category_id));

		$this->db->trans_complete();
	}

	function UpdateCategory($category_id, $name, $codename, $blurb)
	{
		$sql = 'UPDATE	content_types
			SET	content_type_name = ?,
				content_type_codename = ?,
				content_type_blurb = ?
			WHERE	content_type_id = ?';
		$query = $this->db->query($sql,array($name, $codename, $blurb, $category_id));
	}

	function AddNewCategory($name, $section = 'hardcoded')
	{
		$howdoi_type_id = self::GetHowdoiTypeID();

		$sql = 'SELECT	MAX(content_type_section_order) as max_section_order
			FROM	content_types
			WHERE	content_type_parent_content_type_id = ?';
		$query = $this->db->query($sql,array($howdoi_type_id));
		$row = $query->row();
		$section_order = $row->max_section_order + 1;
		$codename = strtolower(ereg_replace("[^A-Za-z0-9]", "", $name));
		$sql = 'INSERT INTO content_types (
				content_type_parent_content_type_id,
				content_type_name,
				content_type_codename,
				content_type_section,
				content_type_section_order)
			VALUES (?, ?, ?, ?, ?)';
		$query = $this->db->query($sql,array($howdoi_type_id, $name, $codename, $section, $section_order));
	}

	function SwapCategoryOrder($category_id_1, $category_id_2)
	{
		$this->db->trans_start();
		$sql = 'SELECT	content_type_id
			FROM	content_types
			WHERE	content_type_section_order = ?
			AND	content_type_parent_content_type_id = ?';
		$query = $this->db->query($sql,array($category_id_1, self::GetHowdoiTypeID()));
		$row = $query->row();
		$content_type_id_1 = $row->content_type_id;

		$sql = 'SELECT	content_type_id
			FROM	content_types
			WHERE	content_type_section_order = ?
			AND	content_type_parent_content_type_id = ?';
		$query = $this->db->query($sql,array($category_id_2, self::GetHowdoiTypeID()));
		$row = $query->row();
		$content_type_id_2 = $row->content_type_id;

		$sql = 'UPDATE	content_types
			SET	content_type_section_order = ?
			WHERE	content_type_section_order = ?
			AND	content_type_id = ?';
		$query = $this->db->query($sql,array($category_id_2, $category_id_1, $content_type_id_1));

		$sql = 'UPDATE	content_types
			SET	content_type_section_order = ?
			WHERE	content_type_section_order = ?
			AND	content_type_id = ?';
		$query = $this->db->query($sql,array($category_id_1, $category_id_2, $content_type_id_2));
		$this->db->trans_complete();
	}
}