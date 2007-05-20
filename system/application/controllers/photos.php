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
	
	function index($type = 'full', $id) {
		if ($type == 'full') {
			die('//TODO dump a full view');
		} elseif ($type == 'view') {
			die('//TODO page containing full view');
		} else {
			$sql = 'SELECT photo_thumb_mime, photo_thumb_data, photo_mime FROM photo_thumbs, photos
			        WHERE photo_thumb_photo_id = ?
			          AND photo_thumb_photo_id = photo_id
			          AND photo_thumb_image_type_id = (SELECT image_type_id FROM image_types WHERE image_type_codename =? LIMIT 1)
			        LIMIT 1';
			$result = $this->db->query($sql, array($id, $type));
			if ($result->num_rows() == 1) {
				header('Content-Type: '.$result->first_row()->photo_mime);
				echo $result->first_row()->photo_thumb_data;
			} else {
				$sql = 'SELECT image_type_error_mime, image_type_error_data FROM image_types WHERE image_type_codename = ? LIMIT 1';
				$result = $this->db->query($sql, array($type));
				if ($result->num_rows() == 1) {
					header('Content-Type: '.$result->first_row()->image_type_error_mime);
					echo $result->first_row()->image_type_error_data;
				}
			}
		}
	}
}
?>