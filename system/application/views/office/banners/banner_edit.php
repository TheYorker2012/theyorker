<div class='RightToolbar'>
	<h4>What's this?</h4>
	<div class="Entry">
		This page allows you to edit banners <b>live</b>. Take care.
	</div>
</div>
<div class='blue_box'>
	<h2>edit banner</h2>
	<div name='banner_details_form' id='banner_details_form'>
		<form name='banner_form' action='/office/banners/update/<?php echo $banner->banner_id; ?>' method='POST' class='form'>
			<fieldset>
				<label for='banner_image'>Banner:</label>
				<?php echo $this->image->getImage($banner->banner_id, 'banner'); ?>
				<br />
				<label for='banner_title'>Title/Author:</label>
				<textarea id='banner_title' name='banner_title' cols="30" rows="2"><?php echo $banner->banner_title; ?></textarea>
				<br />
			<?php if($banner->banner_last_displayed_timestamp != null) { ?>
				<input type='hidden' id='banner_scheduled' name='banner_scheduled' value='1'/>
				<div id="banner_schedule_date_div">
			<?php } else { ?>
				<label for='banner_scheduled'>Scheduled:</label>
				<input type='checkbox' onchange="document.getElementById('banner_schedule_date_div').style.display = (this.checked ? 'block' : 'none');" id='banner_scheduled' name='banner_scheduled' value='1'/>
				<br />
				<div id="banner_schedule_date_div" style="display: none;">
			<?php } ?>
					<label for='banner_schedule_date'>Schedule Date:</label>
					<input type='text' id='banner_schedule_date' name='banner_schedule_date' value='<?php echo ($banner->banner_last_displayed_timestamp ? $banner->banner_last_displayed_timestamp : ''); ?>'/>
					<br />
				</div>
				<input name='name_cancel_button' type='button' onClick="document.location='/office/banners/';" value='Cancel' class='button' />
				<input name='name_delete_button' type='submit' value='Delete' class='button' onclick="return confirm('Are you sure you want to perminently remove this banner?');" />
				<input name='name_update_button' type='submit' value='Update' class='button' />
			</fieldset>
		</form>
	</div>
</div>
