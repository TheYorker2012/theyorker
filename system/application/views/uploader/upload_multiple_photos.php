<div id="source" style="display:none;">
	<label for="title" style="margin-top: 20px;">Title / ALT Text: </label>
	<input type="text" name="title" size="32" style="margin-top: 20px;" />
	<label for="userfile">File Location: </label>
	<input type="file" name="userfile" size="20"/>
</div>
<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<p> This allows you to submit photos to the gallary. Once submitted, you will be asked to thumbnail each photo so that they can be used on the site.</p>
	<p> The <b>photo title</b> will be used as an alternative text for the photo that is uploaded, to make the site more accessible to people who have difficulty recognising images. Please try to make this title <b>concisely</b> describe what the image depicts.</p>
</div>

<div id="MainColumn">
	<form action="<?=site_url($this->uri->uri_string())?>" method="post" onsubmit="return ValidateClones();" enctype="multipart/form-data">
		<div class="BlueBox">
			<h2>photo upload</h2>
			<p> Please choose the photos you wish up upload here. Each file is limited to 2Mb in size, and must be larger than 220x165 in resolution.</p>
			<div>
				<label for="title1">Title / ALT Text: </label>
				<input type="text" name="title1" size="32" />
				<label for="userfile1">File Location: </label>
				<input type="file" name="userfile1" size="20" />
			</div>
			<input type="hidden" name="destination" id="destination" value="1" />
			<div style="clear: both"></div>
			<input type="button" class="button" onclick="AddClones()" value="Add Another File"/>
		</div>
		<div class="BlueBox">
			<h2>watermark</h2>
			<p> If you would like to have these photos watermarked, enter the watermark text in the box below. Leave this box blank otherwise.</p>
			<p> Use the watermark to accredit the <b>author</b> of the photo, if necessary.</p>
			<div>
				<label for="watermark">Watermark: </label>
				<input type="text" name="watermark" size="32" />
				<div style="clear: both"></div>
			</div>
		</div>
		<div class="BlueBox">
			<h2>next step...</h2>
			<p>When you have finished selecting files, click the button below to proceed.</p>
			<input type="submit" class="button" value="Upload" />
		</div>
	</form>
</div>