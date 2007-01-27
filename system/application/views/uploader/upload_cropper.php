<p>Some useful text should go here:-</p>
<?php
foreach($data as $d) {
	echo $d;
}
?>

<h2>Dynamic image test</h2>
<p>
	Test of dynamically changing images or removing & re-applying the cropper
</p>
<div id="previewArea-0"></div>
<div id="previewArea-1"></div>

<script type="text/javascript" charset="utf-8">
	function submitPicture()
	{
		xajax.$('submitButton').disabled=true;
		xajax.$('submitButton').value="Saving...";
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
			$( 'testImage' ).src = imgSrc;
			$( 'imgCrop_testImage' ).src = imgSrc;
			$( 'testImage' ).width = w;
			$( 'testImage' ).height = h;
				if (imgTypeNew == 0) {
					this.removeCropper();
					this.curCrop = new Cropper.ImgWithPreview( 'testImage', {
						minWidth: 200,
						minHeight: 120,
						ratioDim: { x: 200, y: 120 },
						displayOnInit: true, 
						onEndCrop: onEndCrop,
						previewWrap: 'previewArea-0'} );
					this.attachCropper();
				}
				if (imgTypeNew == 1) {
					this.removeCropper();
					this.curCrop = new Cropper.ImgWithPreview( 'testImage', {
						minWidth: 100,
						minHeight: 120,
						ratioDim: { x: 100, y: 120 },
						displayOnInit: true, 
						onEndCrop: onEndCrop,
						previewWrap: 'previewArea-1'} );
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
			if( this.curCrop == null ) this.curCrop = new Cropper.ImgWithPreview( 'testImage', {
				minWidth: 200,
				minHeight: 120,
				ratioDim: { x: 200, y: 120 },
				displayOnInit: true, 
				onEndCrop: onEndCrop,
				previewWrap: 'previewArea-0'} );
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
		$( 'x1' ).value = coords.x1;
		$( 'y1' ).value = coords.y1;
		$( 'x2' ).value = coords.x2;
		$( 'y2' ).value = coords.y2;
		$( 'width' ).value = dimensions.width;
		$( 'height' ).value = dimensions.height;
	}
	
	// basic example
	Event.observe( 
		window, 
		'load', 
		function() { 
			CropImageManager.init();
			Event.observe( $('removeCropper'), 'click', CropImageManager.removeCropper.bindAsEventListener( CropImageManager ), false );
			Event.observe( $('resetCropper'), 'click', CropImageManager.resetCropper.bindAsEventListener( CropImageManager ), false );
			Event.observe( $('imageChoice'), 'change', CropImageManager.onChange.bindAsEventListener( CropImageManager ), false );
		}
	);
	
</script>
<div id="testWrap">
	<img src="images/photos/1.jpg" alt="test image" id="testImage" width="500" height="333" />
</div>
<form id="pictureCrop" action="javascript:void(null);" onsubmit="submitPicture();">
	<p>
		<label for="imageChoice">image:</label>
		<select name="imageChoice" id="imageChoice">
			<option value="images/photos/1.jpg|380|235|0">Castle0</option>
			<option value="castle.jpg|500|333|1">Castle1</option>
			<option value="poppy.jpg|311|466|0">Flower0</option>
		</select>
	</p>

	<p>
		<input type="button" id="removeCropper" value="Remove Cropper" />
		<input type="button" id="resetCropper" value="Reset Cropper" />
		<input id="submitButton" type="submit" value="Save"/>
	</p>


	<p>
		<label for="x1">x1:</label>
		<input type="text" name="x1" id="x1" />
	</p>
	<p>
		<label for="y1">y1:</label>
		<input type="text" name="y1" id="y1" />
	</p>
	<p>
		<label for="x2">x2:</label>
		<input type="text" name="x2" id="x2" />
	</p>
	<p>
		<label for="y2">y2:</label>
		<input type="text" name="y2" id="y2" />
	</p>
	<p>
		<label for="width">width:</label>
		<input type="text" name="width" id="width" />
	</p>
	<p>
		<label for="height">height</label>
		<input type="text" name="height" id="height" />
	</p>
</form>