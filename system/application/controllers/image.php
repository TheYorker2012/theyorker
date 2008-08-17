<?php

/**
 * @author Mark Goodall (mark.goodall@gmail.com)
 */
class Image extends Controller
{
	/// Default constructor.
	function Image() {
		parent::Controller();
	}
	
	function index($type, $id = 0) {
		$sql = 'SELECT	image_mime,
						image_data,
						UNIX_TIMESTAMP(image_timestamp) AS image_timestamp
				FROM	images
				WHERE	image_id = ?
				LIMIT	1';
		$result = $this->db->query($sql, array($id));
		if ($result->num_rows() == 1){
			$row = $result->first_row();
		} else {
			$sql = 'SELECT	image_type_error_mime AS image_mime,
							image_type_error_data AS image_data,
							UNIX_TIMESTAMP() AS image_timestamp
					FROM	image_types
					WHERE	image_type_codename = ?
					LIMIT	1';
			$result = $this->db->query($sql, array($type));
			if ($result->num_rows() == 1) {
				$row = $result->first_row();
			} else {
				header('HTTP/1.0 404 Not Found');
				return;
			}
		}

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			$modified = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
			if ($modified >= $row->image_timestamp) {
				header('HTTP/1.1 304 Not Modified');
				return;
			}
		}

		header('Content-Type: '.$row->image_mime);
		header('Last-Modified: '.date('r', $row->image_timestamp));
		echo $row->image_data;
	}
}
?>
