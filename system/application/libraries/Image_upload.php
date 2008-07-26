<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
if (!defined('VIEW_WIDTH')) {
	define('VIEW_WIDTH', 600);
}
define('VIEW_HEIGHT', 600);
class Image_upload {

	private $ci;

	public function Image_upload() {
		$this->ci = &get_instance();
		$this->ci->load->library(array('xajax', 'image'));
		$this->ci->load->helper('url');
		$this->ci->xajax->registerFunction(array("process_form_data", &$this, "process_form_data"));
		putenv('GDFONTPATH=' . realpath('.').'/images');
	}

	public function automatic($returnPath, $types = false, $multiple = false, $photos = false) {
		if ($this->uploadForm($multiple, $photos)) {
			$this->recieveUpload($returnPath, $types, $photos);
		}
	}

	public function uploadForm($multiple = false, $photos = false) {
		$this->ci->xajax->processRequests();
		$_SESSION['img'] = array();
		if ($this->ci->input->post('destination')) {
			return true;
		}
		$this->ci->load->model('photos_model');
		$data = array(
			'watermark_colours' => $this->ci->photos_model->GetWatermarkColours()
		);
		$this->ci->main_frame->IncludeJs('javascript/clone.js');
		if ($multiple && $photos) {
			$this->ci->main_frame->SetTitle('Multiple Photo Uploader');
			$this->ci->main_frame->SetContentSimple('uploader/upload_multiple_photos', $data);
		} elseif ($multiple) {
			$this->ci->main_frame->SetTitle('Multiple Image Uploader');
			$this->ci->main_frame->SetContentSimple('uploader/upload_multiple_images', $data);
		} elseif ($photos) {
			$this->ci->main_frame->SetTitle('Photo Upload');
			$this->ci->main_frame->SetContentSimple('uploader/upload_single_photo', $data);
		} else {
			$this->ci->main_frame->SetTitle('Image Upload');
			$this->ci->main_frame->SetContentSimple('uploader/upload_single_image', $data);
		}
		$this->ci->main_frame->Load();
	}

	private function checkImageProperties(&$imgData, &$imgTypes, $photo) {
		foreach ($imgTypes->result() as $imgType) {
			if ($imgData['image_width'] < $imgType->image_type_width) return false;
			if ($imgData['image_height'] < $imgType->image_type_height) return false;
		}
		return true;
	}

	//types is an array
	public function recieveUpload($returnPath, $types = false, $photo = true) {
		$this->ci->load->library(array('image_lib', 'upload'));
		$data = array();

		$config = array();
		$config['upload_path'] = './tmp/uploads/';
		$config['allowed_types'] = 'jpg|png|gif|jpeg';
		$config['max_size'] = 16384;
		$this->ci->upload->initialize($config);

		$photos_loaded = 0;
		$errors = array();

		for ($x = 1; $x <= $this->ci->input->post('destination'); $x++) {
			$title = $this->ci->input->post('title'.$x);
			$source = $this->ci->input->post('photo_source' . $x);
			if (!isset($title) || strlen($title) == 0) {
				$errors[] = 'Photo ' . $x . ' did not have a title set.';
			} elseif (!isset($source) || strlen($source) == 0) {
				$errors[] = 'Photo ' . $x . ' did not have it\'s source set.';
			} else {
				if ( ! $this->ci->upload->do_upload('userfile'.$x)) {
					$errors[] = 'Photo ' . $x . ': ' . $this->ci->upload->display_errors();
				} else {
					$data[] = $this->ci->upload->data();
					if (!$data[$x - 1]['is_image']) {
						$errors[] = 'Uploaded file ' . $x . ' is not an image.';
					} else {
						// fix for Microsoft's Stupidity
						if ($data[$x - 1]['file_type'] == 'image/pjpeg') {
							$data[$x - 1]['file_type'] = 'image/jpeg';
						}
						$this->processImage($data[$x - 1], $x, $photo);
						$photos_loaded++;
					}
				}
			}
		}

		if ($photos_loaded > 0) {
			$this->ci->main_frame->AddMessage('success', $photos_loaded . ' photos were successfully uploaded.');
		}
		if (count($errors) > 0) {
			$this->ci->main_frame->AddMessage('error', implode('<br />', $errors));
		}

		redirect($returnPath);
	}

