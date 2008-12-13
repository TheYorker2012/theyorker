<?php

/// Crosswords data model
class Crosswords_model extends model
{
	function __construct()
	{
		parent::model();
		$this->load->helper('crosswords');
	}

	/*
	 * Tips
	 */

	/*
	 * Layouts
	 */

	/** Get information about all layouts.
	 * @return array Array of layouts indexed by id.
	 *  - 'id'
	 *  - 'name'
	 *  - 'description'
	 */
	function GetAllLayouts()
	{
		$sql =	'SELECT '.
				'	`crossword_layout_id`			AS id,'.
				'	`crossword_layout_name`			AS name,'.
				'	`crossword_layout_description`	AS description'.
				' FROM `crossword_layouts`'.
				' ORDER BY `crossword_layout_name` ASC';
		$data = $this->db->query($sql)->result_array();
		// Reindex
		$results = array();
		foreach ($data as $datum) {
			$datum['id'] = (int)$datum['id'];
			$results[$datum['id']] = $datum;
		}
		return $results;
	}

	/** Get information about a layout.
	 * @param $id Crossword layout id.
	 * @return array Array representing the layout or null.
	 *  - 'id'
	 *  - 'name'
	 *  - 'description'
	 */
	function GetLayoutById($id)
	{
		$sql =	'SELECT '.
				'	`crossword_layout_id`			AS id,'.
				'	`crossword_layout_name`			AS name,'.
				'	`crossword_layout_description`	AS description'.
				' FROM `crossword_layouts`'.
				' WHERE `crossword_layout_id` = ?';
		$data = $this->db->query($sql, array($id))->result_array();
		if (count($data) < 1) {
			return null;
		}
		return $data[0];
	}

	/** Add a new crossword layout to the database.
	 * @param $Layout array with:
	 *  - 'name'
	 *  - 'description'
	 * @return Messages structure
	 */
	function AddLayout($Layout)
	{
		// Perform the query
		$sql =	'INSERT IGNORE INTO `crossword_layouts` ('.
				'	`crossword_layout_name`,'.
				'	`crossword_layout_description`'.
				') VALUES (?,?)';
		$bind = array($Layout['name'], $Layout['description']);
		$this->db->query($sql, $bind);
		// Find what happened
		$messages = array();
		if ($this->db->affected_rows() > 0) {
			$messages['success'][] = 'Layout successfully added.';
		}
		else {
			$messages['error'][] = 'Layout could not be added. The name '.xml_escape('"'.$Layout['name'].'"').' is probably already used by another layout.';
		}
		return $messages;
	}

	/** Modify an existing layout to the database.
	 * @param $Id Crossword layout id.
	 * @param $Layout array with:
	 *  - 'name'
	 *  - 'description'
	 * @return Messages structure
	 */
	function ModifyLayout($Id, $Layout)
	{
		// Perform the query
		$sql =	'UPDATE IGNORE `crossword_layouts` '.
				'SET	`crossword_layout_name` = ?,'.
				'		`crossword_layout_description` = ? '.
				'WHERE `crossword_layout_id` = ? ';
		$bind = array($Layout['name'], $Layout['description'], $Id);
		$this->db->query($sql, $bind);
		// Find what happened
		$messages = array();
		if ($this->db->affected_rows() > 0) {
			$messages['success'][] = 'Layout successfully modified.';
		}
		else {
			$messages['error'][] = 'Layout was not modified. The name '.xml_escape('"'.$Layout['name'].'"').' is probably already used by another layout.';
		}
		return $messages;
	}

	/*
	 * Crossword categories
	 */

	/** Get information about all categories.
	 * @return array Array of categories indexed by id.
	 *  - 'id'
	 *  - 'name'
	 *  - 'short_name'
	 */
	function GetAllCategories()
	{
		$sql =	'SELECT '.
				'	`crossword_category_id`							AS id,'.
				'	`crossword_category_name`						AS name,'.
				'	`crossword_category_short_name`					AS short_name,'.
				'	`crossword_category_default_width`				AS default_width,'.
				'	`crossword_category_default_height`				AS default_height,'.
				'	`crossword_category_default_layout_id`			AS default_layout_id,'.
				'	`crossword_category_default_has_normal_clues`	AS default_has_normal_clues,'.
				'	`crossword_category_default_has_cryptic_clues`	AS default_has_cryptic_clues,'.
				'	`crossword_category_default_winners`			AS default_winners'.
				' FROM `crossword_categories`'.
				' ORDER BY `crossword_category_name` ASC';
		$data = $this->db->query($sql)->result_array();
		// Reindex
		$results = array();
		foreach ($data as $datum) {
			$datum['id'] = (int)$datum['id'];
			$results[$datum['id']] = $datum;
		}
		return $results;
	}

