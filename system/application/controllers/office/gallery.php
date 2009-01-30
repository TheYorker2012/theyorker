<?php

/**
 *	@brief		Office Gallery
 *	@author		Mark Goodall (mg512@cs.york.ac.uk)
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 */

define('PHOTOS_PERPAGE', 66);
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
				header('Location: ' . $_SESSION['img_return']);
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

		if (isset($_POST['search'])) {
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
		$data['watermark_colours'] = $this->photos_model->GetWatermarkColours();

		$this->load->library('xajax');
		$this->xajax->registerFunction(array("tag_suggest", &$this, "tag_suggest"));
		$this->xajax->processRequests();

		if ($this->input->post('save_photo_details')) {
			if ($_POST['title'] == '') {
				$this->main_frame->AddMessage('error', 'Please enter a title for this photo.');
			} elseif ($_POST['source'] == '') {
				$this->main_frame->AddMessage('error', 'Please specify the source of this photo.');
			} elseif (!isset($data['watermark_colours'][$_POST['watermark_colour']])) {
				$this->main_frame->AddMessage('error', 'Please select a valid colour for the watermark text.');
			} else {
				$_POST['hidden'] = (isset($_POST['hidden'])) ? 1 : 0;
				$_POST['hidden-gallery'] = (isset($_POST['hidden-gallery'])) ? 1 : 0;
				$_POST['public_gallery'] = (isset($_POST['public_gallery'])) ? 1 : 0;
				$this->photos_model->UpdatePhotoDetails($id, $_POST['title'], $_POST['source'], $_POST['watermark'], $_POST['watermark_colour'], $_POST['hidden'], $_POST['hidden-gallery'], $_POST['public_gallery']);
				$this->photos_model->ResetThumbnails($id);
				$this->main_frame->AddMessage('success', 'The photo details were successfully updated.');
			}
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

	function mass_upload() {
		if (!CheckPermissions('office')) return;

		$data = array();
		$this->pages_model->SetPageCode('office_gallery_massupload');
		$data['intro_heading'] = $this->pages_model->GetPropertyText('intro_heading');
		$data['intro_text'] = $this->pages_model->GetPropertyWikiText('intro_text');
		$data['watermark_colours'] = $this->photos_model->GetWatermarkColours();
		$preview_width = 100;
		$preview_height = 75;

		$this->load->model('static_model');
		$exts = array('jpg', 'jpeg', 'png', 'gif');
		$data['files'] = array();
		foreach ($this->static_model->GetDirectoryListing($this->config->item('static_local_path') . '/photos', '', $exts) as $file) {
			$data['files'][] = $file;
		}

		$this->main_frame->SetContentSimple('office/gallery/massupload', $data);
		$this->main_frame->IncludeCss('stylesheets/gallery.css');
		$this->main_frame->AddExtraHead('<style type="text/css">div#massupload_selection div { height: ' . $preview_height . 'px; width: ' . $preview_width . 'px; }</style>');

		$this->main_frame->Load();
	}

	function mass_upload_process () {
		if (!CheckPermissions('office')) return;

		// Workaround if function not available in version of PHP on server
		if (!function_exists('exif_imagetype')) {
			function exif_imagetype($filename) {
				if ((list($width, $height, $type, $attr) = getimagesize($filename)) !== false) {
					return $type;
				}
				return false;
			}
		}

		if (isset($_POST['selected_photos'])) {
			$photo_count = 0;
			foreach ($_POST['selected_photos'] as $photo) {
				$image = null;
				$image_path = $this->config->item('static_local_path') . '/photos/' . $photo;
				$image_mime = image_type_to_mime_type(exif_imagetype($image_path));
				switch ($image_mime) {
					case 'image/gif':
						$image = imagecreatefromgif($image_path);
						break;
					case 'image/jpeg':
						$image = imagecreatefromjpeg($image_path);
						break;
					case 'image/png':
						$image = imagecreatefrompng($image_path);
						break;
				}
				if ($image === null) {
					$this->main_frame->AddMessage('error', 'Unable to process ' . $photo);
				} elseif ($this->input->post('p_title'.$photo_count) == '') {
					$this->main_frame->AddMessage('error', 'Unable to add ' . $photo . ' to the gallery as you failed to specify a title for the photo.');
				} elseif ($this->input->post('p_photo_source'.$photo_count) == '') {
					$this->main_frame->AddMessage('error', 'Unable to add ' . $photo . ' to the gallery as you failed to specify the source for the photo.');
				} else {
					$x = imagesx($image);
					$y = imagesy($image);
					$info = array(
						'author_id'				=> $this->user_auth->entityId,
						'title'     			=> $this->input->post('p_title'.$photo_count),
						'x'         			=> $x,
						'y'         			=> $y,
						'mime'      			=> $image_mime,
						'watermark' 			=> $this->input->post('p_watermark'.$photo_count),
						'watermark_colour_id'	=> $this->input->post('p_watermark_colour'.$photo_count),
						'source'				=> $this->input->post('p_photo_source'.$photo_count),
						'public_gallery'		=> (isset($_POST['p_public_gallery' . $photo_count])) ? 1 : 0
					);
					$id = $this->image->add('photo', $image, $info);
					$tags = explode(' ', $_POST['p_tags' . $photo_count]);
					foreach ($tags as $tag) {
						if ($tag != '') {
							$tag_details = $this->photos_model->GetTagDetails($tag);
							if (!$tag_details) {
								// Tag doesn't already exist so add it
								$tag_id = $this->photos_model->AddPhotoTag($tag);
							} else {
								$tag_id = $tag_details->tag_id;
							}
							// Associate photo with tag
							$this->photos_model->AssociatePhotoTag($id, $tag_id);
						}
					}
					@unlink($image_path);
				}
				$photo_count++;
			}
		}
		redirect('/office/gallery');
	}

	function mass_upload_preview ($file = '') {
		$width = 100;
		$height = 75;
		$path = $this->config->item('static_local_path') . '/photos/' . $file;
		if (is_file($path)) {
			$ext = strrchr($file, '.');
			if ($ext === FALSE) return;
			$ext = strtolower(substr($ext, 1));
			switch ($ext) {
				case 'jpeg':
				case 'jpg':
					$image = imagecreatefromjpeg($path);
					break;
				case 'gif':
					$image = imagecreatefromgif($path);
					break;
				case 'png':
					$image = imagecreatefrompng($path);
					break;
				default:
					return;
			}

			$thumb_ratio = $width / $height;
			$new_height = imagesy($image);
			$new_width = $new_height * $thumb_ratio;
			if ($new_width > imagesx($image)) {
				$new_width = imagesx($image);
				$new_height = $new_width / $thumb_ratio;
			}
			$new_x = floor((imagesx($image) - $new_width) / 2);
			$new_y = floor((imagesy($image) - $new_height) / 2);

			$new_image = imagecreatetruecolor($width, $height);
			imagecopyresampled($new_image, $image, 0, 0, $new_x, $new_y, $width, $height, $new_width, $new_height);

			switch ($ext) {
				case 'jpeg':
				case 'jpg':
					header('Content-type: image/jpeg');
					imagejpeg($new_image, null, 100);
					break;
				case 'gif':
					header('Content-type: image/gif');
					imagegif($new_image);
					break;
				case 'png':
					header('Content-type: image/png');
					imagepng($new_image, null, 0);
					break;
			}
		}
	}

	function edit() {
		if (!CheckPermissions('office')) return;

		$this->load->library('image_upload');
		$this->xajax->processRequests();

		$output = array(); // hack to use same view
		$data = array();

		$id = $this->uri->segment(4);

		$photoDetails = $this->db->getwhere('photos', array('photo_id' => $id));
		$thumbDetails = $this->db->get('image_types');

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
