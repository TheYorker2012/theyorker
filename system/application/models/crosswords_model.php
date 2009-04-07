<?php

/// Crosswords data model
class Crosswords_model extends model
{
	// Snippets of useful sql
	private $overdue_sql;
	private $published_sql;
	private $scheduled_sql;
	private $expired_sql;
	private $winner_count_sql;

	function __construct()
	{
		parent::model();
		$this->load->helper('crosswords');

		// Snippets of useful sql
		$this->overdue_sql		= '(`crossword_deadline` IS NOT NULL '.
									'AND `crossword_deadline`    <= NOW())';
		$this->published_sql	= '(`crossword_publication` IS NOT NULL '.
									'AND `crossword_publication` <= NOW() '.
									'AND `crossword_completeness` = 100)';
		$this->scheduled_sql	= '(`crossword_publication` IS NOT NULL '.
									'AND NOT '.$this->published_sql.')';
		$this->winner_count_sql	= '(SELECT COUNT(*) '.
									'FROM `crossword_winners` '.
									'WHERE `crossword_winner_crossword_id` = `crossword_id`)';
		$this->expired_sql		= '((`crossword_expiry` IS NOT NULL AND `crossword_expiry` <= NOW()) '.
									'OR	'.$this->winner_count_sql.' >= `crossword_winners`)';
	}

	/*
	 * Tips
	 */

	/** Get tip categories.
	 * @param $category_id int,null category id.
	 * @param array[array('id'=>,'name'=>,'description'=>)]
	 */
	function GetTipCategories($category_id = null)
	{
		// Construct query
		$sql =	'SELECT '.
				'	`crossword_tip_category_id`				AS id,'.
				'	`crossword_tip_category_name`			AS name,'.
				'	`crossword_tip_category_description`	AS description '.
				'FROM `crossword_tip_categories` ';
		$conditions = array();
		$bind = array();
		if (null !== $category_id) {
			$conditions[] = '`crossword_tip_category_id`=?';
			$bind[] = $category_id;
		}
		if (!empty($conditions)) {
			$sql .= 'WHERE '.join(' AND ', $conditions).' ';
		}
		// Execute query
		$results = $this->db->query($sql, $bind)->result_array();
		return $results;
	}

	/** Get tips.
	 * @param $category_id int,null category id.
	 * @param $crossword_id int,null crossword id.
	 * @return array[array('id'=>,'category_id'=>,'crossword_id'=>,'content_wikitext'=>,'content_xml'=>,'published'=>,'publication'=>)]
	 */
	function GetTips($category_id = null, $crossword_id = null, $tip_id = null, $published = null)
	{
		// Construct query
		$sql =	'SELECT '.
				'	`crossword_tip_id`					AS id,'.
				'	`crossword_tip_category_id`			AS category_id,'.
				'	`crossword_tip_crossword_id`		AS crossword_id,'.
				'	`crossword_tip_content_wikitext`	AS content_wikitext,'.
				'	`crossword_tip_content_xml`			AS content_xml, '.
				'	'.$this->published_sql.'			AS published, '.
				'	UNIX_TIMESTAMP(`crossword_publication`)	AS publication '.
				'FROM		`crossword_tips` '.
				'INNER JOIN	`crosswords` '.
				'	ON		`crossword_id`=`crossword_tip_crossword_id` ';
		$conditions = array();
		$bind = array();
		if (null !== $category_id) {
			$conditions[] = '`crossword_tip_category_id`=?';
			$bind[] = $category_id;
		}
		if (null !== $crossword_id) {
			$conditions[] = '`crossword_tip_crossword_id`=?';
			$bind[] = $crossword_id;
		}
		if (null !== $tip_id) {
			$conditions[] = '`crossword_tip_id`=?';
			$bind[] = $tip_id;
		}
		if (null !== $published) {
			$conditions[] = ($published?'':'NOT ').$this->published_sql;
		}
		if (!empty($conditions)) {
			$sql .= 'WHERE '.join(' AND ', $conditions).' ';
		}
		$sql .= 'ORDER BY `crossword_publication` ASC';
		// Execute query
		$results = $this->db->query($sql, $bind)->result_array();
		return $results;
	}

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

