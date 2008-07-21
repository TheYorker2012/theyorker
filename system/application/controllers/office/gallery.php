<?php

/**
 *	@brief		Office Gallery
 *	@author		Mark Goodall (mg512@cs.york.ac.uk)
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 */

define('PHOTOS_PERPAGE', 40);
define('VIEW_WIDTH', 600);

class Gallery extends Controller {

	function __construct()
	{
		parent::Controller();
		$this->load->helper(array('url', 'form', 'entity'));
		$this->load->library('image');
		$this->load->model('photos_model');
	}

	function index() {
		if (!CheckPermissions('office')) return;

		$page = $this->uri->segment(3, 0);
		if (isset($_SERVER["HTTP_REFERER"])) {
			$bits = explode('/', $_SERVER["HTTP_REFERER"]);
			if (($page === 'return') && (isset($_SESSION['img_return']))) {
				//some people won't approve of this method, but the user cannot harm anything messing this up and its short
				$_SESSION['img'][] = array('list' => $bits[6], 'type' => 'all');
				redirect($_SESSION['img_return']);
			} elseif (isset ($bits[4]) and $bits[4] != 'gallery') {
				$_SESSION['img_return'] = $_SERVER["HTTP_REFERER"];
			}
		}

		$this->pages_model->SetPageCode('office_gallery');

		if (!isset($_SESSION['gallery_search'])) {
			$_SESSION['gallery_search'] = array(
				'view' => 'icons'
			);
		}

		if ($this->input->post('search_button')) {
			$_SESSION['gallery_search']['term'] = $this->input->post('search');
		}

		if ((isset($_SESSION['gallery_search']['term'])) && ($_SESSION['gallery_search']['term'] != '')) {
			$terms = explode(' ', $_SESSION['gallery_search']['term']);
			$results_title = 'search results...';
		} else {
			$terms = array();
			$results_title = 'recently uploaded...';
		}
		$photos = $this->photos_model->GallerySearch ($terms, $page, PHOTOS_PERPAGE, false);
		$count = $this->photos_model->GallerySearch ($terms, $page, PHOTOS_PERPAGE, true);
		$this->load->library('pagination');
		$config['base_url'] = site_url('office/gallery');
		$config['total_rows'] = $count;
		$config['per_page'] = PHOTOS_PERPAGE;
		$config['num_links'] = 2;
		$config['full_tag_open'] = '<div class="Pagination">';
		$config['full_tag_close'] = '</div>';
		$config['next_tag_open'] = '<span class="direction">';
		$config['next_tag_close'] = '</span>';
		$config['prev_tag_open'] = '<span class="direction">';
		$config['prev_tag_close'] = '</span>';
		$config['cur_tag_open'] = '<span class="selected">';
		$config['cur_tag_close'] = '</span>';
		$config['num_tag_open'] = '<span>';
		$config['num_tag_close'] = '</span>';
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);
		$pageNumbers = $this->pagination->create_links();

		$data = array(
			'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
			'photos' => $photos
		);

		// Set up the center div for the gallery.
		$gallery_div = $this->frames->view('office/gallery/results_' . $_SESSION['gallery_search']['view']);
		$gallery_div->AddData($data);

		// Set up the subview for gallery.
		$frameData = array(
			'photographer' => $this->db->getwhere('users', array('user_office_interface_id' => '2')),
			'tags' => $this->db->getwhere('tags', array('tag_type' => 'photo')),
			'pageNumbers' => $pageNumbers,
			'total' => $count,
			'offset' => $page,
			'perpage' => PHOTOS_PERPAGE,
			'title' => $results_title
		);
		$gallery_frame = $this->frames->frame('office/gallery/galleryframe');
		$gallery_frame->AddData($frameData);
		$gallery_frame->SetContent($gallery_div);

		// Set up the master frame.
		$this->main_frame->IncludeCss('stylesheets/gallery.css');
		$this->main_frame->SetTitle('Photo Gallery');
		$this->main_frame->SetContent($gallery_frame);

