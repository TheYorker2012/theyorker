<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Image_upload {
	
	private $ci;
	
	public function Image_upload() {
		$this->ci = &get_instance();
	}
	
	public function recieveUpload($returnPath, $types = false) {
		if (!CheckPermissions('office')) return;
		
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
				$data[$x - 1] = $this->_processImage($data[$x - 1], $x, $query);
			}
		}
		$this->ci->main_frame->SetTitle('Photo Uploader');
		$head = $this->xajax->getJavascript(null, '/javascript/xajax.js');
		$head.= '<link rel="stylesheet" type="text/css" href="stylesheets/cropper.css" media="all" /><script src="javascript/prototype.js" type="text/javascript"></script><script src="javascript/scriptaculous.js?load=builder,effects,dragdrop" type="text/javascript"></script><script src="javascript/cropper.js" type="text/javascript"></script>';
		$this->ci->main_frame->SetExtraHead($head);
		$this->ci->main_frame->SetContentSimple('uploader/admin_upload_cropper', array('data' => $data, 'ThumbDetails' => &$query));
		$this->ci->main_frame->Load();
	}
	
	function process_form_data($formData) {
		if (!CheckPermissions('office')) return;

		$objResponse = new xajaxResponse();
		$this->load->library('image_lib');

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

		$this->image_lib->initialize($config);

		if (!$this->image_lib->crop())
		{
//			die('The crop failed.');
//			echo $config['source_image'];
			echo $this->image_lib->display_errors();
		}

		$config['source_image'] = BASE_DIR.imageLocationFromId($selectedThumb[4], $selectedThumb[3], null, TRUE);
		unset($config['new_image']);
		$config['width'] = $selectedThumb[5];
		$config['height'] = $selectedThumb[6];

		$this->image_lib->initialize($config);

		if (!$this->image_lib->resize())
		{
//			die('The resize failed.');
//			echo $config['source_image'];
			echo $this->image_lib->display_errors();
		}

		$objResponse->addAssign("submitButton","value","Save");
		$objResponse->addAssign("submitButton","disabled",false);

		return $objResponse;
		
	}

}

?>