<?php

class Upload extends Controller {
	
	function Upload() {
		parent::Controller();
		$this->load->helper(array('form', 'url'));
		$this->load->library('frame_public');
	}
	
	function index() {
		$this->frame_public->SetTitle('Upload Form');
		$this->frame_public->SetExtraHead('<script src="/javascript/clone.js" type="text/javascript"></script>');
		$this->frame_public->SetContentSimple('uploader/upload_form');
		$this->frame_public->Load();
	}

	function do_upload() {
		$this->load->library('upload');
//		$this->load->library('xajax');
//		$this->xajax->registerFunction(array("process_form_data", &$this, "process_form_data"));
//		$this->xajax->processRequests();
		
		//get data about thumbnails
		
		$config['upload_path'] = '/home/theyorker/public_html/tmp/uploads/';
		$config['allowed_types'] = 'gif|jpg|png|zip';
		$config['max_size']	= '2048';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		
		$data = array();
		$this->load->library('upload', $config);
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
					$data[$x - 1] = _processImage($data[$x - 1]);
				}
			}
		}
		$this->frame_public->SetTitle('Photo Cropper');
		$this->frame_public->SetExtraHead('');
		$this->frame_public->SetContentSimple('uploader/upload_cropper', array('data' => $data));
		$this->frame_public->Load();
	}
	
	function process_form_data($form_data) {
		$objResponse = new xajaxResponse();

		$objResponse->addAssign("submitButton","value","Save");
		$objResponse->addAssign("submitButton","disabled",false);

		$objResponse->addAssign("div_result", "innerHTML", $result);
		return $objResponse;
	}
	
	function _processImage($data) {
		return 'Image uploaded, now we must load the cropper, and also the details about the sizes to crop to.';
	}
}
?>