		// Load the main frame
		$this->main_frame->Load();
	}

	function change_view ($view = 'icons')
	{
		if (!CheckPermissions('office')) return;
		$_SESSION['gallery_search']['view'] = $view;
		redirect('/office/gallery');
	}

	function change_tag ($tag = NULL)
	{
		if (!CheckPermissions('office')) return;
		if ($tag !== NULL) {
			if (!isset($_SESSION['gallery_search']))
				$_SESSION['gallery_search'] = array();
			$_SESSION['gallery_search']['term'] = $tag;
		}
		redirect('/office/gallery');
	}

	function show($id = NULL)
	{
		if (!CheckPermissions('office')) return;

		if ($id === NULL)
			show_404();
		$data = array();
		$data['photo'] = $this->photos_model->GetOriginalPhotoProperties($id);
		if (!$data['photo'])
			show_404();

		$this->load->library('xajax');
		$this->xajax->registerFunction(array("tag_suggest", &$this, "tag_suggest"));
		$this->xajax->processRequests();

		if ($this->input->post('save_photo_details')) {
			$_POST['hidden'] = (isset($_POST['hidden'])) ? 1 : 0;
			$_POST['hidden-gallery'] = (isset($_POST['hidden-gallery'])) ? 1 : 0;
			$this->photos_model->UpdatePhotoDetails($id, $_POST['title'], $_POST['watermark'], $_POST['hidden'], $_POST['hidden-gallery']);
			$this->main_frame->AddMessage('success', 'The photo details were successfully updated. Please note that if the watermark text was changed this will not appear on any thumbnails until they are re-cropped!');
			redirect('/office/gallery/show/' . $id);
		}


		$this->pages_model->SetPageCode('office_gallery');
		$data['main_text'] = $this->pages_model->GetPropertyWikitext('photo_view');
		$data['image_types'] = $this->photos_model->GetAllTypesInfo();
		$data['photo_tags'] = $this->photos_model->GetPhotosTags($id);

		$this->main_frame->SetTitle('Photo Gallery');
		$this->main_frame->IncludeJs('javascript/prototype.js');
		$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));
		$this->main_frame->IncludeCss('stylesheets/gallery.css');
		$this->main_frame->SetContentSimple('office/gallery/galleryimage', $data);
		$this->main_frame->Load();
	}

	function tag_suggest($tag) {
		$objResponse = new xajaxResponse();
		if ($tag != '') {
			$tag_details = $this->photos_model->GetTagDetails($tag);
			if (!$tag_details) {
				// Tag doesn't already exist so add it
				$tag_id = $this->photos_model->AddPhotoTag($tag);
			} else {
				$tag_id = $tag_details->tag_id;
			}
			// Associate photo with tag
			$this->photos_model->AssociatePhotoTag($this->uri->segment(4), $tag_id);
			// Return all tags
			$tags = $this->photos_model->GetPhotosTags($this->uri->segment(4));
			$objResponse->addScriptCall('clearTags');
			foreach ($tags as $tag) {
				$objResponse->addScriptCall('createTag', xml_escape($tag['tag_name']));
			}
		}
		$objResponse->addScriptCall('processTag');
		return $objResponse;
	}

	function upload() {
		if (!CheckPermissions('office')) return;

		$_SESSION['img'] = array();
		$this->load->library('image_upload');
		$this->image_upload->automatic('/office/gallery', false, true, true);
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
				$output[$loop]['string'] = '/photos/full/'.$Photo->photo_id.'|'.$Photo->photo_width.'|'.$Photo->photo_height.'|'.$Thumb->image_type_id.'|'.$Photo->photo_id.'|'.$Thumb->image_type_width.'|'.$Thumb->image_type_height.'|'.str_replace('|', '', $Photo->photo_title).'|'.$Photo->photo_id.'-'.$Thumb->image_type_id.'|';
				$output[$loop]['thumb_id'] = $Photo->photo_id.'-'.$Thumb->image_type_id;
				$output[$loop]['cache_img'] = '/photos/full/'.$Photo->photo_id;
				$loop++;
			}
		}

		$data[] = $output;

		$this->main_frame->SetTitle('Photo Recropper');
		$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));
		$this->main_frame->IncludeCss('stylesheets/cropper.css');
		$this->main_frame->IncludeJs('javascript/prototype.js');
		$this->main_frame->IncludeJs('javascript/scriptaculous.js?load=builder,effects,dragdrop');
		$this->main_frame->IncludeJs('javascript/cropper.js');
		$this->main_frame->SetContentSimple('uploader/upload_cropper_new', array('returnPath' => '/office/gallery/show/'.$Photo->photo_id, 'data' => $data, 'noforcesave' => true,  'ThumbDetails' => &$thumbDetails, 'type' => true));
		$this->main_frame->Load();
	}
}

?>