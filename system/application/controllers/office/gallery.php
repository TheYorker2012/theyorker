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
		$photos = $this->db->get('photos', PHOTOS_PERPAGE, $page * PHOTOS_PERPAGE);
		
		$data = array(
			'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
			'photos' => $photos->result()
		);
		
		// Set up the center div for the gallery.
		$gallery_div = $this->frames->view('office/gallery/gallerythumbs');
		$gallery_div->AddData($data);
		
		// Set up the subview for gallery.
		$frameData = array('photographer' => $this->db->getwhere('users', array('user_office_interface_id' => '2'))->result(),
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
		
		$this->pages_model->SetPageCode('office_gallery');
		$id = $this->uri->segment(4);
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
		                   'tags' => $this->db->get('tags')->result());
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