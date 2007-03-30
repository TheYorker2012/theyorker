<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
define('PHOTOS_PERPAGE', 12);
define('VIEW_WIDTH', 650);
define('BASE_DIR', '/home/theyorker/public_html');

class Image_upload {
	
	private $ci;
	
	public function Image_upload() {
		$this->ci = &get_instance();
	}
	
	public function uploadForm($multiple = false, $photos = false) {
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
			$this->ci->main_frame->SetTitle('Photo Uploader');
			$this->ci->main_frame->SetContentSimple('uploader/upload_single_photo');
		} else {
			$this->ci->main_frame->SetTitle('Image Uploader');
			$this->ci->main_frame->SetContentSimple('uploader/upload_single_image');
		}
		$this->main_frame->Load();
	}
	
	//types is an array
	public function recieveUpload($returnPath, $types = false) {
		$this->ci->load->library(array('image_lib', 'upload', 'xajax'));
		$this->ci->load->helper('images');
		$this->ci->xajax->registerFunction(array("process_form_data", &$this, "process_form_data"));
		$this->ci->xajax->processRequests();
		
		//get data about thumbnails
		
		$config['upload_path'] = './tmp/uploads/';
		$config['allowed_types'] = 'jpg';
		$config['max_size'] = '2048';
		
		if (is_array($types)) {
			$query = $this->ci->db->select('image_type_id, image_type_name, image_type_width, image_type_height');
			$query = $query->where('image_type_photo_thumbnail', '1');
			$type = array_pop($types);
			$query = $query->where('image_type_codename', $type);
			foreach ($types as $type) {
				$query = $query-orwhere('image_type_codename', $type);
			}
			$query = $query->get('image_types');
		} else {
			$query = $this->ci->db->select('image_type_id, image_type_name, image_type_width, image_type_height')->getwhere('image_types', array('image_type_photo_thumbnail' => '1'));
		}
		
		$data = array();
		$this->ci->upload->initialize($config);
		for ($x = 1; $x <= $this->ci->input->post('destination'); $x++) {
			if ( ! $this->ci->upload->do_upload('userfile'.$x)) {
				$data[] = $this->ci->upload->display_errors();
			} else {
				$data[] = $this->ci->upload->data();
				$data[$x - 1] = $this->processImage($data[$x - 1], $x, $query);
			}
		}
		$this->ci->main_frame->SetTitle('Photo Uploader');
		$head = $this->ci->xajax->getJavascript(null, '/javascript/xajax.js');
		$head.= '<link rel="stylesheet" type="text/css" href="stylesheets/cropper.css" media="all" /><script src="javascript/prototype.js" type="text/javascript"></script><script src="javascript/scriptaculous.js?load=builder,effects,dragdrop" type="text/javascript"></script><script src="javascript/cropper.js" type="text/javascript"></script>';
		$this->ci->main_frame->SetExtraHead($head);
		$this->ci->main_frame->SetContentSimple('uploader/upload_cropper_new', array('returnpath' => $returnpath, 'data' => $data, 'ThumbDetails' => &$query));
		return $this->ci->main_frame->Load();
	}
	
	public function process_form_data($formData) {
		if (!CheckPermissions('office')) return; //keep this for now...

		$objResponse = new xajaxResponse();
		$this->ci->load->library('image_lib');

		$selectedThumb = explode("|", $formData['imageChoice']);

		if (!createImageLocationFromId($selectedThumb[4], $selectedThumb[3])) {
			$objResponse->addAssign("submitButton","value","Error: Location not created");
			$objResponse->addAssign("submitButton","disabled",false);

			return $objResponse;
		}

		$config['image_library'] = 'netpbm';
		$config['library_path'] = '/usr/bin/';
		$config['source_image'] = BASE_DIR.$selectedThumb[0];
		$config['width'] = $formData['width'];
		$config['height'] = $formData['height'];
		$config['maintain_ratio'] = FALSE;
		$config['new_image'] = BASE_DIR.imageLocationFromId($selectedThumb[4], $selectedThumb[3], null, TRUE);
		$config['x_axis'] = $formData['x1'];
		$config['y_axis'] = $formData['y1'];

		$this->ci->image_lib->initialize($config);

		if (!$this->ci->image_lib->crop())
		{
//			die('The crop failed.');
//			echo $config['source_image'];
			echo $this->ci->image_lib->display_errors();
		}

		$config['source_image'] = BASE_DIR.imageLocationFromId($selectedThumb[4], $selectedThumb[3], null, TRUE);
		unset($config['new_image']);
		$config['width'] = $selectedThumb[5];
		$config['height'] = $selectedThumb[6];

		$this->ci->image_lib->initialize($config);

		if (!$this->ci->image_lib->resize())
		{
//			die('The resize failed.');
//			echo $config['source_image'];
			echo $this->ci->image_lib->display_errors();
		}

		$objResponse->addAssign("submitButton","value","Save");
		$objResponse->addAssign("submitButton","disabled",false);

		return $objResponse;
		
	}

	private function processImage($data, $form_value, &$ThumbDetails) {
		$config['image_library'] = 'gd2';
		$config['source_image'] = $data['full_path'];
		$config['quality'] = 85;
		$config['master_dim'] = 'width';
		$config['width'] = VIEW_WIDTH;
		$config['height'] = 1000;
		
		$output = array();
		
		$this->ci->image_lib->initialize($config);
		if ($data['image_width'] > 650) {
			if (!$this->ci->image_lib->resize()) {
				$output[]['title']= $this->ci->image_lib->display_errors();
				return $output;
			}
		}
		$newDetails = getimagesize($data['full_path']);

		$row_values = array ('photo_author_user_entity_id' => $this->ci->user_auth->entityId,
		                     'photo_title' => $this->ci->input->post('title'.$form_value),
		                     'photo_width' => $newDetails[0],
		                     'photo_height' => $newDetails[1]);
		$this->ci->db->insert('photos', $row_values);
		$query = $this->ci->db->select('photo_id')->getwhere('photos', $row_values, 1);
		
		$oneRow = $query->row();
		createImageLocation($oneRow->photo_id);
		rename ($data['full_path'], BASE_DIR.photoLocation($oneRow->photo_id, $data['file_ext'], TRUE));
		
		$_SESSION['img_list'][] = $oneRow->photo_id;
		
		$loop = 0;
		foreach ($ThumbDetails->result() as $Thumb) {
			$output[$loop]['title'] = $this->ci->input->post('title'.$form_value).' - '.$Thumb->image_type_name;
			$output[$loop]['string'] = photoLocation($oneRow->photo_id, $data['file_ext']).'|'.$newDetails[0].'|'.$newDetails[1].'|'.$Thumb->image_type_id.'|'.$oneRow->photo_id.'|'.$Thumb->image_type_width.'|'.$Thumb->image_type_height;
			$loop++;
		}
		return $output;
	}

}

?>