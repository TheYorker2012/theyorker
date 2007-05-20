<?php


class Imagecp extends Controller {

	function Imagecp() {
		parent::Controller();
		if (!CheckPermissions('office')) return;
		$this->load->helper(array('url', 'form', 'entity'));
		$this->load->library('image');
	}
	
	function index() {
		
		$data['imageTypes'] = $this->db->select('image_type_name, image_type_codename')->get('image_types');
		
		$this->main_frame->SetTitle('Image Control Panel');
		$this->main_frame->SetContentSimple('admin/image/index', $data);
		
		$this->main_frame->Load();
	}

}
?>