	/** Get information about a category.
	 * @param $short_name Crossword category short name.
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
	function GetCategoryByShortName($short_name)
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
				' WHERE `crossword_category_short_name` = ?';
		$data = $this->db->query($sql, array($short_name))->result_array();
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

	/** Add a new crossword to a category.
	 * @param $category_id int Crossword category id.
	 * @return int,null Crossword id or null on failure.
	 */
	function AddCrossword($category_id)
	{
		// Copy defaults from category
		// This does a convenient check that the category exists
		$sql =	'INSERT INTO `crosswords` ('.
				'	`crossword_width`, '.
				'	`crossword_height`, '.
				'	`crossword_has_normal_clues`, '.
				'	`crossword_has_cryptic_clues`, '.
				'	`crossword_category_id`, '.
				'	`crossword_layout_id`, '.
				'	`crossword_winners` '.
				') '.
				'SELECT '.
				'	`crossword_category_default_width`, '.
				'	`crossword_category_default_height`, '.
				'	`crossword_category_default_has_normal_clues`, '.
				'	`crossword_category_default_has_cryptic_clues`, '.
				'	`crossword_category_id`, '.
				'	`crossword_category_default_layout_id`, '.
				'	`crossword_category_default_winners` '.
				'FROM `crossword_categories` '.
				'WHERE	`crossword_category_id` = ? ';
		$bind = array($category_id);
		$this->db->query($sql, $bind);
		if ($this->db->affected_rows() < 1) {
			return null;
		}
		else {
			return $this->db->insert_id();
		}
	}

	/** Get crosswords.
	 * @param $crossword_id int,null Crossword id.
	 * @param $category_id int,null Category id.
	 * @param $overdue bool,null Overdue or not.
	 * @param $published bool,null Published or not.
	 * @param $expired bool,null Expired or not.
	 * @param $count int,null Maximum crosswords to get.
	 * @param $order 'ASC','DESC' Ordering.
	 */
	function GetCrosswords($crossword_id = null, $category_id = null,
				$overdue = null, $scheduled = null, $published = null, $expired = null,
				$limit = null, $order = 'DESC')
	{
		$sql = 	'SELECT '.
				'`crossword_id`					AS id, '.
				'`crossword_width`				AS width, '.
				'`crossword_height`				AS height, '.
				'`crossword_has_normal_clues`	AS has_quick_clues, '.
				'`crossword_has_cryptic_clues`	AS has_cryptic_clues, '.
				'`crossword_completeness`		AS completeness, '.
				'`crossword_category_id`		AS category_id, '.
				'`crossword_layout_id`			AS layout_id, '.
				'UNIX_TIMESTAMP(`crossword_deadline`)		AS deadline, '.
				'UNIX_TIMESTAMP(`crossword_publication`)	AS publication, '.
				'UNIX_TIMESTAMP(`crossword_expiry`)			AS expiry, '.
				'`crossword_winners`			AS winners, '.
				'`crossword_public_comment_thread_id`	AS public_thread_id, '.
				$this->overdue_sql.		' AS overdue, '.
				$this->scheduled_sql.	' AS scheduled, '.
				$this->published_sql.	' AS published, '.
				$this->expired_sql.		' AS expired, '.
				$this->winner_count_sql.' AS winners_so_far '.
				'FROM crosswords ';

		$bind = array();
		$conditions = array();
		if (null !== $crossword_id) {
			$conditions[] = '`crossword_id`=?';
			$bind[] = $crossword_id;
		}
		if (null !== $category_id) {
			$conditions[] = '`crossword_category_id`=?';
			$bind[] = $category_id;
		}
		if (null !== $overdue) {
			$conditions[] = ($overdue ? $this->overdue_sql : 'NOT '.$this->overdue_sql.'');
		}
		if (null !== $scheduled) {
			$conditions[] = ($scheduled ? $this->scheduled_sql : 'NOT '.$this->scheduled_sql.'');
		}
		if (null !== $published) {
			$conditions[] = ($published ? $this->published_sql : 'NOT '.$this->published_sql.'');
		}
		if (null !== $expired) {
			$conditions[] = ($expired ? $this->expired_sql : 'NOT '.$this->expired_sql.'');
		}
		if (count($conditions) > 0) {
			$sql .= 'WHERE ('.join(') AND (', $conditions).') ';
		}

		$sql .= 'ORDER BY `crossword_publication` '.$order.' ';
		if (null !== $limit) {
			$sql .= 'LIMIT 0,'.(int)$limit;
		}

		$results = $this->db->query($sql, $bind)->result_array();
		foreach ($results as &$result) {
			$result['authors'] = array();
		}
		return $results;
	}

