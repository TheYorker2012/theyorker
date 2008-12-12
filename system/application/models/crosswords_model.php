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

	/*
	 * Crossword categories
	 */

	/*
	 * Crosswords
	 */
}

?>
