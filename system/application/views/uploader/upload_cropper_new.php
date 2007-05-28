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
			if ( thumbList.grep(currentThumb).length > 0) {
				if(!confirm('You have not saved changes to this thumbnail. Are you sure you want to switch to a different thumbnail?')) {
					return false;
				}
			}
			this.setImage( vals[0], vals[1], vals[2], vals[3] );
			currentThumb = vals[8];
			document.getElementById('uploadedWrapMaster').style.display = (document.getElementById('imageChoice').value == 'choose' ? 'none' : 'block' );
			document.getElementById('thumbWrapMaster').style.display = 'block';
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

	var currentThumb = null;

	var thumbList = new Array();
	var thumbNameMap = new Array();

	<?php
	foreach($data as $d) {
		foreach($d as $singleThumb) {
		?>
		thumbNameMap['<?=$singleThumb['thumb_id']?>'] = '<?=str_replace("'", "\\'", $singleThumb['title'])?>';
		thumbList.push('<?=$singleThumb['thumb_id']?>');
		<?
		}
	}
	?>

	function registerImageSave(thumb_id) {
		thumbList = thumbList.without(thumb_id);
	}

	window.onbeforeunload = function () {
		if(thumbList.length != 0) {
			var msg = 'You have not saved versions of the following thumbnails:\n';
			thumbList.each(function(item) {
			  msg += ' ' + thumbNameMap[item] + '\n';
			});
			return msg;
		}
	}

	function pageReady() {
		document.getElementById('loadingWrap').style.display = 'none';
		document.getElementById('dropdownWrap').style.display = 'block';
	}

	onLoadFunctions.push(pageReady);

	function canReturn() {
		if(thumbList.length != 0) {
			var msg = 'You have not saved versions of the following thumbnails:\n';
			thumbList.each(function(item) {
			  msg += ' ' + thumbNameMap[item] + '\n';
			});
			msg += '\nYou must save all thumbnail sizes to continue.';
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
<div class="BlueBox" style="width: 100%;">
	<div style="float: right; width: 55%;">
	<ol>
	<li>Select a thumbnail from the drop down box</li>
	<li>Use the tool below to crop your photo appropriately</li>
	<li>Press save once you are happy with the crop</li>
	<li>Repeat for every thumbnail in the list</li>
	</ol>
	</div>
	<h2>thumbnail selector</h2>
	<div id="loadingWrap">
		<p><b>Loading...</b></p>
	</div>
	<div id="dropdownWrap" style="display: none;">
	<p>
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
	</div>
</div>

<div class="BlueBox" id="thumbWrapMaster" style="display: none; width: 100%;">
<h2>stored thumbnails</h2>
<table border="0" width="100%">
<tr>
<?php
foreach ($ThumbDetails->result() as $Single) {
	echo '<th>'.$Single->image_type_name.'</th>';
}
?>
</tr>
<tr>
<?php
foreach ($ThumbDetails->result() as $Single) {
	echo '<td><div id="previewArea-'.$Single->image_type_id.'"></div></td>';
}
?>
</tr>
</table>
</div>

<div id="uploadedWrapMaster" class="BlueBox" style="display: none; width: 100%;">
	<h2>original photograph</h2>
	<div id="uploadedWrap">
		<img src="/images/photos/null.jpg" alt="Uploaded image" id="uploadedImage" />
	</div>
	<input type="hidden" name="x1" id="x1" />
	<input type="hidden" name="y1" id="y1" />
	<input type="hidden" name="x2" id="x2" />
	<input type="hidden" name="y2" id="y2" />
	<input type="hidden" name="width" id="width" />
	<input type="hidden" name="height" id="height" />
</div>
</form>
<div class="BlueBox" style="width: 100%;">
	<h2>finished</h2>
	<p>If you have thumbnailed all photos, click the button below:</p>
	<p><input type="button" onclick="if (canReturn()) window.location='/<?=$returnPath?>';" value="Finish" /></p>
</div>