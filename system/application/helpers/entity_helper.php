<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter Entity Helper
 *
 * @package		TheYorker
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Mark Goodall <mark.goodall@gmail.com
 */

// ------------------------------------------------------------------------
/**
 * Entity Name
 *
 * When Given an ID, this will return the name of the entity.
 *
 * @access	public
 * @param	integer
 * @param	string
 * @param	boolean
 * @return	string
 */	
function fullname($id) {
	$query = $this->db->getwhere('users', array('user_entity_id'), 1);
	if ($query->num_rows() == 1) {
		$result = $query->row();
		return $result->user_firstname.' '.$result->user_surname;
	} else {
		return 'Invalid User ID';
	}
}