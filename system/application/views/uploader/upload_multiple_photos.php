<div id="source" style="display:none;">
	<label for="title" style="margin-top: 20px;">Photo Title: </label>
	<input type="text" name="title" id="title" size="32" style="margin-top: 20px;" />
	<label for="userfile">File Location: </label>
	<input type="file" name="userfile" id="userfile" size="20" />
	<label for="photo_source">Photo Source: </label>
	<input type="text" name="photo_source" id="photo_source" size="32" />
	<label for="watermark">Watermark: </label>
	<input type="text" name="watermark" id="watermark" size="32" />
	<label for="watermark_colour">Text Colour: </label>
	<select name="watermark_colour" id="watermark_colour" size="1">
<?php foreach ($watermark_colours as $colour) { ?>
		<option value="<?php echo($colour->id); ?>"><?php echo($colour->name); ?></option>
<?php } ?>
	</select>

</div>
<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<p> This allows you to submit photos to the gallary.</p>
	<p> The <b>photo title</b> will be used as an alternative text for the photo that is uploaded, to make the site more accessible to people who have difficulty recognising images. Please try to make this title <b>concisely</b> describe what the image depicts.</p>
</div>

<div id="MainColumn">
	<form action="<?php echo site_url($this->uri->uri_string());?>" method="post" onsubmit="return ValidateClones();" enctype="multipart/form-data">
		<div class="BlueBox">
			<h2>photo upload</h2>
			<p>Please choose the photos you wish up upload here. Each file is limited to 2Mb in size.</p>
			<p>Don't forget to fill in the <b>source</b> field to let us know where you got this photo from. If it
			is a personal photo or from a photographer just enter their name. If the photo is from a web site,
			simply paste in the link where the photo can be found.</p>
			<p>If you would like to have these photos watermarked, enter the watermark text in the box below.
			Leave this box blank otherwise. Use the watermark to accredit the <b>author</b> of the photo.</p>
			<div>
				<label for="title1">Photo Title: </label>
				<input type="text" name="title1" id="title1" size="32" />
				<label for="userfile1">File Location: </label>
				<input type="file" name="userfile1" id="userfile1" size="20" />
				<label for="photo_source1">Photo Source: </label>
				<input type="text" name="photo_source1" id="photo_source1" size="32" />
				<label for="watermark1">Watermark: </label>
				<input type="text" name="watermark1" id="watermark1" size="32" />
				<label for="watermark_colour1">Text Colour: </label>
				<select name="watermark_colour1" id="watermark_colour1" size="1">
<?php foreach ($watermark_colours as $colour) { ?>
					<option value="<?php echo($colour->id); ?>"><?php echo($colour->name); ?></option>
<?php } ?>
				</select>
			</div>
			<input type="hidden" name="destination" id="destination" value="1" />
			<div style="clear: both"></div>
			<input type="button" class="button" onclick="AddClones()" value="Add Another File"/>
		</div>
		<div class="BlueBox">
			<h2>next step...</h2>
			<p>When you have finished selecting files, click the button below to proceed.</p>
			<input type="submit" class="button" value="Upload" />
		</div>
	</form>
</div>