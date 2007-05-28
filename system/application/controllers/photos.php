<?php
/**
 * @author Mark Goodall (mark.goodall@gmail.com)
 *
 */
class Photos extends Controller
{
	/// Default constructor.
	function Photos() {
		parent::Controller();
	}

	function index($type = 'full', $id = -1) {
		if ($type == 'full') {
			$sql = 'SELECT
					photo_mime AS mime,
					photo_data AS data,
					UNIX_TIMESTAMP(photo_timestamp) AS timestamp
				FROM photos WHERE photo_id = ? LIMIT 1';
			$result = $this->db->query($sql, array($id));
			if ($result->num_rows() == 1) {
				$row = $result->first_row();
			} else {
				header('HTTP/1.0 404 Not Found');
				return;
			}
		} elseif ($type == 'view') {
			die('//TODO page containing full view');
		} else {
			$sql = 'SELECT
					photo_thumbs_data AS data,
					photo_mime AS mime,
					UNIX_TIMESTAMP(photo_thumbs_timestamp) AS timestamp
				FROM photo_thumbs, photos
			        WHERE photo_thumbs_photo_id = ?
			          AND photo_thumbs_photo_id = photo_id
			          AND photo_thumbs_image_type_id = (SELECT image_type_id FROM image_types WHERE image_type_codename =? LIMIT 1)
			        LIMIT 1';
			$result = $this->db->query($sql, array($id, $type));
			if ($result->num_rows() == 1) {
				$row = $result->first_row();
			} else {
				$sql = 'SELECT
						image_type_error_mime AS mime,
						image_type_error_data AS data,
						NOW() AS timestamp
					FROM image_types WHERE image_type_codename = ? LIMIT 1';
				$result = $this->db->query($sql, array($type));
				if ($result->num_rows() == 1) {
					$row = $result->first_row();
				} else {
					header('HTTP/1.0 404 Not Found');
					return;
				}
			}
		}

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			$modified = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
			if ($modified >= $row->timestamp) {
				header('HTTP/1.1 304 Not Modified');
				return;
			}
		}

		header('Content-type: '.$row->mime);
		header('Last-Modified: '.date('r', $row->timestamp));
		echo $row->data;
	}
}
?>
