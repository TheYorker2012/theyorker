<?php

/// Office Gallery
/**
 * @author Mark Goodall (mg512@cs.york.ac.uk)
 *
 */
define('PHOTOS_PERPAGE', 12);
define('VIEW_WIDTH', 650);
define('BASE_DIR', '/home/theyorker/public_html');

 class Gallery extends Controller
{
	/**
	 * @brief Default constructor.
	 */
	function __construct() {
		parent::Controller();
		$this->load->helper(array('url', 'form', 'images', 'entity'));
	}
	
	function index() {
		if (!CheckPermissions('office')) return;
		
		$this->pages_model->SetPageCode('office_gallery');
		$count = $this->db->get('photos')->num_rows();
		if ($count > PHOTOS_PERPAGE) {
			$this->load->library('pagination');
			
			$config['base_url'] = site_url('office/gallery/');
			$config['total_rows'] = $count;
			$config['per_page'] = PHOTOS_PERPAGE;
			$config['uri_segment'] = 3;
			
			$this->pagination->initialize($config);
			$pageNumbers = $this->pagination->create_links();
		} else {
			$pageNumbers = '';
		}
		$page = $this->uri->segment(3, 0);
		
		if ($this->input->post('submit') == 'Clear') {
			$_SESSION['img_search'] = false;
		} elseif ($this->input->post('submit')) {
			$_SESSION['img_search'] = $this->input->post('search');
			$_SESSION['img_search_by'] = $this->input->post('searchcriteria');
			$_SESSION['img_search_order'] = $this->input->post('order');
			$_SESSION['img_tag'] = $this->input->post('tag');
			$_SESSION['img_photographer'] = $this->input->post('photographer');
		}
		
		if (isset($_SESSION['img_search']) and $_SESSION['img_search']) {
			$photos = $this->db->select('*')->from('photos');
			if ($_SESSION['img_search']) {
				switch($_SESSION['img_search_by']) {
					case "date":
						$photos = $photos->like('photo_timestamp', $_SESSION['img_search']);
					break;
					case "title":
						$photos = $photos->like('photo_title', $_SESSION['img_search']);
					break;
					case "photographer":
						//not implemented!!!
						$photos = $photos->like('photo_title', $_SESSION['img_search']);
					break;
				}
			}
			if ($_SESSION['img_tag'] != 'null') {
				$photos = $photos->join('photo_tags', 'photo_tags.photo_tag_photo_id = photos.photo_id')->where('photo_tags.photo_tag_tag_id', $_SESSION['img_tag']);
			}
			if ($_SESSION['img_photographer'] != 'null') {
				$photos = $photos->where('photo_author_user_entity_id', $_SESSION['img_photographer']);
			}
			if ($_SESSION['img_search_order']) {
				switch ($_SESSION['img_search_order']) {
					case "title":
						$photos = orderby('photo_title', 'desc');
					break;
					case "date":
						$photos = orderby('photo_timestamp', 'desc');
					break;
					case "photographer":
						$photos = orderby('photo_author_user_entity_id', 'desc');
					break;
				}
			}
			$photos = $photos->limit(PHOTOS_PERPAGE, $page * PHOTOS_PERPAGE)->get();
		} else {
			$photos = $this->db->get('photos', PHOTOS_PERPAGE, $page * PHOTOS_PERPAGE);
		}
		
		$data = array(
			'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
			'photos' => $photos->result()
		);
		
		// Set up the center div for the gallery.
		$gallery_div = $this->frames->view('office/gallery/gallerythumbs');
		$gallery_div->AddData($data);
		
		// Set up the subview for gallery.
		$frameData = array('photographer' => $this->db->getwhere('users', array('user_office_interface_id' => '2')),
		                   'tags' => $this->db->get('tags'),
		                   'pageNumbers' => $pageNumbers);
		$gallery_frame = $this->frames->frame('office/gallery/galleryframe');
		$gallery_frame->AddData($frameData);
		$gallery_frame->SetContent($gallery_div);

		// Set up the master frame.
		$this->main_frame->SetContent($gallery_frame);
		$this->main_frame->SetTitle('Photo Gallery');
	
		// Load the main frame
		$this->main_frame->Load();
	}
	
