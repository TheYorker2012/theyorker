<?php

class Upload extends Controller {
	
	function Upload() {
		parent::Controller();
		$this->load->helper(array('form', 'url'));
		$this->load->library('frame_public');
	}
	
	function _processImage($data) {
		return 'Image uploaded, now we must load the cropper, and also the details about the sizes to crop to.';
	}
	
	function index() {
		$this->frame_public->SetTitle('Upload Form');
		$this->frame_public->SetExtraHead('<script src="/javascript/clone.js" type="text/javascript"></script>');
		$this->frame_public->SetContentSimple('uploader/upload_form');
		$this->frame_public->Load();
	}

	function do_upload() {
		$this->load->library('upload');
		$this->load->library('xajax');
		$this->xajax->registerFunction(array("process_form_data", &$this, "process_form_data"));
		$this->xajax->processRequests();
		
		//get data about thumbnails
		
		$config['upload_path'] = './tmp/uploads/';
		$config['allowed_types'] = 'gif|jpg|png|zip';
		$config['max_size']	= '2048';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		
		$data = array();
		$this->load->library('upload', $config); // this config call is clearly not working!!! I hate it
		$this->upload->initialize($config);
		for ($x = 1; $x <= $this->input->post('destination'); $x++) {
			if ( ! $this->upload->do_upload('userfile'.$x)) {
				$data[] = $this->upload->display_errors();
			} else {
				$data[] = $this->upload->data();
				if ($data[$x - 1]['file_ext'] == '.zip') {
					// TODO Zip support
					trigger_error("No Zip Support yet...");
				} else {
					$data[$x - 1] = $this->_processImage($data[$x - 1]);
				}
			}
		}
		$this->frame_public->SetTitle('Photo Cropper');
		$head = '<script src="javascript/prototype.js" type="text/javascript"></script>	
<script src="javascript/scriptaculous.js?load=builder,dragdrop" type="text/javascript"></script>
<script src="javascript/cropper.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">
	function submitPicture()
	{
		xajax.$(\'submitButton\').disabled=true;
		xajax.$(\'submitButton\').value="Saving...";
		xajax_process_form_data(xajax.getFormValues("pictureCrop"));
		return false;
	}
	
	/**
	 * A little manager that allows us to swap the image dynamically
	 *
	 */
	var CropImageManager = {
		/**
		 * Holds the current Cropper.Img object
		 * @var obj
		 */
		curCrop: null,
		
		/**
		 * Initialises the cropImageManager
		 *
		 * @access public
		 * @return void
		 */
		init: function() {
			this.attachCropper();
		},
		
		/**
		 * Handles the changing of the select to change the image, the option value
		 * is a pipe seperated list of imgSrc|width|height
		 * 
		 * @access public
		 * @param obj event
		 * @return void
		 */
		onChange: function( e ) {
			var vals = $F( Event.element( e ) ).split('|');
			this.setImage( vals[0], vals[1], vals[2], vals[3] ); 
		},
		
		/**
		 * Sets the image within the element & attaches/resets the image cropper
		 *
		 * @access private
		 * @param string Source path of new image
		 * @param int Width of new image in pixels
		 * @param int Height of new image in pixels
		 * @return void
		 */
		setImage: function( imgSrc, w, h, imgTypeNew ) {
			$( \'testImage\' ).src = imgSrc;
			$( \'imgCrop_testImage\' ).src = imgSrc;
			$( \'testImage\' ).width = w;
			$( \'testImage\' ).height = h;
				if (imgTypeNew == 0) {
					this.removeCropper();
					this.curCrop = new Cropper.ImgWithPreview( \'testImage\', {
						minWidth: 200,
						minHeight: 120,
						ratioDim: { x: 200, y: 120 },
						displayOnInit: true, 
						onEndCrop: onEndCrop,
						previewWrap: \'previewArea-0\'} );
					this.attachCropper();
				}
				if (imgTypeNew == 1) {
					this.removeCropper();
					this.curCrop = new Cropper.ImgWithPreview( \'testImage\', {
						minWidth: 100,
						minHeight: 120,
						ratioDim: { x: 100, y: 120 },
						displayOnInit: true, 
						onEndCrop: onEndCrop,
						previewWrap: \'previewArea-1\'} );
					this.attachCropper();
				}
		},
		
		/** 
		 * Attaches/resets the image cropper
		 *
		 * @access private
		 * @return void
		 */
		attachCropper: function() {
			if( this.curCrop == null ) this.curCrop = new Cropper.ImgWithPreview( \'testImage\', {
				minWidth: 200,
				minHeight: 120,
				ratioDim: { x: 200, y: 120 },
				displayOnInit: true, 
				onEndCrop: onEndCrop,
				previewWrap: \'previewArea-0\'} );
			else this.curCrop.reset();
		},
		
		/**
		 * Removes the cropper
		 *
		 * @access public
		 * @return void
		 */
		removeCropper: function() {
			if( this.curCrop != null ) {
				this.curCrop.remove();
			}
		},
		
		/**
		 * Resets the cropper, either re-setting or re-applying
		 *
		 * @access public
		 * @return void
		 */
		resetCropper: function() {
			this.attachCropper();
		}
	};
	
	
	// setup the callback function
	function onEndCrop( coords, dimensions ) {
		$( \'x1\' ).value = coords.x1;
		$( \'y1\' ).value = coords.y1;
		$( \'x2\' ).value = coords.x2;
		$( \'y2\' ).value = coords.y2;
		$( \'width\' ).value = dimensions.width;
		$( \'height\' ).value = dimensions.height;
	}
	
	// basic example
	Event.observe( 
		window, 
		\'load\', 
		function() { 
			CropImageManager.init();
			Event.observe( $(\'removeCropper\'), \'click\', CropImageManager.removeCropper.bindAsEventListener( CropImageManager ), false );
			Event.observe( $(\'resetCropper\'), \'click\', CropImageManager.resetCropper.bindAsEventListener( CropImageManager ), false );
			Event.observe( $(\'imageChoice\'), \'change\', CropImageManager.onChange.bindAsEventListener( CropImageManager ), false );
		}
	);
	
</script>';
		$this->frame_public->SetExtraHead($head);
		$this->frame_public->SetContentSimple('uploader/upload_cropper', array('data' => $data));
		$this->frame_public->Load();
	}
	
	function process_form_data($form_data) {
		// there is no hack protection on this, i'm pretty sure
		$objResponse = new xajaxResponse();

		$objResponse->addAssign("submitButton","value","Save");
		$objResponse->addAssign("submitButton","disabled",false);

		$objResponse->addAssign("div_result", "innerHTML", $result);
		return $objResponse;
	}
	
}
?>