	/** Update a crossword as obtained by GetCrosswords.
	 * @param $crossword array as returned as an element from @a GetCrosswords.
	 * @return bool true on success
	 */
	function UpdateCrossword($crossword)
	{
		if (!isset($crossword['id'])) {
			return false;
		}
		static $field_mapping = array(
			'has_quick_clues'	=> 'crossword_has_normal_clues',
			'has_cryptic_clues'	=> 'crossword_has_cryptic_clues',
			'completeness'		=> 'crossword_completeness',
			'category_id'		=> 'crossword_category_id',
			'layout_id'			=> 'crossword_layout_id',
			'deadline'			=> 'crossword_deadline',
			'publication'		=> 'crossword_publication',
			'expiry'			=> 'crossword_expiry',
			'winners'			=> 'crossword_winners',
		);
		static $sql_mappings = array(
			'deadline'		=> 'FROM_UNIXTIME',
			'publication'	=> 'FROM_UNIXTIME',
			'expiry'		=> 'FROM_UNIXTIME',
		);
		$settings = array();
		$bind = array();
		foreach ($field_mapping as $field => $col) {
			if (array_key_exists($field, $crossword)) {
				if (isset($sql_mappings[$field])) {
					$settings[] = "`$col`=".$sql_mappings[$field].'(?)';
				}
				else {
					$settings[] = "`$col`=?";
				}
				$bind[] = $crossword[$field];
			}
		}
		if (count($settings) > 0) {
			$sql =	'UPDATE	`crosswords` '.
					'SET	'.join(',',$settings).' '.
					'WHERE	`crossword_id`=?';
			$bind[] = $crossword['id'];
			$this->db->query($sql, $bind);
			return ($this->db->affected_rows() > 0);
		}
		else {
			return false;
		}
	}

	/** Get the list of winners for a crossword.
	 * @return array[id,time,firstname,surname] winners.
	 */
	function GetWinners($crossword_id)
	{
		$sql =	'SELECT '.
				'	`user_entity_id`		AS id, '.
				'	UNIX_TIMESTAMP(`crossword_winner_time`)	AS time, '.
				'	`user_firstname`		AS firstname, '.
				'	`user_surname`			AS surname '.
				'FROM	`crossword_winners` '.
				'INNER JOIN	`crosswords` '.
				'	ON	`crossword_id` = `crossword_winner_crossword_id` '.
				'INNER JOIN `users` '.
				'	ON	`user_entity_id` = `crossword_winner_user_entity_id` '.
				'WHERE	`crossword_id` = ? '.
				'ORDER BY	`crossword_winner_time` ASC';
		$bind = array($crossword_id);
		$winners = $this->db->query($sql, $bind)->result_array();
		return $winners;
	}

	/** Add a specific user as a winner of a crossword.
	 * @return bool true if successful.
	 */
	function AddWinner($crossword_id, $user_id)
	{
		$bind = array();
		// Add to winners
		$sql =	'INSERT INTO `crossword_winners` ('.
				'	`crossword_winner_crossword_id`, '.
				'	`crossword_winner_user_entity_id`, '.
				'	`crossword_winner_time` '.
				') '.
				'SELECT	`crossword_id`, ?, NOW() ';
		$bind[] = $user_id;
		// Where the crossword exists
		$sql .=	'FROM	`crosswords` '.
				'WHERE `crossword_id` = ? ';
		$bind[] = $crossword_id;
		// And where the crossword is published
		$sql .=	'	AND '.$this->published_sql.' ';
		// And where the crossword hasn't expired
		$sql .=	'	AND NOT '.$this->expired_sql.' ';
		// And where this user hasn't already got a medal
		$sql .=	'	AND NOT EXISTS (SELECT * '.
				'		 FROM	`crossword_winners` '.
				'		 WHERE	`crossword_winner_crossword_id`=`crossword_id` '.
				'			AND	`crossword_winner_user_entity_id`=?)';
		$bind[] = $user_id;
		$this->db->query($sql, $bind);
		return $this->db->affected_rows() > 0;
	}

