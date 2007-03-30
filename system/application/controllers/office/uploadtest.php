<?php

class Uploadtest extends Controller {
	/**
	 * @brief Default constructor.
	 */
	function __construct() {
		parent::Controller();
	}
	
	function index() {
		if (!CheckPermissions('office')) return;
		$this->load->library('Image_upload');
		$this->load->helper('url');
		if ($this->image_upload->uploadForm(true, true)) {
			$this->image_upload->recieveUpload('office/uploadtest/done', false);
		}
	}
	
	function done() {
		if (!CheckPermissions('office')) return;
		echo "done";
	}
	
}
?>