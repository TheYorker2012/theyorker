<?php

define('PHOTOS_PERPAGE', 18);
define('PHOTOS_PERROW', 3);
define('VIEW_WIDTH', 650);

class image extends Controller {

	function images() {
		parent::Controller();
		$this->load->helper(array('form', 'url')); // possibly not both required?
	}

	function _processImage($data, $form_value, &$ThumbDetails) {
		$config['image_library'] = 'GD2';
		$config['source_image'] = $data['full_path'];
		$config['quality'] = 85;
		$config['master_dim'] = 'width';
		$config['width'] = VIEW_WIDTH;
		$config['height'] = 1000;
		
		$output = array();
		
		$this->image_lib->initialize($config);
		if ($data['image_width'] > 650) {
			if (!$this->image_lib->resize()) {
				$output[]['title']= $this->image_lib->display_errors();
				return $output;
			}
		}
		$newDetails = getimagesize($data['full_path']);

		$row_values = array ('photo_author_user_entity_id' => $this->user_auth->entityId,
		                     'photo_title' => $this->input->post('title'.$form_value),
		                     'photo_width' => $newDetails[0],
		                     'photo_height' => $newDetails[1]);
		$this->db->insert('photos', $row_values);
		$query = $this->db->select('photo_id')->getwhere('photos', $row_values, 1);
		
		$oneRow = $query->row();
		createImageLocation($oneRow->photo_id);
		rename ($data['full_path'], photoLocation($oneRow->photo_id, $data['file_ext'], TRUE));
		
		$loop = 0;
		foreach ($ThumbDetails->result() as $Thumb) {
			$output[$loop]['title'] = $this->input->post('title'.$form_value).' - '.$Thumb->image_type_name;
			$output[$loop]['string'] = photoLocation($oneRow->photo_id, $data['file_ext']).'|'.$newDetails[0].'|'.$newDetails[1].'|'.$Thumb->image_type_id.'|'.$oneRow->photo_id.'|'.$Thumb->image_type_width.'|'.$Thumb->image_type_height;
			$loop++;
		}
		return $output;
	}

	function index() {
		if (!CheckPermissions('admin')) return;
		$this->load->library('pagination');
		$this->load->helper('images');
		
		$config['base_url'] = site_url('admin/images/');
		
		$data = array();
		
		$allPhotos = $this->db->getwhere('photos', array('photo_deleted' => '0'));
		$totalPhotos = $allPhotos->num_rows();
		
		$image_type = $this->db->where('image_type_width <=', VIEW_WIDTH/3)->orderby('image_type_width', 'desc')->get('image_types', 1);
		$data['imageType'] = $image_type->row();
		
		if ($totalPhotos > PHOTOS_PERPAGE) {
			$data['shownPhotos'] = $this->db->getwhere('photos', array('photo_deleted' => 0), PHOTOS_PERPAGE, $this->uri->segment(3, 0) * PHOTOS_PERPAGE);
			
			$config['total_rows'] = $totalPhotos;
			$config['per_page'] = PHOTOS_PERPAGE;
			$this->pagination->initialize($config);
			
			$data['pages'] = $this->pagination->create_links();
		} elseif ($totalPhotos == 0) {
			$data['shownPhotos'] = false;
			$data['pages'] = '';
		} else {
			$data['pages'] = '';
			$data['shownPhotos'] = $this->db->getwhere('photos', array('photo_deleted' => 0));
		}
		
		$this->main_frame->SetTitle('Admins\'s Photo Management System');
		$this->main_frame->SetContentSimple('admin/images_index', $data);
		$this->main_frame->Load();
	}

	function upload() {
		if (!CheckPermissions('admin')) return;
		
		$this->main_frame->SetTitle('Admins\'s Photo Uploader');
		$this->main_frame->SetExtraHead('<script src="/javascript/clone.js" type="text/javascript"></script>');
		$this->main_frame->SetContentSimple('uploader/admin_upload_form');
		$this->main_frame->Load();
	}
	