	function show()
	{
		if (!CheckPermissions('office')) return;
		
		$id = $this->uri->segment(4);
		
		if ($this->uri->segment(5) == 'save'
		    and !$this->input->post('title')
		    and !$this->input->post('date')
		    and !$this->input->post('photographer')) {
			
			$new = array('photo_title' => $this->input->post('title'),
			             'photo_timestamp' => $this->input->post('date'),
			             'photo_author_user_entity_id' =>  $this->input->post('photographer'),
			             'photo_deleted' => $this->input->post('hidden'));
			
			if ($this->input->post('onfrontpage') == TRUE) {
				$new['photo_homepage'] = "";
			} else {
				$new['photo_homepage'] = "NULL";
			}
			
			$this->db->where('photo_id', $id)->update('photos', $new);
		}
		
		$this->pages_model->SetPageCode('office_gallery');

		if ($id) {
			$data = array(
				'main_text' => $this->pages_model->GetPropertyWikitext('photo_view'),
				'photoDetails' => $this->db->getwhere('photos', array('photo_id' => $id), 1)->row(),
				'type' => $this->db->getwhere('image_types', array('image_type_photo_thumbnail' => '1'))->result(),
				'photoTag' => $this->db->from('tags')->join('photo_tags', 'photo_tags.photo_tag_tag_id = tags.tag_id')->where('photo_tags.photo_tag_photo_id', $id)->get(),
				'photographer' => $this->db->getwhere('users', array('user_office_interface_id' => '2'))
			);
		}
		
//		$test = $this->load->view('office/gallery/galleryimage', $data, true);
//		$data2 = array('pageNumbers' => '', 'test' => $test);
//		$this->load->view('office/gallery/galleryframe', $data2);
		
		// Set up the center div for the gallery.
		$gallery_div = $this->frames->view('office/gallery/galleryimage');
		$gallery_div->AddData($data);

		// Set up the subview for gallery.
		$frameData = array('photographer' => $data['photographer'],
		                   'tags' => $this->db->get('tags'),
		                   'pageNumbers' => '');
		$gallery_frame = $this->frames->frame('office/gallery/galleryframe');
		$gallery_frame->AddData($frameData);
		$gallery_frame->SetContent($gallery_div);

		// Set up the master frame.
		$this->main_frame->SetContent($gallery_frame);
		$this->main_frame->SetTitle('Photo Details');
	
		// Load the main frame
		$this->main_frame->Load();
	}
	
	function upload() {
		if (!CheckPermissions('office')) return;
		
		$_SESSION['img_list'] = array();
		
		$this->main_frame->SetTitle('Admins\'s Photo Uploader');
		$this->main_frame->SetExtraHead('<script src="/javascript/clone.js" type="text/javascript"></script>');
		$this->main_frame->SetContentSimple('uploader/admin_upload_form');
		$this->main_frame->Load();
	}
	
	function do_upload() {
		if (!CheckPermissions('office')) return;
		
		$this->load->library(array('image_lib', 'upload', 'xajax'));
		$this->load->helper('images');
		$this->xajax->registerFunction(array("process_form_data", &$this, "process_form_data"));
		$this->xajax->processRequests();
		
		//get data about thumbnails
		
		$config['upload_path'] = './tmp/uploads/';
		$config['allowed_types'] = 'jpg';
		$config['max_size'] = '2048';
		
		$query = $this->db->select('image_type_id, image_type_name, image_type_width, image_type_height')->getwhere('image_types', array('image_type_photo_thumbnail' => '1'));
		
		$data = array();
		$this->load->library('upload');
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
		$config['source_image'] = $selectedThumb[0];
		$config['width'] = $formData['width'];
		$config['height'] = $formData['height'];
		$config['maintain_ratio'] = FALSE;
		$config['new_image'] = imageLocation($selectedThumb[4], $selectedThumb[3], null, TRUE);
		$config['x_axis'] = $formData['x1'];
		$config['y_axis'] = $formData['y1'];
		
		$this->image_lib->initialize($config);

		if (!$this->image_lib->crop())
		{
//			die('The crop failed.');
			echo $this->image_lib->display_errors();
		}
		
		$config['source_image'] = $config['new_image'];
		$config['new_image'] = null;
		$config['width'] = $selectedThumb[5];
		$config['height'] = $selectedThumb[6];
		
		$this->image_lib->initialize($config);
		
		if (!$this->image_lib->resize())
		{
//			die('The resize failed.');
			echo $this->image_lib->display_errors();
		}
		
		$objResponse->addAssign("submitButton","value","Save");
		$objResponse->addAssign("submitButton","disabled",false);

		return $objResponse;
	}
	
	function _processImage($data, $form_value, &$ThumbDetails) {
		$config['image_library'] = 'gd2';
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
		rename ($data['full_path'], BASE_DIR.photoLocation($oneRow->photo_id, $data['file_ext'], TRUE));
		
		$_SESSION['img_list'][] = $oneRow->photo_id;
		
		$loop = 0;
		foreach ($ThumbDetails->result() as $Thumb) {
			$output[$loop]['title'] = $this->input->post('title'.$form_value).' - '.$Thumb->image_type_name;
			$output[$loop]['string'] = photoLocation($oneRow->photo_id, $data['file_ext']).'|'.$newDetails[0].'|'.$newDetails[1].'|'.$Thumb->image_type_id.'|'.$oneRow->photo_id.'|'.$Thumb->image_type_width.'|'.$Thumb->image_type_height;
			$loop++;
		}
		return $output;
	}
}

?>