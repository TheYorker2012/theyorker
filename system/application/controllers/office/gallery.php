<?php

/// Office Gallery
/**
 * @author Mark Goodall (mg512@cs.york.ac.uk)
 *
 */
define('PHOTOS_PERPAGE', 12);
define('VIEW_WIDTH', 650);

class Gallery extends Controller {
	/**
	 * @brief Default constructor.
	 */
	function __construct() {
		parent::Controller();
		$this->load->helper(array('url', 'form', 'entity'));
		$this->load->library('image');
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
		if (isset($_SERVER["HTTP_REFERER"])) {
			$bits = explode('/', $_SERVER["HTTP_REFERER"]);
			if ($page === 'return') {
				//some people won't approve of this method, but the user cannot harm anything messing this up and its short
				$_SESSION['img'][] = array('list' => $bits[6], 'type' => 'all');
				header('Location: '.$_SESSION['img_return']);
			} elseif (isset ($bits[4]) and $bits[4] != 'gallery') {
				$_SESSION['img_return'] = $_SERVER["HTTP_REFERER"];
			}
		}
		
		
		if ($this->input->post('clear') == 'clear') {
			unset($_SESSION['img_search']);
		} elseif ($this->input->post('submit')) {
			$_SESSION['img_search'] = $this->input->post('search');
			$_SESSION['img_search_by'] = $this->input->post('searchcriteria');
			$_SESSION['img_search_order'] = $this->input->post('order');
			$_SESSION['img_tag'] = $this->input->post('tag');
			$_SESSION['img_photographer'] = $this->input->post('photographer');
		}
		
		if (isset($_SESSION['img_search'])) {
			$photos = $this->db->select('photo_id, photo_timestamp, photo_author_user_entity_id, photo_title, photo_width, photo_height, photo_gallery, photo_complete, photo_deleted')->from('photos');
			if ($_SESSION['img_search']) {
				switch($_SESSION['img_search_by']) {
					case "date":
						$photos = $photos->like('photo_timestamp', $_SESSION['img_search']);
					break;
					case "title":
						$photos = $photos->like('photo_title', $_SESSION['img_search']);
					break;
					case "photographer":
						$photos = $photos->join('users', 'users.user_entity_id = photos.photo_author_user_entity_id');
						$photos = $photos->like('users.user_nickname', $_SESSION['img_search']);
						$photos = $photos->orlike('users.user_firstname', $_SESSION['img_search']);
						$photos = $photos->orlike('users.user_surname', $_SESSION['img_search']);
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
						$photos = $photos->orderby('photo_title', 'asc');
					break;
					case "date":
						$photos = $photos->orderby('photo_timestamp', 'desc');
					break;
					case "photographer":
						$photos = $photos->orderby('photo_author_user_entity_id', 'asc');
					break;
				}
			}
			$photos = $photos->limit(PHOTOS_PERPAGE, $page)->get();
		} else {
			$photos = $this->db->select('photo_id, photo_timestamp, photo_author_user_entity_id, photo_title, photo_width, photo_height, photo_gallery, photo_complete, photo_deleted')->get('photos', PHOTOS_PERPAGE, $page);
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
		                   'tags' => $this->db->getwhere('tags', array('tag_type' => 'photo')),
		                   'pageNumbers' => $pageNumbers);
		$gallery_frame = $this->frames->frame('office/gallery/galleryframe');
		$gallery_frame->AddData($frameData);
		$gallery_frame->SetContent($gallery_div);

		// Set up the master frame.
		$this->main_frame->SetTitle('Photo Gallery');
		$this->main_frame->SetContent($gallery_frame);
	
		// Load the main frame
		$this->main_frame->Load();
	}
	