	function do_upload() {
		if (!CheckPermissions('admin')) return;
		
		$this->load->library('image_lib');
		$this->load->library('upload');
		$this->load->library('xajax');
		$this->load->helper('images');
		$this->xajax->registerFunction(array("process_form_data", &$this, "process_form_data"));
		$this->xajax->processRequests();
		
		//get data about thumbnails
		
		$config['upload_path'] = './tmp/uploads/';
		$config['allowed_types'] = 'jpg';
		$config['max_size'] = '2048';
		
		$query = $this->db->select('image_type_id, image_type_name, image_type_width, image_type_height')->getwhere('image_types', array('image_type_photo_thumbnail' => '1'));
		
		$data = array();
		$this->load->library('upload', $config); // this config call is clearly not working!!! I hate it
		$this->upload->initialize($config);
		for ($x = 1; $x <= $this->input->post('destination'); $x++) {
			if ( ! $this->upload->do_upload('userfile'.$x)) {
				$data[] = $this->upload->display_errors();
			} else {
				$data[] = $this->upload->data();
				$data[$x - 1] = $this->_processImage($data[$x - 1], $x, $query);
			}
		}
		$this->main_frame->SetTitle('Admin\'s Photo Cropper');
		$head = $this->xajax->getJavascript(null, '/javascript/xajax.js');
		$head.= '<link rel="stylesheet" type="text/css" href="stylesheets/cropper.css" media="all" /><script src="javascript/prototype.js" type="text/javascript"></script><script src="javascript/scriptaculous.js?load=builder,effects,dragdrop" type="text/javascript"></script><script src="javascript/cropper.js" type="text/javascript"></script>';
		$this->main_frame->SetExtraHead($head);
		$this->main_frame->SetContentSimple('uploader/admin_upload_cropper', array('data' => $data, 'ThumbDetails' => &$query));
		$this->main_frame->Load();
	}
	
	function process_form_data($formData) {
		if (!CheckPermissions('admin')) return;

		$objResponse = new xajaxResponse();
		$this->load->library('image_lib');
		
		$selectedThumb = explode("|", $formData['imageChoice']);
		
		$imageData = array('image_photo_id' => $selectedThumb[4],
		                   'image_image_type_id' => $selectedThumb[3]);
		$query = $this->db->select('image_id')->getwhere('images', $imageData);
		if ($query->num_rows() > 0) {
			$this->db->delete('images', $imageData);
		}
		$this->db->insert('images', $imageData);
		$query = $this->db->select('image_id')->getwhere('images', $imageData);
		
		foreach ($query->result() as $singleRow) {
			$id = $singleRow->image_id;
		}
		
		if (!createImageLocation($id, $selectedThumb[3])) {
			$objResponse->addAssign("submitButton","value","Error: Location not created");
			$objResponse->addAssign("submitButton","disabled",false);

			return $objResponse;
		}
		
		$config['image_library'] = 'gd2';
		$config['source_image'] = $selectedThumb[0];
		$config['width'] = $formData['width'];
		$config['height'] = $formData['height'];
		$config['maintain_ratio'] = FALSE;
		$config['new_image'] = imageLocation($id, $selectedThumb[3], null, TRUE);
		$config['x_axis'] = $formData['x1'];
		$config['y_axis'] = $formData['y1'];
		
		$this->image_lib->initialize($config);

		if (!$this->image_lib->crop())
		{
		    echo $this->image_lib->display_errors();
		}
		
		$config['source_image'] = $config['new_image'];
		$config['new_image'] = null;
		$config['width'] = $selectedThumb[5];
		$config['height'] = $selectedThumb[6];
		
		$this->image_lib->initialize($config);
		
		if (!$this->image_lib->resize())
		{
		    echo $this->image_lib->display_errors();
		}
		
		$objResponse->addAssign("submitButton","value","Save");
		$objResponse->addAssign("submitButton","disabled",false);

		return $objResponse;
	}

}
?>
