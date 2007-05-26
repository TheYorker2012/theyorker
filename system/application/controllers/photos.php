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
			$sql = 'SELECT photo_mime, photo_data FROM photos WHERE photo_id = ? LIMIT 1';
			$result = $this->db->query($sql, array($id));
			if ($result->num_rows() == 1) {
				header('Expires: '.date(DATE_RFC822, strtotime('+1 month')));
				header('Cache-Control: store, cache');
				header('Pragma: cache');
				header('Content-Type: '.$result->first_row()->photo_mime);
				echo $result->first_row()->photo_data;
			} else {
				header('HTTP/1.0 404 Not Found');
			}
		} elseif ($type == 'view') {
			die('//TODO page containing full view');
		} else {
			$sql = 'SELECT photo_thumbs_data, photo_mime FROM photo_thumbs, photos
			        WHERE photo_thumbs_photo_id = ?
			          AND photo_thumbs_photo_id = photo_id
			          AND photo_thumbs_image_type_id = (SELECT image_type_id FROM image_types WHERE image_type_codename =? LIMIT 1)
			        LIMIT 1';
			$result = $this->db->query($sql, array($id, $type));
			if ($result->num_rows() == 1) {
				header('Expires: '.date(DATE_RFC822, strtotime('+10 minutes')));
				header('Cache-Control: no-store, cache');
				header('Pragma: cache');
				header('Content-Type: '.$result->first_row()->photo_mime);
				echo $result->first_row()->photo_thumbs_data;
			} else {
				$sql = 'SELECT image_type_error_mime, image_type_error_data FROM image_types WHERE image_type_codename = ? LIMIT 1';
				$result = $this->db->query($sql, array($type));
				if ($result->num_rows() == 1) {
					header('Expires: '.date(DATE_RFC822, strtotime('-10 minutes')));
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
}
?>