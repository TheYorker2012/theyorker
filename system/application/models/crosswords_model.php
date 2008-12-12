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

	/*
	 * Crosswords
	 */
}

?>
