<?php

/**
 * @author Mark Goodall (mark.goodall@gmail.com)
 */
class Images extends Controller
{
	/// Default constructor.
	function Images() {
		parent::Controller();
	}
	
	function index($type, $id = 0) {
		$sql = 'SELECT image_mime, image_data FROM images WHERE image_id = ? LIMIT 1';
		$result = $this->db->query($sql, array($id));
		if ($result->num_rows() == 1){
			header('Expires: '.date(DATE_RFC822, strtotime('+10 minutes')));
			header('Cache-Control: no-store, cache');
			header('Pragma: cache');
			header('Content-Type: '.$result->first_row()->image_mime);
			echo $result->first_row()->image_data;
		} else {
			$sql = 'SELECT image_type_error_mime, image_type_error_data FROM image_types WHERE image_type_codename = ? LIMIT 1';
			$result = $this->db->query($sql, array($type));
			if ($result->num_rows() == 1) {
				header('Expires: '.date(DATE_RFC822, strtotime('+5 minutes')));
				header('Cache-Control: no-store, cache');
				header('Pragma: cache');
				header('Content-Type: '.$result->first_row()->image_type_error_mime);
				echo $result->first_row()->image_type_error_data;
			} else {
				header('HTTP/1.0 404 Not Found');
			}
		}
	}
}
?>