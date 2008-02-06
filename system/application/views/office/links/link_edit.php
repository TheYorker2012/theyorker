<div class='RightToolbar'>
	<h4>What's this?</h4>
	<div class="Entry">
		This page allows you to edit links <b>live</b>. Take care.
	</div>
	<div class="Entry">
		 Please note that <b>the image must be 50x50 pixels</b> in size.
	</div>
</div>
<div class='blue_box'>
	<h2>edit link</h2>
	<div name='link_details_form' id='link_details_form'>
		<form name='link_form' action='/office/links/update/<?php echo($link->link_id); ?>' method='POST' class='form'>
			<fieldset>
				<label for='link_image'>Link:</label>
				<?php echo($this->image->getImage($link->link_image_id, 'link')); ?>
				<br />
				<label for='link_name'>Name:</label>
				<textarea id='link_name' name='link_name' cols="30" rows="2"><?php echo(xml_escape($link->link_name)); ?></textarea>
				<br />
				<label for='link_url'>URL:</label>
				<textarea id='link_url' name='link_url' cols="30" rows="2"><?php echo(xml_escape($link->link_url)); ?></textarea>
				<br />
				<input name='name_cancel_button' type='button' onClick="document.location='/office/links/';" value='Cancel' class='button' />
				<input name='name_delete_button' type='submit' value='Delete' class='button' onclick="return confirm('Are you sure you want to remove this link? Doing this will remove the link from ALL user\'s homepages, without warning.');" />
				<input name='name_update_button' type='submit' value='Update' class='button' />
			</fieldset>
		</form>
	</div>
</div>

<div class="blue_box" id="upload_form">
	<h2>upload image</h2>
	<form action="/office/links/updateimage/<?php echo($link->link_id); ?>" method="post" enctype="multipart/form-data">
		<fieldset>
			<label for="upload">50x50 Image</label>
			<input type="file" name="upload" /></br>
			<input type="submit" value="Upload" class='button' />
		</fieldset>
	</form>
</div>