	/** Save a version of a crossword for a user.
	 * @param $crossword_id int Id of crossword.
	 * @param $user_id int User entity id.
	 * @param $puzzle CrosswordPuzzle Puzzle with lights to save.
	 * @return bool true if successful.
	 */
	function SaveCrosswordVersion($crossword_id, $user_id, &$puzzle)
	{
		// Create new save row
		$sql =	'INSERT INTO `crossword_saves` '.
				'SET	`crossword_save_crossword_id` = ?, '.
				'		`crossword_save_user_entity_id` = ?, '.
				'		`crossword_save_time` = NOW()';
		$bind = array($crossword_id, $user_id);
		$this->db->query($sql, $bind);
		$save_id = $this->db->insert_id();

		// Go through lights extracting texts
		$grid = &$puzzle->grid();
		$height = $grid->height();
		$width = $grid->width();
		$bind = array();
		$qs = array();
		for ($y = 0; $y < $height; ++$y) {
			for ($x = 0; $x < $width; ++$x) {
				$lights = $grid->lightsAt($x, $y, true);
				foreach ($lights as &$light) {
					$answer = $grid->lightText($light);
					if (str_replace('_','',$answer) == '') {
						continue;
					}
					$qs[] =	'(?,?,?,?,?)';
					$bind[] = $save_id;
					$bind[] = $light->x();
					$bind[] = $light->y();
					$bind[] = (($light->orientation() == CrosswordGrid::$HORIZONTAL)
								? 'horizontal'
								: 'vertical');
					$bind[] = $answer;
				}
			}
		}
		if (count($qs) > 0) {
			$sql =	'INSERT INTO `crossword_light_saves` ('.
					'	`crossword_light_save_save_id`, '.
					'	`crossword_light_save_posx`, '.
					'	`crossword_light_save_posy`, '.
					'	`crossword_light_save_orientation`, '.
					'	`crossword_light_save_answer` '.
					') '.
					'VALUES '.
					join(',',$qs);
			$this->db->query($sql, $bind);
			if ($this->db->affected_rows() < count($qs)) {
				return false;
			}
		}

		return true;
	}

	/** Load a version of a crossword for a user.
	 * @param $crossword_id int Id of crossword.
	 * @param $user_id int User entity id.
	 * @param $puzzle CrosswordPuzzle Puzzle with lights to save.
	 * @param $save_id int,null Save id or null for latest.
	 * @return bool true of successful.
	 */
	function LoadCrosswordVersion($crossword_id, $user_id, &$puzzle, $save_id = null)
	{
		if (null === $save_id) {
			// Get the latest save
			$sql =	'SELECT	`crossword_save_id` AS id, `crossword_save_time` AS time '.
					'FROM	`crossword_saves` '.
					'WHERE	`crossword_save_crossword_id` = ? '.
					'	AND	`crossword_save_user_entity_id` = ? '.
					'ORDER BY	`crossword_save_time` DESC '.
					'LIMIT 0,1';
			$bind = array($crossword_id, $user_id);
			$saves = $this->db->query($sql, $bind)->result_array();
			if (count($saves) < 1) {
				return false;
			}
			$save_id = $saves[0]['id'];
		}

		// Get the light values
		$sql =	'SELECT	`crossword_save_id`					AS id, '.
				'		`crossword_light_save_posx`			AS posx, '.
				'		`crossword_light_save_posy`			AS posy, '.
				'		`crossword_light_save_orientation`	AS orientation, '.
				'		`crossword_light_save_answer`		AS answer '.
				'FROM	`crossword_saves` '.
				'LEFT JOIN	`crossword_light_saves` '.
				'		ON	`crossword_save_id`=`crossword_light_save_save_id` '.
				'WHERE	`crossword_save_id` = ? '.
				'	AND	`crossword_save_crossword_id` = ? '.
				'	AND	`crossword_save_user_entity_id` = ? ';
		$bind = array($save_id, $crossword_id, $user_id);
		$results = $this->db->query($sql, $bind)->result_array();
		// No results mean there isn't even a save with this id, crossword, user
		if (count($results) == 0) {
			return false;
		}
		$grid = &$puzzle->grid();
		foreach ($results as &$result) {
			// If they're null, then skip (no light_saves, just the one save)
			if ($result['posx'] === null) {
				continue;
			}
			
			$light = new CrosswordGridLight(
				(int)$result['posx'],
				(int)$result['posy'],
				(($result['orientation'] == 'horizontal')
					? CrosswordGrid::$HORIZONTAL
					: CrosswordGrid::$VERTICAL),
				strlen($result['answer'])
			);
			$grid->setLightText($light, $result['answer']);
		}
		return true;
	}