	function show()
	{
		if (!CheckPermissions('office')) return;
		$this->load->library('xajax');
		$this->xajax->registerFunction(array("tag_suggest", &$this, "tag_suggest"));
		$this->xajax->processRequests();
		
		$id = $this->uri->segment(4);
		
		if ($this->uri->segment(5) == 'save'
		    and $this->input->post('title')
		    and $this->input->post('date')) {
			
			$new = array('photo_title' => $this->input->post('title'),
			             'photo_timestamp' => $this->input->post('date'),
			             'photo_author_user_entity_id' =>  $this->input->post('photographer'));
			
			if ($this->input->post('hidden') == 'hide') {
				$new['photo_deleted'] = "1";
			} else {
				$new['photo_deleted'] = "0";
			}
			$this->db->update('photos', $new, array('photo_id' => $id));
			
			//tags
			$this->db->delete('photo_tags', array('photo_tag_photo_id' => $id));
			//add
			if ($this->input->post('tags')) {
				$tagsRaw = explode('+', $this->input->post('tags'));
				array_pop($tagsRaw);
				foreach ($tagsRaw as $tag) {
					$tagSearch = $this->db->getwhere('tags', array('tag_name' => $tag, 'tag_type' => 'photo'));
					if ($tagSearch->num_rows() > 0) {
						//this is an existing tag
						foreach ($tagSearch->result() as $tagS) {
							$this->db->insert('photo_tags', array('photo_tag_photo_id' => $id, 'photo_tag_tag_id' => $tagS->tag_id));
						}
					} else {
						//this is a new tag
						$this->db->insert('tags', array('tag_name' => $tag, 'tag_type' => 'photo'));
						$newTag = $this->db->getwhere('tags', array('tag_name' => $tag, 'tag_type' => 'photo'));
						foreach ($newTag->result() as $Ntag) {
							$this->db->insert('photo_tags', array('photo_tag_photo_id' => $id, 'photo_tag_tag_id' => $Ntag->tag_id));
						}
					}
				}
			}
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
		
		// Set up the center div for the gallery.
		$gallery_div = $this->frames->view('office/gallery/galleryimage');
		$gallery_div->AddData($data);

		// Set up the subview for gallery.
		$frameData = array('photographer' => $data['photographer'],
		                   'tags' => $this->db->getwhere('tags', array('tag_type'=>'photo')),
		                   'pageNumbers' => '');
		$gallery_frame = $this->frames->frame('office/gallery/galleryframe');
		$gallery_frame->AddData($frameData);
		$gallery_frame->SetContent($gallery_div);

		// Set up the master frame.
		$head = $this->xajax->getJavascript(null, '/javascript/xajax.js');
		$head.= '<script src="/javascript/prototype.js" type="text/javascript"></script>';
		$this->main_frame->SetExtraHead($head);
		$this->main_frame->SetContent($gallery_frame);
		$this->main_frame->SetTitle('Photo Details');
	
		// Load the main frame
		$this->main_frame->Load();
	}
	
	function tag_suggest($tag) {
		$objResponse = new xajaxResponse();
		if ($tag == "") {
			$objResponse->addAssign("txt_result", "style.display", 'none');
			return $objResponse;
		}
		$tagSearch = $this->db->where('tag_type', 'photo')->like('tag_name', $tag)->get('tags');
		$reply = '';
		if ($tagSearch->num_rows() > 0) {
			foreach ($tagSearch->result() as $tag) {
				$reply.='<li id="'.$tag->tag_name.'"><a onClick="setTag(\''.$tag->tag_name.'\'); addTag()"><img src="/images/icons/add.png" title="add" alt="add"> '.$tag->tag_name.'</a></li>';
			}
			$objResponse->addAssign("ntags", "innerHTML", $reply);
		} else {
			$objResponse->addAssign("ntags", "innerHTML", '');
		}
		return $objResponse;
	}
	
	function upload() {
		if (!CheckPermissions('office')) return;
		
		$_SESSION['img'] = array();
		$this->load->library('image_upload');
		$this->image_upload->automatic('office/gallery', false, true, true);
	}
	
	private function _checkImageProperties(&$imgData, &$imgTypes) {
		foreach ($imgTypes->result() as $imgType) {
			if ($imgData['image_width'] < $imgType->image_type_width) return false;
			if ($imgData['image_height'] < $imgType->image_type_height) return false;
		}
		return true;
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
		$this->upload->initialize($config);
		for ($x = 1; $x <= $this->input->post('destination'); $x++) {
			if ( ! $this->upload->do_upload('userfile'.$x)) {
				$data[] = $this->upload->display_errors();
			} else {
				$data[] = $this->upload->data();
				if ($this->_checkImageProperties($data[$x - 1], $query))
					$data[$x - 1] = $this->_processImage($data[$x - 1], $x, $query);
			}
		}
		$this->main_frame->SetTitle('Gallery Photo Cropper');
		$head = $this->xajax->getJavascript(null, '/javascript/xajax.js');
		$head.= '<link rel="stylesheet" type="text/css" href="/stylesheets/cropper.css" media="all" /><script src="/javascript/prototype.js" type="text/javascript"></script><script src="/javascript/scriptaculous.js?load=builder,effects,dragdrop" type="text/javascript"></script><script src="/javascript/cropper.js" type="text/javascript"></script>';
		$this->main_frame->SetExtraHead($head);
		$this->main_frame->SetContentSimple('uploader/admin_upload_cropper', array('data' => $data, 'ThumbDetails' => &$query));
		$this->main_frame->Load();
	}
	
	function edit() {
		if (!CheckPermissions('office')) return;
		
		$this->load->library('image_upload');
		$this->xajax->processRequests();
		
		$output = array(); // hack to use same view
		$data = array();
		
		$id = $this->uri->segment(4);
		
		$photoDetails = $this->db->getwhere('photos', array('photo_id' => $id));
		$thumbDetails = $this->db->getwhere('image_types', array('image_type_photo_thumbnail' => '1'));
		
		$loop = 0;
		foreach ($photoDetails->result() as $Photo) {
			foreach ($thumbDetails->result() as $Thumb) {
				$output[$loop]['title'] = $Photo->photo_title.' - '.$Thumb->image_type_name;
				$output[$loop]['string'] = '/photos/full/'.$Photo->photo_id.'|'.$Photo->photo_width.'|'.$Photo->photo_height.'|'.$Thumb->image_type_id.'|'.$Photo->photo_id.'|'.$Thumb->image_type_width.'|'.$Thumb->image_type_height;
				$loop++;
			}
		}
		
		$data[] = $output;
		
		$this->main_frame->SetTitle('Admin\'s Photo Cropper');
		$head = $this->xajax->getJavascript(null, '/javascript/xajax.js');
		$head.= '<link rel="stylesheet" type="text/css" href="/stylesheets/cropper.css" media="all" /><script src="/javascript/prototype.js" type="text/javascript"></script><script src="/javascript/scriptaculous.js?load=builder,effects,dragdrop" type="text/javascript"></script><script src="/javascript/cropper.js" type="text/javascript"></script>';
		$this->main_frame->SetExtraHead($head);
		$this->main_frame->SetContentSimple('uploader/upload_cropper_new', array('returnPath' => '/office/gallery/show/'.$Photo->photo_id, 'data' => $data, 'ThumbDetails' => &$thumbDetails, 'type' => true));
		$this->main_frame->Load();
	}
}

?>
