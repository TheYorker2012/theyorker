<?php

class Upload extends Controller {
	
	function Upload() {
		parent::Controller();
		$this->load->helper(array('form', 'url'));
		$this->load->library('frame_public');
	}
	
	function _processImage($data, $form_value) {
		$output = 'Image Success:';
		$config['image_library'] = 'GD2';
		$config['source_image'] = $data['full_path'];
		$config['quality'] = 75;
		$config['master_dim'] = 'width';
		$config['width'] = 650;
		$config['height'] = 1000;
		
		$this->image_lib->initialize($config);
		if (!$this->image_lib->resize()) {
			$output.= $this->image_lib->display_errors();
			return $output;
		}
		$newDetails = getimagesize($data['full_path']);

		$row_values = array ('photo_author_user_entity_id' => '1',
		                     'photo_title' => $this->input->post('title'.$form_value),
		                     'photo_width' => $newDetails[0],
		                     'photo_height' => $newDetails[1],
		                     'photo_gallery' => $this->input->post('gallery'.$form_value));
		$this->db->insert('photos', $row_values);
		$query = $this->db->select('photo_id')->getwhere('photos', $row_values, 1);
		
		$oneRow = $query->row();
		createImageLocation($oneRow->photo_id);
		rename ($data['full_path'], photoLocation($oneRow->photo_id, $data['file_ext'], TRUE));
		
		return $output.'Image uploaded, now we must load the cropper, and also the details about the sizes to crop to.';
	}
	
	function index() {
		$this->frame_public->SetTitle('Upload Form');
		$this->frame_public->SetExtraHead('<script src="/javascript/clone.js" type="text/javascript"></script>');
		$this->frame_public->SetContentSimple('uploader/upload_form');
		$this->frame_public->Load();
	}

	function do_upload() {
		$this->load->library('image_lib');
		$this->load->library('upload');
		$this->load->library('xajax');
		$this->load->helper('images');
		$this->xajax->registerFunction(array("process_form_data", &$this, "process_form_data"));
		$this->xajax->processRequests();
		
		//get data about thumbnails
		
		$config['upload_path'] = './tmp/uploads/';
		$config['allowed_types'] = 'gif|jpg|png|zip';
		$config['max_size']	= '2048';
		
		$data = array();
		$this->load->library('upload', $config); // this config call is clearly not working!!! I hate it
		$this->upload->initialize($config);
		for ($x = 1; $x <= $this->input->post('destination'); $x++) {
			if ( ! $this->upload->do_upload('userfile'.$x)) {
				$data[] = $this->upload->display_errors();
			} else {
				$data[] = $this->upload->data();
				if ($data[$x - 1]['file_ext'] == '.zip') {
					// TODO Zip support
					trigger_error("No Zip Support yet...");
				} else {
					$data[$x - 1] = $this->_processImage($data[$x - 1], $x-1);
				}
			}
		}
		$this->frame_public->SetTitle('Photo Cropper');
		$head = $this->xajax->getJavascript(null, '/javascript/xajax.js');
		$head.= '\n<link rel="stylesheet" type="text/css" href="stylesheets/cropper.css" media="all" /><script src="javascript/prototype.js" type="text/javascript"></script><script src="javascript/scriptaculous.js?load=builder,effects,dragdrop" type="text/javascript"></script><script src="javascript/cropper.js" type="text/javascript"></script>';
		$this->frame_public->SetExtraHead($head);
		$this->frame_public->SetContentSimple('uploader/upload_cropper', array('data' => $data));
		$this->frame_public->Load();
	}
	
	function process_form_data($form_data) {
		// there is no hack protection on this, i'm pretty sure
		$objResponse = new xajaxResponse();
		//store to imagestable
		$objResponse->addAssign("submitButton","value","Save");
		$objResponse->addAssign("submitButton","disabled",false);

		return $objResponse;
	}
	
}
?>