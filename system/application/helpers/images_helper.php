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
function photoLocation($id, $extension = '.jpg', $repeat = FALSE) {
	$location = 'images/photos/'.(floor($id / IMAGE_HASH)).'/'.$id.$extension;
	if ($repeat || is_file($location)) {
		return $location;
	} else {
		return photoLocation('1', '.jpg', true);
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
 * @access	public
 * @param	integer
 * @param	integer
 * @param	string
 * @param	boolean
 * @return	string
 */	

function imageLocation($id, $type = false, $extension = '.jpg', $repeat = FALSE) {
	if (is_null($extension)) $extension = '.jpg';
	if ($type_codename) {
		$location = 'images/images/'.$type.'/'.(floor($id / IMAGE_HASH)).'/'.$id.$extension;
		if ($repeat || is_file($location)) {
			return $location;
		} else {
			return imageLocation('1', false, '.jpg', true);
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
			$location = 'images/images/'.$fetched_type.'/'.(floor($id / IMAGE_HASH)).'/'.$id.$extension;
			if ($repeat || is_file($location)) {
				return $location;
			} else {
				return ''; //run the risk of recursion if this is set
			}
		} else {
			return imageLocation('1', false, '.jpg', true);
		}
	}
}

// ------------------------------------------------------------------------

/**
 * Location Constructor
 *
 * When Given an ID, this will create the location of an Image or photo 
 * if required.
 *
 * @access	public
 * @param	integer
 * @param	integer
 * @return	string
 */	
function createImageLocation($id, $type = false) {
	if ($type) {
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