	/** Save over a crossword.
	 * @param $crossword_id int Id of crossword.
	 * @param $puzzle CrosswordPuzzle Puzzle with lights to save.
	 * @return bool true if successful.
	 */
	function SaveCrossword($crossword_id, &$puzzle)
	{
		// Delete any existing lights
		$sql =	'DELETE FROM `crossword_lights` '.
				'WHERE `crossword_light_crossword_id` = ?';
		$bind = array($crossword_id);
		$this->db->query($sql, $bind);

		// Make sure size is right
		$grid = &$puzzle->grid();
		$height = $grid->height();
		$width = $grid->width();
		$sql =	'UPDATE `crosswords` '.
				'SET	`crossword_width`=?, '.
				'		`crossword_height`=? '.
				'WHERE	`crossword_id`=?';
		$bind = array($width, $height, $crossword_id);
		$this->db->query($sql, $bind);

		// Then add the new ones
		$bind = array();
		$qs = array();
		for ($y = 0; $y < $height; ++$y) {
			for ($x = 0; $x < $width; ++$x) {
				$lights = $grid->lightsAt($x, $y, true);
				foreach ($lights as &$light) {
					$qs[] =	'(?,?,?,?,?,?,?)';
					$bind[] = $crossword_id;
					$bind[] = $light->x();
					$bind[] = $light->y();
					$bind[] = (($light->orientation() == CrosswordGrid::$HORIZONTAL)
								? 'horizontal'
								: 'vertical');
					$bind[] = $light->clue()->solution(' ');
					$bind[] = $light->clue()->quickClue();
					$bind[] = $light->clue()->crypticClue();
				}
			}
		}
		if (count($qs) > 0) {
			$sql =	'INSERT INTO `crossword_lights` ('.
					'	`crossword_light_crossword_id`, '.
					'	`crossword_light_posx`, '.
					'	`crossword_light_posy`, '.
					'	`crossword_light_orientation`, '.
					'	`crossword_light_solution`, '.
					'	`crossword_light_normal_clue`, '.
					'	`crossword_light_cryptic_clue` '.
					') '.
					'VALUES '.
					join(',',$qs);
			$this->db->query($sql, $bind);
		}
		return true;
	}

	/** Load a crossword.
	 * @param $crossword_id int Id of crossword.
	 * @param $puzzle CrosswordPuzzle Puzzle to load into.
	 * @return bool true if successful.
	 */
	function LoadCrossword($crossword_id, &$puzzle)
	{
		// First get dimentions
		$sql =	'SELECT	`crossword_width`	AS width, '.
				'		`crossword_height`	AS height '.
				'FROM	`crosswords` '.
				'WHERE	`crossword_id` = ?';
		$bind = array($crossword_id);
		$results = $this->db->query($sql, $bind)->result_array();
		if (count($results) === 0) {
			return false;
		}

		// Set up the result
		$width = (int)$results[0]['width'];
		$height = (int)$results[0]['height'];
		$puzzle = new CrosswordPuzzle($width, $height);

		// Get the lights
		$sql =	'SELECT	`crossword_light_posx`			AS posx, '.
				'		`crossword_light_posy`			AS posy, '.
				'		`crossword_light_orientation`	AS orientation, '.
				'		`crossword_light_solution`		AS solution, '.
				'		`crossword_light_normal_clue`	AS quickClue, '.
				'		`crossword_light_cryptic_clue`	AS crypticClue ' .
				'FROM	`crossword_lights` '.
				'WHERE	`crossword_light_crossword_id` = ?';
		// Use bind from above
		$results = $this->db->query($sql, $bind)->result_array();
		foreach ($results as &$result) {
			$puzzle->addLight(
				(int)$result['posx'],
				(int)$result['posy'],
				(($result['orientation'] == 'horizontal')
					? CrosswordGrid::$HORIZONTAL
					: CrosswordGrid::$VERTICAL),
				new CrosswordClue(
					$result['solution'],
					$result['quickClue'],
					$result['crypticClue']
				)
			);
		}
		return true;
	}
}

?>
