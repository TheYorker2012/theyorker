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
		return '/'.$location;
	} else {
		return '/images/photos/null.jpg';
	}
}

// ------------------------------------------------------------------------

/**
 * Photo Location with Tag
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
function photoLocTag($id, $extension = '.jpg', $alt = false, $force = FALSE) {
	$location = 'images/photos/'.(floor($id / IMAGE_HASH)).'/'.$id.$extension;
	if ($force or is_file($location)) {
		if (is_string($alt)) {
			return '<a href="'.photoLocation($id, $extension).'"><img src="/'.$location.'" title="'.$alt.'" alt="'.$alt.'" /></a>';
		} else {
			$CI =& get_instance();
			$query = $CI->db->select('photo_title')->getwhere('photos', array('photo_id' => $id), 1);
			$query = $query->result()->photo_title;
			return '<a href="'.photoLocation($id, $extension).'"><img src="/'.$location.'" title="'.$query.'" alt="'.$query.'" /></a>';
		}
		return '/'.$location;
	} else {
		return '<img src="/images/images/null.jpg" alt="File not found" title="File not found"/>';
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
		if ($force or is_file($location)) {
			return '/'.$location;
		} else {
			return '/images/photos/null.jpg';
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
				return '/images/photos/null.jpg';
			}
			$location = 'images/images/'.$fetched_type.'/'.(floor($id / IMAGE_HASH)).'/'.$id.$extension;
			if ($force or is_file($location)) {
				return '/'.$location;
			} else {
				return '/images/photos/null.jpg';
			}
		} else {
			return '/images/photos/null.jpg';
		}
	}
}

// ------------------------------------------------------------------------

/**
 * Image Location with Tag
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

function imageLocTag($id, $type = false, $extension = '.jpg', $alt = null) {
	if (is_null($extension)) $extension = '.jpg';
	if (is_string($type)) {
		$location = 'images/images/'.$type.'/'.(floor($id / IMAGE_HASH)).'/'.$id.$extension;
		if ($force or is_file($location)) {
			if (is_string($alt)) {
				return '<a href="'.photoLocation($id, $extension).'"><img src="/'.$location.'" title="'.$alt.'" alt="'.$alt.'" /></a>';
			} else{
				$CI =& get_instance();
				$query = $CI->db->select('photo_title')->getwhere('photos', array('photo_id' => $id), 1);
				$query = $query->result();
				return '<a href="'.photoLocation($id, $extension).'"><img src="/'.$location.'" title="'.$query->photo_title.'" alt="'.$query->photo_title.'" /></a>'
			}
		} else {
			return '<img src="/images/images/'.$type.'/null.jpg" />';
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
				return '<img src="/images/images/null.jpg" />';
			}
			$location = 'images/images/'.$fetched_type.'/'.(floor($id / IMAGE_HASH)).'/'.$id.$extension;
			if ($force or is_file($location)) {
				if (is_string($alt)) {
					return '<a href="'.photoLocation($id, $extension).'"><img src="/'.$location.'" title="'.$alt.'" alt="'.$alt.'" /></a>';
				} else{
					$CI =& get_instance();
					$query = $CI->db->select('photo_title')->getwhere('photos', array('photo_id' => $id), 1);
					$query = $query->result();
					return '<a href="'.photoLocation($id, $extension).'"><img src="/'.$location.'" title="'.$query->photo_title.'" alt="'.$query->photo_title.'" /></a>'
				}
			} else {
				return '<img src="/images/images/'.$fetched_type.'/null.jpg" />';
			}
		} else {
			return '<img src="/images/images/null.jpg" />';
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
function createImageLocation($id, $type = FALSE) {
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


//Depreciated -- only the gallery should use these :( phase out in later release
function ImageLocationFromId($id, $type = false, $extension = '.jpg', $force = FALSE) {
	if (is_null($extension)) $extension = '.jpg';
	$CI =& get_instance();
	$query = $CI->db->select('image_type_codename')->getwhere('image_types', array('image_type_id' => $type), 1);
	$codename = $query->row()->image_type_codename;
	$location = 'images/images/'.$codename.'/'.(floor($id / IMAGE_HASH)).'/'.$id.$extension;
	if ($force or is_file($location)) {
		return '/'.$location;
	} else {
		return '/images/photos/null.jpg';
	}
}

function createImageLocationFromId($id, $type) {
	$CI =& get_instance();
	$query = $CI->db->select('image_type_codename')->getwhere('image_types', array('image_type_id' => $type), 1);
	$codename = $query->row()->image_type_codename;
	$location = 'images/images/'.$codename.'/';
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
}


?>