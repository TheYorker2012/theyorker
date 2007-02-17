<?php

/// Office Gallery
/**
 * @author Nick Evans (nse500@cs.york.ac.uk)
 *
 */
define('PHOTOS_PERPAGE', 12);

 class Gallery extends Controller
{
	/**
	 * @brief Default constructor.
	 */
	function __construct() {
		parent::Controller();
		$this->load->helper(array('url', 'images', 'entity'));
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
}

?>