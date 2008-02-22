<div class='RightToolbar'>
	<h4 class="first">Information</h4>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div class='blue_box'>
	<h2>edit banner</h2>
	<div name='banner_details_form' id='banner_details_form'>
		<form action='/office/banners/update/<?php echo($banner->banner_id); ?>' method='POST' class='form'>
			<fieldset>
				<label for='banner_image'>Banner:</label>
				<?php echo($this->image->getImage($banner->banner_id, 'banner')); ?>
				<br />
				<label for='banner_title'>Title:</label>
				<textarea id='banner_title' name='banner_title' cols="30" rows="2"><?php echo(xml_escape($banner->banner_title)); ?></textarea>
				<br />
				<label for='banner_link'>Link:</label>
				<input type='text' id='banner_link' name='banner_link' size="35" value="<?php echo(xml_escape($banner->link)); ?>" />
				<br />
			<?php if($banner->banner_last_displayed_timestamp != null) { ?>
				<input type='hidden' id='banner_scheduled' name='banner_scheduled' value='1'/>
				<div id="banner_schedule_date_div">
			<?php } else { ?>
				<label for='banner_homepage'>Homepage:</label>
				<select name="banner_homepage">
					<option value="">NONE</option>
					<?php
				foreach ($homepages as $homepage) {
					echo ('					<option value="'.$homepage['id'].'"');
						if(!empty($current_homepage_id))
						{
							if ($homepage['id']==$current_homepage_id) {
								echo ' selected="selected"';
							}
						}
						echo ('>'."\n");
						echo ('						'.xml_escape($homepage['name'])."\n");
						echo ('					</option>'."\n");
				}
				?>
				</select>
				<label for='banner_scheduled'>Scheduled:</label>
				<input type='checkbox' onchange="document.getElementById('banner_schedule_date_div').style.display = (this.checked ? 'block' : 'none');" id='banner_scheduled' name='banner_scheduled' value='1'/>
				<br />
				<div id="banner_schedule_date_div" style="display: none;">
			<?php } ?>
					<label for='banner_schedule_date'>Schedule Date:</label>
					<input type='text' id='banner_schedule_date' name='banner_schedule_date' value='<?php echo($banner->banner_last_displayed_timestamp ? $banner->banner_last_displayed_timestamp : ''); ?>'/>
					<br />
				</div>
				<input name='name_cancel_button' type='button' onClick="document.location='/office/banners/';" value='Cancel' class='button' />
				<input name='name_delete_button' type='submit' value='Delete' class='button' onclick="return confirm('Are you sure you want to perminently remove this banner?');" />
				<input name='name_update_button' type='submit' value='Update' class='button' />
			</fieldset>
		</form>
	</div>
</div>
