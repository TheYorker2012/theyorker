<div class='RightToolbar'>
	<h4>What's this?</h4>
	<div class="Entry">
		This page allows you to edit links <b>live</b>. Take care.
	</div>
</div>
<div class='blue_box'>
	<h2>edit link</h2>
	<div name='link_details_form' id='link_details_form'>
		<form name='link_form' action='/office/links/update/<?php echo $link->link_id; ?>' method='POST' class='form'>
			<fieldset>
				<label for='link_image'>Link:</label>
				<?php echo $this->image->getImage($link->link_image_id, 'link'); ?>
				<br />
				<label for='link_name'>Name:</label>
				<textarea id='link_name' name='link_name' cols="30" rows="2"><?php echo $link->link_name; ?></textarea>
				<br />
				<label for='link_url'>URL:</label>
				<textarea id='link_url' name='link_url' cols="30" rows="2"><?php echo $link->link_url; ?></textarea>
				<br />
				<input name='name_cancel_button' type='button' onClick="document.location='/office/links/';" value='Cancel' class='button' />
				<input name='name_update_button' type='submit' value='Update' class='button' />
			</fieldset>
		</form>
	</div>
</div>
