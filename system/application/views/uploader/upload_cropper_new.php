<p>All your photos are now belong to us, Select a photo and an image type below to crop a photo into an image (for stuff like thumbnails), and press save once you are happy with the crop, so we can store it. You can always re-save it if you change your mind.</p>
<?php
foreach ($ThumbDetails->result() as $Single) {
	echo '<p>'.$Single->image_type_name.': -</p><div id="previewArea-'.$Single->image_type_id.'"></div>';
}
?>
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
			this.setImage('/images/null-blank.jpg', 380, 235, 1);
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
			$( 'uploadedImage' ).src = imgSrc;
			$( 'uploadedImage' ).width = w;
			$( 'uploadedImage' ).height = h;
<?php		foreach ($ThumbDetails->result() as $Single) : ?>
			if (imgTypeNew == <?=$Single->image_type_id?>) {
				if (!$( 'previewArea-<?=$Single->image_type_id?>' ).empty()) $( 'previewArea-<?=$Single->image_type_id?>' ).removeChild($( 'previewArea-<?=$Single->image_type_id?>' ).firstChild);
				if (this.curCrop != null) this.curCrop.remove();
				this.curCrop = new Cropper.ImgWithPreview( 'uploadedImage', {
					minWidth: <?=$Single->image_type_width?>,
					minHeight: <?=$Single->image_type_height?>,
					ratioDim: { x: <?=$Single->image_type_width?>, y: <?=$Single->image_type_height?> },
					displayOnInit: true,
					onEndCrop: onEndCrop,
					previewWrap: 'previewArea-<?=$Single->image_type_id?>'} );
				this.curCrop.reset();
			}
<?php		endforeach; ?>
		}
	};


	var thumbTypeList = new Array();
	var thumbTypeNameMap = new Array();
<?php		foreach ($ThumbDetails->result() as $Single) : ?>
		thumbTypeNameMap[<?=$Single->image_type_id?>] = '<?=$Single->image_type_name?>';
		thumbTypeList.push(<?=$Single->image_type_id?>);
<?php		endforeach; ?>

	function registerImageSave(image_type_id) {
		thumbTypeList = thumbTypeList.without(image_type_id);
	}

	window.onbeforeunload = function () {
		if(thumbTypeList.length != 0) {
			var msg = 'You have not saved versions of the following thumbnails:\n';
			thumbTypeList.each(function(item) {
			  msg += thumbTypeNameMap[item] + '\n';
			};
			return msg;
		}
	}

	function canReturn() {
		if(thumbTypeList.length != 0) {
			var msg = 'You have not saved versions of the following thumbnails:\n';
			thumbTypeList.each(function(item) {
			  msg += thumbTypeNameMap[item] + '\n';
			};
			alert(msg);
			return false;
		} else {
			return true;
		}
	}

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
			Event.observe( $('imageChoice'), 'change', CropImageManager.onChange.bindAsEventListener( CropImageManager ), false );
		}
	);

</script>

<form id="pictureCrop" action="javascript:void(null);" onsubmit="submitPicture();">
	<p>
		<label for="imageChoice">Thumbnail: -</label>
		<select name="imageChoice" id="imageChoice">
			<option value="choose">Please Choose...</option>
			<?php
			foreach($data as $d) {
				foreach($d as $singleThumb) {
					echo '<option value="'.$singleThumb['string'].'">'.$singleThumb['title'].'</option>';
				}
			}
			?>
		</select>
		<input id="submitButton" type="submit" value="Save"/>
	</p>
	<div style="clear: both;"></div>
	<p>Photo: -</p>
	<div id="uploadedWrap">
		<img src="/images/photos/null.jpg" alt="Uploaded image" id="uploadedImage" />
	</div>
		<input type="hidden" name="x1" id="x1" />
		<input type="hidden" name="y1" id="y1" />
		<input type="hidden" name="x2" id="x2" />
		<input type="hidden" name="y2" id="y2" />
		<input type="hidden" name="width" id="width" />
		<input type="hidden" name="height" id="height" />
</form>
<p><a href="<?=$returnPath?>" onclick="return canReturn();">Return</a></p>