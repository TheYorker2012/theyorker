<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
if (!defined('VIEW_WIDTH')) {
	define('VIEW_WIDTH', 650);
}
define('VIEW_HEIGHT', 650);
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
		if ($this->ci->input->post('destination')) return true;
		if ($multiple && $photos) {
			$this->ci->main_frame->SetTitle('Multiple Photo Uploader');
			$this->ci->main_frame->SetExtraHead('<script src="/javascript/clone.js" type="text/javascript"></script>');
			$this->ci->main_frame->SetContentSimple('uploader/upload_multiple_photos');
		} elseif ($multiple) {
			$this->ci->main_frame->SetTitle('Multiple Image Uploader');
			$this->ci->main_frame->SetExtraHead('<script src="/javascript/clone.js" type="text/javascript"></script>');
			$this->ci->main_frame->SetContentSimple('uploader/upload_multiple_images');
		} elseif ($photos) {
			$this->ci->main_frame->SetTitle('Photo Upload');
			$this->ci->main_frame->SetContentSimple('uploader/upload_single_photo');
		} else {
			$this->ci->main_frame->SetTitle('Image Upload');
			$this->ci->main_frame->SetContentSimple('uploader/upload_single_image');
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

		//get data about thumbnails

		$config['upload_path'] = './tmp/uploads/';
		$config['allowed_types'] = 'jpg|png|gif|jpeg';
		$config['max_size'] = 16384;

		if (is_array($types)) {
			$query = $this->ci->db->select('image_type_id, image_type_codename, image_type_name, image_type_width, image_type_height');
			$query = $query->where('image_type_photo_thumbnail', $photo);
			$type = array_pop($types);
			$query = $query->where('image_type_codename', $type);
			foreach ($types as $type) {
				$query = $query->orwhere('image_type_codename', $type);
			}
			$query = $query->get('image_types');
		} else {
			$query = $this->ci->db->select('image_type_id, image_type_codename, image_type_name, image_type_width, image_type_height')->getwhere('image_types', array('image_type_photo_thumbnail' => '1'));
		}
		$data = array();
		$this->ci->upload->initialize($config);
		for ($x = 1; $x <= $this->ci->input->post('destination'); $x++) {
			if ( ! $this->ci->upload->do_upload('userfile'.$x)) {
				$this->ci->main_frame->AddMessage('error', $this->ci->upload->display_errors());
				redirect($returnPath, 'location');
			} else {
				$data[] = $this->ci->upload->data();

				if (!$data[$x - 1]['is_image']) {
					$this->ci->main_frame->AddMessage('error', 'The uploaded file was not an image.');
					redirect($returnPath, 'location');
				} elseif ($this->checkImageProperties($data[$x - 1], $query, $photo)) {
					// fix for Microsoft's Stupidity
					if ($data[$x - 1]['file_type'] == 'image/pjpeg') {
						$data[$x - 1]['file_type'] = 'image/jpeg';
					}
					$data[$x - 1] = $this->processImage($data[$x - 1], $x, $query, $photo);
				} elseif($this->ci->input->post('destination') == 1) {
					//redirect back home
					$this->ci->main_frame->AddMessage('error', 'The image you uploaded is too small');
					redirect($returnPath, 'location');
				} else {
					//just display error
					$this->ci->main_frame->AddMessage('error', 'One of the images you uploaded was too small');
				}
			}
		}
		$this->ci->main_frame->SetTitle('Photo Uploader');
		$head = $this->ci->xajax->getJavascript(null, '/javascript/xajax.js');
		$head.= '<link rel="stylesheet" type="text/css" href="/stylesheets/cropper.css" media="all" /><script src="/javascript/prototype.js" type="text/javascript"></script><script src="/javascript/scriptaculous.js?load=builder,effects,dragdrop" type="text/javascript"></script><script src="/javascript/cropper.js" type="text/javascript"></script>';
		$this->ci->main_frame->SetExtraHead($head);
		$this->ci->main_frame->SetContentSimple('uploader/upload_cropper_new', array('returnPath' => $returnPath, 'data' => $data, 'ThumbDetails' => &$query, 'type' => $photo));
		return $this->ci->main_frame->Load();
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
			redirect('/', 'location');
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

			//Water mark
			$photowatermark = $selectedThumb[9];
			if (strlen($photowatermark) > 0) {
				$grey = imagecolorallocate($newImage, 0x99, 0x99, 0x99);
				$font = 'arial';
				imagettftext($newImage, 8, 90, $width - 5, $height - 5, $grey, $font, htmlspecialchars_decode($photowatermark));
			}

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
						//$newImages['list'] = $id;
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
			$this->ci->image->thumbnail($selectedThumb[4], $result->first_row(), $formData['x1'], $formData['y1'], $formData['width'] , $formData['height']);
		}

		$objResponse->addScriptCall("registerImageSave", $selectedThumb[4].'-'.$selectedThumb[3]);
		$objResponse->addAssign("submitButton","value","Save");
		$objResponse->addAssign("submitButton","disabled",false);

		return $objResponse;
	}

	private function processImage($data, $form_value, &$ThumbDetails, $photo) {
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

		if ($data['image_width'] > VIEW_WIDTH) {
			$ratio_orig = $data['image_width']/$data['image_height'];
			$width = VIEW_WIDTH;
			$height = VIEW_HEIGHT;
			if (VIEW_WIDTH/VIEW_HEIGHT > $ratio_orig) {
			   $width = VIEW_HEIGHT*$ratio_orig;
			} else {
			   $height = VIEW_WIDTH/$ratio_orig;
			}
			$newImage = imagecreatetruecolor($width, $height);
			imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $data['image_width'], $data['image_height']);
		} else {
			$newImage = $image;
		}

		//Water mark
		$photowatermark = $this->ci->input->post('watermark');
		$photowatermark = (isset($photowatermark) ? trim($this->ci->input->post('watermark')) : '');
		if (strlen($photowatermark) > 0) {
			$grey = imagecolorallocate($newImage, 0x99, 0x99, 0x99);
			$font = 'arial';
			imagettftext($newImage, 8, 90, $width - 10, $height - 10, $grey, $font, htmlspecialchars_decode($photowatermark));
		}

		$x = imagesx($newImage);
		$y = imagesy($newImage);

		$output = array();

		if ($photo) {
			unlink($data['full_path']);
			$info = array('author_id' => $this->ci->user_auth->entityId,
			              'title'     => $this->ci->input->post('title'.$form_value),
			              'x'         => $x,
			              'y'         => $y,
			              'mime'      => $data['file_type'],);
			$id = $this->ci->image->add('photo', &$newImage, $info);
			if ($id === false) {
				return false;
			} else {
				foreach ($ThumbDetails->result() as $Thumb) {
					$watermark = ($Thumb->image_type_codename == 'medium' && $photowatermark ? str_replace('|', '', $photowatermark) : '');

					$_SESSION['img'][] = array('list' => $id, 'type' => $Thumb->image_type_id);
					$output[] = array('title'  => $this->ci->input->post('title'.$form_value).' - '.$Thumb->image_type_name,
					                  'string' => '/photos/full/'.$id.'|'.$x.'|'.$y.'|'.$Thumb->image_type_id.'|'.$id.'|'.$Thumb->image_type_width.'|'.$Thumb->image_type_height.'|'.str_replace('|', '', $this->ci->input->post('title'.$form_value)).'|'.$id.'-'.$Thumb->image_type_id.'|'.$watermark,
					                  'thumb_id' => $id.'-'.$Thumb->image_type_id
					                  );
				}
			}
		} else {
			switch ($data['file_type']) {
				case 'image/gif':
					imagegif($newImage, $data['full_path']);
					break;
				case 'image/jpeg':
					imagejpeg($newImage, $data['full_path'], 90);
					break;
				case 'image/png':
					imagepng($newImage, $data['full_path'], 9);
					break;
			}
			foreach ($ThumbDetails->result() as $Thumb) {
				$watermark = ($photowatermark ? str_replace('|', '', $photowatermark) : '');

				$_SESSION['img'][] = array('list'		=> count($_SESSION['img']),
				                           'type'		=> $Thumb->image_type_id,
				                           'codename'	=> $Thumb->image_type_codename);
				$output[] = array('title'  => $this->ci->input->post('title'.$form_value).' - '.$Thumb->image_type_name,
				                  'string' => '/tmp/uploads/'.$data['file_name'].'|'.$x.'|'.$y.'|'.$Thumb->image_type_id.'|'.count($output).'|'.$Thumb->image_type_width.'|'.$Thumb->image_type_height.'|'.$this->ci->input->post('title'.$form_value).'|'.$id.'-'.$Thumb->image_type_id.'|'.$watermark,
				                  'thumb_id' => $id.'-'.$Thumb->image_type_id
				                  );
			}
		}
		return $output;
	}
}
?>