	/** Get information about a category.
	 * @param $id Crossword category id.
	 * @return array Array representing the category or null.
	 *  - 'id'
	 *  - 'name'
	 *  - 'short_name'
	 *  - 'default_width'
	 *  - 'default_height'
	 *  - 'default_layout_id'
	 *  - 'default_has_normal_clues'
	 *  - 'default_has_cryptic_clues'
	 *  - 'default_winners'
	 */
	function GetCategoryById($id)
	{
		$sql =	'SELECT '.
				'	`crossword_category_id`							AS id,'.
				'	`crossword_category_name`						AS name,'.
				'	`crossword_category_short_name`					AS short_name,'.
				'	`crossword_category_default_width`				AS default_width,'.
				'	`crossword_category_default_height`				AS default_height,'.
				'	`crossword_category_default_layout_id`			AS default_layout_id,'.
				'	`crossword_category_default_has_normal_clues`	AS default_has_normal_clues,'.
				'	`crossword_category_default_has_cryptic_clues`	AS default_has_cryptic_clues,'.
				'	`crossword_category_default_winners`			AS default_winners'.
				' FROM `crossword_categories`'.
				' WHERE `crossword_category_id` = ?';
		$data = $this->db->query($sql, array($id))->result_array();
		if (count($data) < 1) {
			return null;
		}
		return $data[0];
	}

	/** Add a new crossword category to the database.
	 * @param $Category array with:
	 *  - 'name'
	 *  - 'short_name'
	 *  - 'default_width'
	 *  - 'default_height'
	 *  - 'default_layout_id'
	 *  - 'default_has_normal_clues'
	 *  - 'default_has_cryptic_clues'
	 *  - 'default_winners'
	 * @return Messages structure
	 */
	function AddCategory($Category)
	{
		// Perform the query
		$sql =	'INSERT IGNORE INTO `crossword_categories` ('.
				'	`crossword_category_name`,'.
				'	`crossword_category_short_name`,'.
				'	`crossword_category_default_width`,'.
				'	`crossword_category_default_height`,'.
				'	`crossword_category_default_layout_id`,'.
				'	`crossword_category_default_has_normal_clues`,'.
				'	`crossword_category_default_has_cryptic_clues`,'.
				'	`crossword_category_default_winners`'.
				') VALUES (?,?,?,?,?,?,?,?)';
		$bind = array(
			$Category['name'],
			$Category['short_name'],
			$Category['default_width'],
			$Category['default_height'],
			$Category['default_layout_id'],
			$Category['default_has_normal_clues'],
			$Category['default_has_cryptic_clues'],
			$Category['default_winners'],
		);
		$this->db->query($sql, $bind);
		// Find what happened
		$messages = array();
		if ($this->db->affected_rows() > 0) {
			$messages['success'][] = 'Category successfully added.';
		}
		else {
			$messages['error'][] = 'Category could not be added. The short name '.xml_escape('"'.$Category['short_name'].'"').' is probably already used by another category.';
		}
		return $messages;
	}

	/** Modify an existing category to the database.
	 * @param $Id Crossword category id.
	 * @param $Category array with:
	 *  - 'name'
	 *  - 'short_name'
	 *  - 'default_width'
	 *  - 'default_height'
	 *  - 'default_layout_id'
	 *  - 'default_has_normal_clues'
	 *  - 'default_has_cryptic_clues'
	 *  - 'default_winners'
	 * @return Messages structure
	 */
	function ModifyCategory($Id, $Category)
	{
		// Perform the query
		$sql =	'UPDATE IGNORE `crossword_categories` '.
				'SET	`crossword_category_name` = ?,'.
				'		`crossword_category_short_name` = ?,'.
				'		`crossword_category_default_width` = ?,'.
				'		`crossword_category_default_height` = ?,'.
				'		`crossword_category_default_layout_id` = ?,'.
				'		`crossword_category_default_has_normal_clues` = ?,'.
				'		`crossword_category_default_has_cryptic_clues` = ?,'.
				'		`crossword_category_default_winners` = ? '.
				'WHERE `crossword_category_id` = ? ';
		$bind = array(
			$Category['name'],
			$Category['short_name'],
			$Category['default_width'],
			$Category['default_height'],
			$Category['default_layout_id'],
			$Category['default_has_normal_clues'],
			$Category['default_has_cryptic_clues'],
			$Category['default_winners'],
			$Id
		);
		$this->db->query($sql, $bind);
		// Find what happened
		$messages = array();
		if ($this->db->affected_rows() > 0) {
			$messages['success'][] = 'Category successfully modified.';
		}
		else {
			$messages['error'][] = 'Category was not modified. The short name '.xml_escape('"'.$Category['short_name'].'"').' is probably already used by another category.';
		}
		return $messages;
	}

	/*
	 * Crosswords
	 */
}

?>
