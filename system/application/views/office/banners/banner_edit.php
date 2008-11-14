<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class='BlueBox'>
		<h2>edit banner</h2>
		<div name='banner_details_form' id='banner_details_form'>
			<form action='/office/banners/edit/<?php echo(xml_escape($banner['banner_id'])); ?>' method='POST' class='form'>
				<fieldset>
					<label>Banner:</label>&nbsp;&nbsp;
					<?php echo($this->image->getImage($banner['banner_id'], $banner['banner_type'], array('width' => '196', 'height' => '50'))); ?><br />
					<label for='banner_title'>Title:</label>
					<input type='text' id='banner_title' name='banner_title' size="35" value="<?php 
					echo(xml_escape($banner['banner_title'])); 
					?>" />
					<br />
					<label for='banner_link'>Link:</label>
					<input type='text' id='banner_link' name='banner_link' size="35" <?php 
					echo('value="'.xml_escape($banner['link']).'"');
					if (empty($current_homepage['homepage_id'])) {
						echo(' disabled="true"');
					}					
					?> />
					<br />
					<label for='banner_homepage'>Homepage:</label>
					<select name="banner_homepage" id="banner_homepage" onchange="TryDisableSchedule()">
						<option value="-1">#Unassigned#</option>
						<?php
						foreach ($homepages as $homepage) {
							echo ('					<option value="'.$homepage['id'].'"');
								if(!empty($current_homepage['homepage_id']))
								{
									if ($homepage['id']==$current_homepage['homepage_id']) {
										echo ' selected="selected"';
									}
								}
								echo ('>'."\n");
								echo ('						'.xml_escape($homepage['name'])."\n");
								echo ('					</option>'."\n");
						}
						?>
					</select>
					<?php
					//If there is no schedule at the moment hide all the detail.
					if (empty($banner['banner_last_displayed_timestamp'])) {
						$check_scheduled_box='';
						$display_scheduled_div=' style="display: none;"';
					} else {
						$check_scheduled_box=' checked';
						$display_scheduled_div='';
					}
					?>
					<label for='banner_scheduled'>Schedule:</label>
					<input type='checkbox' onchange="SwitchScheduling()" id='banner_scheduled' name='banner_scheduled' value='1'<?php 
					echo($check_scheduled_box); 
					//If there is no homepage dont allow scheduling
					if (empty($current_homepage['homepage_id'])) {
						echo(' disabled="true"');
					}
					?>/>
					<br />
					<div id="banner_schedule_date_div" <?php echo($display_scheduled_div); ?>>
						<label for='banner_schedule_days_radio'>By Days:</label>
						<input type="radio" name="banner_schedule_radio" value="days" onclick="SwitchScheduler(1)" checked>
						<select name="banner_schedule_days" id="banner_schedule_days">
							<option value="-1">&nbsp;</option>
							<?php
							if (!empty($banner['banner_last_displayed_timestamp'])) {
								$days_to_schedule = floor($banner['banner_last_displayed_timestamp'] / 86400) - floor(time() / 86400);
							} else {
								$days_to_schedule = -1;//There is no date, so set a number of days that doesnt exist.
							}
							for ($days_count=0;$days_count<=14;$days_count++)
							{
									echo ('					<option value="'.$days_count.'"');
									if ($days_to_schedule == $days_count) {
										echo ' selected="selected"';
									}
									echo ('>'."\n");
									if ($days_count==0) {
										echo ('Today');
									} else if ($days_count==1){
										echo ('Tomorrow');
									} else {
										echo ('In '.$days_count.' Days');
									}
									echo ('					</option>'."\n");
							}
							?>
						</select>
						<label for="banner_schedule_date_radio">By Date:</label>
						<input type="radio" name="banner_schedule_radio" value="date" onclick="SwitchScheduler(0)">
						<input type="text" name="banner_schedule_date" id="banner_schedule_date" value="<?php
						if (!empty($banner['banner_last_displayed_timestamp'])) {
							echo(date('d/m/y',$banner['banner_last_displayed_timestamp']));
						} else {
							echo('dd/mm/yy');
						}
						?>" disabled="true"/>
						<br />
						<script language="JavaScript">
							function TryDisableSchedule()
							{
								if (document.getElementById('banner_homepage').value == -1) {
									document.getElementById('banner_scheduled').checked=false;
									document.getElementById('banner_scheduled').disabled=true;
									document.getElementById('banner_schedule_date_div').style.display = 'none';
									document.getElementById('banner_link').disabled=true;
								} else {
									document.getElementById('banner_scheduled').disabled=false;
									document.getElementById('banner_link').disabled=false;
								}
							}
							function SwitchScheduling()
							{
								if (document.getElementById('banner_scheduled').checked) {
									document.getElementById('banner_schedule_date_div').style.display = 'block';
								} else {
									document.getElementById('banner_schedule_date_div').style.display = 'none';
								}
							}
							function SwitchScheduler(Scheduler)
							{
								if (Scheduler==0) {
									document.getElementById('banner_schedule_date').disabled=false;
									document.getElementById('banner_schedule_days').disabled=true;
								} else {
									document.getElementById('banner_schedule_date').disabled=true;
									document.getElementById('banner_schedule_days').disabled=false;
								}
							}
						</script>
					</div>
				</fieldset>
				<fieldset>
					<input name='name_cancel_button' type='button' onClick="document.location='/office/banners/<?php 
					if (!empty($current_homepage['homepage_codename'])) {
						echo(xml_escape('section/'.$current_homepage['homepage_codename']));
					}
					?>';" value='Cancel' class='button' />
					<input name='name_delete_button' type='submit' value='Delete' class='button' onclick="return confirm('Are you sure you want to perminently delete this banner?');" />
					<input name='name_update_button' type='submit' value='Update' class='button' />
				</fieldset>
			</form>
		</div>
	</div>
</div>
