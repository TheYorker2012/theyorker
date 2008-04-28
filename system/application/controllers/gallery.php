<?php
/**
 *	@brief	Displays a photo with caption
 *	@author	Chris Travis (cdt502 - ctravis@gmail.com)
 */
class Gallery extends Controller {

	function __construct()
	{
		parent::Controller();
		$this->load->model('photos_model');
	}

	function _remap ($method = NULL)
	{
		$this->index($method);
	}

	function index($photo_id = NULL)
	{
		if (!CheckPermissions('public')) return;

		if (($photo_id === NULL) || (($photo = $this->photos_model->GetPhotoDetails($photo_id)) === NULL))
			show_404();

		$this->load->library('image');
		$this->pages_model->SetPageCode('gallery');

		$data = array();
		$data['photo_title'] = $photo->photo_title;
		$data['photo_xhtml'] = $this->image->getThumb($photo->photo_id, 'gallery', true);

		$data['tags'] = $this->photos_model->GetPhotosTags($photo_id);
		$data['tags_photos'] = array();
		foreach ($data['tags'] as $tag) {
			$photos = $this->photos_model->GetPhotosForTag($tag['tag_id']);
			$data['tags_photos'][$tag['tag_id']] = array();
			foreach ($photos as $p) {
				$selected = ($p['photo_id'] == $photo_id) ? array('class' => 'selected') : array();
				$data['tags_photos'][$tag['tag_id']][] = array(
					'id'	=>	$p['photo_id'],
					'xhtml'	=>	$this->image->getThumb($p['photo_id'], 'small', false, $selected)
				);
			}
		}

		$this->main_frame->SetTitleParameters(array('photo_title' => $data['photo_title']));
		$this->main_frame->SetContentSimple('gallery/view', $data);
		$this->main_frame->Load();
	}

}
?>