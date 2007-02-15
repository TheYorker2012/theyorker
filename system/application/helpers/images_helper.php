<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Code Igniter URL Helpers
 *
 * @package		TheYorker
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Mark Goodall <mark.goodall@gmail.com
 */

// ------------------------------------------------------------------------
define ("IMAGE_HASH", 2000);
/**
 * Photo Location
 *
 * When Given an ID, this will return the location of an Photo. 
 * Offers a fallback when the photo is not found.
 *
 * @access	public
 * @param	integer
 * @param	string
 * @param	boolean
 * @return	string
 */	
function photoLocation($id, $extension = '.jpg', $force = FALSE) {
	$location = 'images/photos/'.(floor($id / IMAGE_HASH)).'/'.$id.$extension;
	if ($force or is_file($location)) {
		return $location;
	} else {
		return 'images/photos/null.jpg';
	}
}

// ------------------------------------------------------------------------

/**
 * Image Location
 *
 * When Given an ID, this will return the location of an Image. 
 * Offers a partial fallback when the image is not found. The optional 
 * type_codename parameter greatly speeds up the function my removing the 
 * need to query the database.
 *
 * If a type is not specified, then it will assume that it exists in the
 * database and fetch a type.
 *
 * @access	public
 * @param	integer
 * @param	integer
 * @param	string
 * @param	boolean
 * @return	string
 */	

function imageLocation($id, $type = false, $extension = '.jpg', $force = FALSE) {
	if (is_null($extension)) $extension = '.jpg';
	if (is_string($type)) {
		$location = 'images/images/'.$type.'/'.(floor($id / IMAGE_HASH)).'/'.$id.$extension;
		echo 'beforenm'.$location;
		if ($force or is_file($location)) {
			echo 'innm'.$location;
			return $location;
		} else {
			return 'images/photos/null.jpg';
		}
	} else {
		$CI =& get_instance();
		$query = $CI->db->select('image_image_type_id')->getwhere('images', array('image_id' => $id), 1);
		$fetched_type = false;
		foreach ($query->result() as $onerow) {
			$fetched_type = $onerow->image_image_type_id;
		}
		$query->free_result();
		if ($fetched_type) {
			$query = $CI->db->select('image_type_codename')->getwhere('image_types', array('image_type_id' => $fetched_type));
			$fetched_type = false;
			foreach ($query->result() as $onerow) {
				$fetched_type = $onerow->image_type_codename;
			}
			if (!$fetched_type) {
				return 'images/photos/null.jpg';
			}
			$location = 'images/images/'.$fetched_type.'/'.(floor($id / IMAGE_HASH)).'/'.$id.$extension;
			echo 'db'.$location;
			if ($force or is_file($location)) {
				echo $location;
				return $location;
			} else {
				return 'images/photos/null.jpg';
			}
		} else {
			return 'images/photos/null.jpg';
		}
	}
}

// ------------------------------------------------------------------------

/**
 * Location Constructor
 *
 * When Given an ID, this will create the location of an Image or photo 
 * if no type is specified.
 *
 * @access	public
 * @param	integer
 * @param	integer
 * @return	string
 */	
function createImageLocation($id, $type = false) {
	if (is_string($type)) {
		$location = 'images/images/'.$type.'/';
		if (is_dir($location)) {
			$location.= (floor($id / IMAGE_HASH)).'/';
			if (is_dir($location)) {
				return true;
			} elseif (mkdir($location, 0770)) {
				return true;
			} else {
				return false;
			}
		} else {
			if (mkdir($location, 0770)) {
				$location.= (floor($id / IMAGE_HASH)).'/';
				if (is_dir($location)) {
					return true;
				} elseif (mkdir($location, 0770)) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	} else {
		$location = 'images/photos/'.(floor($id / IMAGE_HASH)).'/';
		if (is_dir($location)) {
			return true;
		} else {
			if (mkdir($location, 0770)) {
				return true;
			} else {
				return false;
			}
		}
	}
	
}

?>