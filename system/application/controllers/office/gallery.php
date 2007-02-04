<?php

/// Main viparea controller.
class Gallery extends Controller
{
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
		
		SetupMainFrame('office');
	}
	
	function index()
	{
		$this->pages_model->SetPageCode('office_gallery');
		
		// Load the main frame
		if (CheckPermissions(array('student','office'))) {
			$data = array(
				'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
			);

			$extra_head = array(
				'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
			);
			
			// Set up the center div for the gallery.
			$gallery_div = $this->frames->view('office/gallery/gallerythumbs');
			$gallery_div->AddData($data);

			// Set up the subview for gallery.
			$gallery_frame = $this->frames->frame('office/gallery/galleryframe');
			$gallery_frame->AddData($data);
			$gallery_frame->SetContent($gallery_div);

			// Set up the master frame.
			$this->main_frame->SetContent($gallery_frame);
		}
	
		// Load the main frame
		$this->main_frame->Load();
	}
	
	function show()
	{
		$this->pages_model->SetPageCode('office_gallery');
		
		// Load the main frame
		if (CheckPermissions(array('student','office'))) {
			$data = array(
				'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
			);

			$extra_head = array(
				'main_text' => $this->pages_model->GetPropertyWikitext('main_text'),
			);
			
			// Set up the center div for the gallery.
			$gallery_div = $this->frames->view('office/gallery/galleryimage');
			$gallery_div->AddData($data);

			// Set up the subview for gallery.
			$gallery_frame = $this->frames->frame('office/gallery/galleryframe');
			$gallery_frame->AddData($data);
			$gallery_frame->SetContent($gallery_div);

			// Set up the master frame.
			$this->main_frame->SetContent($gallery_frame);
		}
	
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>