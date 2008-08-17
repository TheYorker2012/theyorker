<?php
/**
 *	@author		Mark Goodall (mark.goodall@gmail.com)
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Photos extends Controller
{
	function Photos() {
		parent::Controller();
		$this->load->model('photos_model');
	}

	function index($type = 'full', $id = -1) {
		if ($type == 'full') {
			$photo = $this->photos_model->GetFullPhoto($id);
			if (!$photo)
				show_404();
		} elseif ($type == 'view') {
			// @TODO: Create a gallery viewing page
			show_404();
		} else {
			// Check requested image type exists
			$type_info = $this->photos_model->GetTypeInfo($type);
			if (!$type_info)
				show_404();
			// See if a thumbnail is available
			$photo = $this->photos_model->GetThumbnail($id, $type_info->id);
			if ((!$photo) || ($photo->data === NULL)) {
				// Check the photo does actually exist
				$properties = $this->photos_model->GetOriginalPhotoProperties($id);
				if (!$properties)
					show_404();
				if (isset($photo->x)) {
					// Need to re-generate thumbnail
					$new_x = $photo->x;
					$new_y = $photo->y;
					$new_width = $photo->width;
					$new_height = $photo->height;
				} else {
					// Thumbnail doesn't exist so auto create one
					$thumb_ratio = $type_info->width / $type_info->height;
					$new_height = $properties->height;
					$new_width = $new_height * $thumb_ratio;
					if ($new_width > $properties->width) {
						$new_width = $properties->width;
						$new_height = $new_width / $thumb_ratio;
					}
					$new_x = floor(($properties->width - $new_width) / 2);
					$new_y = floor(($properties->height - $new_height) / 2);
				}
				$this->load->library('image');
				$this->image->thumbnail($id, $type_info, $new_x, $new_y, $new_width, $new_height);
				$photo = $this->photos_model->GetThumbnail($id, $type_info->id);
				if (!$photo) {
					// There must have been an error creating the thumbnail
					$photo = $this->photos_model->GetErrorImage($type);
					if (!$photo)
						show_404();
				}
			}
		}

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			$modified = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
			if ($modified >= $photo->timestamp) {
				header('HTTP/1.1 304 Not Modified');
				return;
			}
		}

		header('Content-type: '.$photo->mime);
		header('Last-Modified: '.date('r', $photo->timestamp));
		echo $photo->data;
	}
}
?>