	public function process_form_data($formData) {
		$objResponse = new xajaxResponse();
		$selectedThumb = explode("|", $formData['imageChoice']);
		// 0 location
		// 1 original width(?)
		// 2 original height(?)
		// 3 type
		// 4 image id
		// 5 image type width
		// 6 image type height
		// 7 title
		// 8 thumb_id
		// 9 watermark

		/* REDO
		$securityCheck = array_search($selectedThumb[4], $_SESSION['img'][]['list']);// this is the line to change
		if ($securityCheck === false) {
			exit("LOGOUT #1" . print_r($selectedThumb) . '****' . var_dump($_SESSION['img']));
			$this->ci->user_auth->logout();
			redirect('/', 'location');
			//TODO add some kind of logging
			exit;
		} else {
			if ($_SESSION['img'][$securityCheck]['type'] != $selectedThumb[3]) {
				exit("LOGOUT #2" . print_r($selectedThumb) . '****' . var_dump($_SESSION['img']));
				$this->ci->user_auth->logout();
				redirect('/', 'location');
				//TODO add some kind of logging
				exit;
			}
		}
		*/

		$sql = 'SELECT image_type_id AS id, image_type_width AS x,
		               image_type_height AS y, image_type_codename AS codename
		        FROM image_types WHERE image_type_id = ? LIMIT 1';
		$result = $this->ci->db->query($sql, array($selectedThumb[3]));
		if($result->num_rows() != 1) {
			$this->ci->user_auth->logout();
			redirect('/');
			//TODO add some kind of logging
			exit;
		}

		$bits = explode('/', $selectedThumb[0]);
		if ($bits[1] == 'tmp') {
			//Get mime
			if (function_exists('exif_imagetype')) {
				$mime = image_type_to_mime_type(exif_imagetype('.'.$selectedThumb[0]));
			} else {
				$byDot = explode('.', $selectedThumb[0]);
				switch ($byDot[count($byDot)-1]) {
					case 'jpg':
					case 'jpeg':
					case 'JPG':
					case 'JPEG':
						$mime = 'image/jpeg';
						break;
					case 'png':
					case 'PNG':
						$mime = 'image/png';
						break;
					case 'gif':
					case 'GIF':
						$mime = 'image/gif';
						break;
				}
			}

			switch ($mime) {
				case 'image/jpeg':
					$image = imagecreatefromjpeg('.'.$selectedThumb[0]);
					break;
				case 'image/png':
					$image = imagecreatefrompng('.'.$selectedThumb[0]);
					break;
				case 'image/gif':
					$image = imagecreatefromgif('.'.$selectedThumb[0]);
					break;
			}

			$result = $result->first_row();
			$newImage = imagecreatetruecolor($result->x, $result->y);
			imagecopyresampled($newImage, $image, 0, 0, $formData['x1'], $formData['y1'], $result->x, $result->y, $formData['width'], $formData['height']);

			$id = $this->ci->image->add('image', $newImage, array('title' => $selectedThumb[7], 'mime' => $mime, 'type_id' => $selectedThumb[3]));
			if ($id != false) {
				for ($iUp = 0; $iUp < count($_SESSION['img']); $iUp++) {
					if ($selectedThumb[4] == $_SESSION['img'][$iUp]['list'] and $selectedThumb[3] == $_SESSION['img'][$iUp]['type']) {
						if (isset($_SESSION['img'][$iUp]['oldID'])) {
							$this->ci->image->delete('image', $_SESSION['img'][$iUp]['oldID']); //TODO log orphaned image if false
							$_SESSION['img'][$iUp]['oldID'] = $id;
						} else {
							$_SESSION['img'][$iUp]['oldID'] = $id;
						}
						$_SESSION['img'][$iUp]['list'] = $id;
					}
				}
// php limitation
//				foreach ($_SESSION['img'] as &$newImages) {
//					if ($selectedThumb[4] == $newImages['list'] and $selectedThumb[3] == $newImages['type']) {
//						if (isset($newImages['oldID'])) {
//							$this->ci->image->delete('image', $newImages['oldID']); //TODO log orphaned image if false
//							$newImage['oldID'] = $id;
//						} else {
//							$newImages['oldID'] = 0;
//						}
//						//$newImages['list'] = $id;
//					}
//				}
			} else {
				$objResponse->addAlert("The thumbnail was not saved, please try again.");
				$objResponse->addAssign("submitButton","value","Save again");
				$objResponse->addAssign("submitButton","disabled",false);
				return $objResponse;
			}
		} else {
			$sql = 'DELETE FROM photo_thumbs WHERE photo_thumbs_photo_id = ? AND photo_thumbs_image_type_id = ? LIMIT 1';
			$this->ci->db->query($sql, array($selectedThumb[4], $selectedThumb[3]));
			if (!$this->ci->image->thumbnail($selectedThumb[4], $result->first_row(), $formData['x1'], $formData['y1'], $formData['width'] , $formData['height'], $selectedThumb[9]) ) {
				$objResponse->addAlert("The thumbnail was not saved, please try again.");
			}
		}

		$objResponse->addScriptCall("registerImageSave", $selectedThumb[4].'-'.$selectedThumb[3]);
		$objResponse->addAssign("submitButton","value","Save");
		$objResponse->addAssign("submitButton","disabled",false);

		return $objResponse;
	}

	private function processImage($data, $form_value, $photo) {
		$image = null;
		switch ($data['file_type']) {
			case 'image/gif':
				$image = imagecreatefromgif($data['full_path']);
				break;
			case 'image/jpeg':
				$image = imagecreatefromjpeg($data['full_path']);
				break;
			case 'image/png':
				$image = imagecreatefrompng($data['full_path']);
				break;
		}

		$width = $data['image_width'];
		$height = $data['image_height'];
		$x = imagesx($image);
		$y = imagesy($image);

		if ($photo) {
			unlink($data['full_path']);
			$info = array(
				'author_id'				=> $this->ci->user_auth->entityId,
				'title'     			=> $this->ci->input->post('title'.$form_value),
				'x'         			=> $x,
				'y'         			=> $y,
				'mime'      			=> $data['file_type'],
				'watermark' 			=> $this->ci->input->post('watermark'.$form_value),
				'watermark_colour_id'	=> $this->ci->input->post('watermark_colour'.$form_value),
				'source'				=> $this->ci->input->post('photo_source'.$form_value)
			);
			$id = $this->ci->image->add('photo', $image, $info);
			if ($id === false) {
				return false;
			}
		}
	}
}
?>
