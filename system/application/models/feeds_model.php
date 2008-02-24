<?php

/**
 *	@brief		Model for retrieving data about RSS feeds
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Feeds_model extends Model
{

	function __construct()
	{
		parent::Model();
	}

	function getArticleTypeFeeds ($parent_type = NULL)
	{
		$feeds = array();
		$params = ($parent_type === NULL) ? array() : array($parent_type);
		$sql = 'SELECT	content_types.content_type_id,
						content_types.content_type_name,
						content_types.content_type_codename,
						content_types.content_type_has_children
				FROM	content_types';
		if ($parent_type !== NULL) {
			$sql .= ', content_types AS parent_type';
		}
		$sql .= ' WHERE content_types.content_type_shelved = 0
				AND content_types.content_type_section != "hardcoded" ';
		if ($parent_type === NULL) {
			$sql .= 'AND content_types.content_type_parent_content_type_id IS NULL ';
		} else {
			$sql .= 'AND content_types.content_type_parent_content_type_id = parent_type.content_type_id
					AND parent_type.content_type_codename = ? ';
		}
		$sql .= 'ORDER BY content_types.content_type_section_order ASC';
		$query = $this->db->query($sql, $params);
		if ($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$children = ($row->content_type_has_children) ? $this->getArticleTypeFeeds($row->content_type_codename) : array();
				$feeds[] = array($row->content_type_name, $row->content_type_codename, $children);
			}
		}
		return $feeds;
	}

}
?>
