<div id="RightColumn">
	<h2 class="first">Quick Links</h2>
	<div class="Entry">
		<ul>
			<li><a href="/office/advertising/view/<?php echo($advert['id']); ?>">View This Advert</a></li>
			<li><a href="/office/advertising/editimage/<?php echo($advert['id']); ?>">Edit Advert Image</a></li>
			<li><a href="/office/advertising/">Back To Adverts List</a></li>
		</ul>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>edit advert</h2>
	<?php
		echo('	<form class="form" action="/office/advertising/edit/'.$advert['id'].'" method="post">'."\n");
	?>
			<fieldset>
	<?php
		echo('			<input type="hidden" id="advert_id" name="advert_id" value="'.$advert['id'].'" />'."\n");
	?>
			</fieldset>
			<fieldset>
				<label for="advert_name">Name: </label>
	<?php
		echo('			<input type="text" id="advert_name" name="advert_name" value="'.xml_escape($advert['name']).'" style="width: 230px" />'."\n");
	?>
				<label for="advert_name">Target Web Address: </label>
	<?php
		echo('			<input type="text" id="advert_url" name="advert_url" value="'.xml_escape($advert['url']).'" style="width: 230px" />'."\n");
	?>
				<label for="advert_alt">Image Hover Text: </label>
	<?php
		echo('			<input type="text" id="advert_alt" name="advert_alt" value="'.xml_escape($advert['alt']).'" style="width: 230px" />'."\n");
	?>
				<label for="advert_image">Max Views: </label>
	<?php
		echo('			<input type="text" id="advert_max_views" name="advert_max_views" value="'.$advert['max_views'].'" style="width: 230px" />'."\n");
	?>
				<label for="advert_start_date_day">Start Date: </label>
					<input
						type="text"
						id="advert_start_date_day"
						name="advert_start_date_day"
						value="<?php 
								if($advert['start_date']!=0){
									echo(date('d',$advert['start_date']));}
							?>"
						style="width: 20px"
						maxlength="2" />
					<input
						type="text"
						id="advert_start_date_month"
						name="advert_start_date_month"
						value="<?php
								if($advert['start_date']!=0){
									echo(date('m',$advert['start_date']));}
							 ?>"
						style="width: 20px"
						maxlength="2" />
					<input
						type="text"
						id="advert_start_date_year"
						name="advert_start_date_year"
						value="<?php
								if($advert['start_date']!=0){
									echo(date('y',$advert['start_date']));}
							?>"
						style="width: 20px"
						maxlength="2" />
				<div style="float:left; margin:10px 0 0 10px; font-style:italic;"">DD MM YY</div>
				<label for="advert_end_date_day">End Date: </label>
					<input
						type="text"
						id="advert_end_date_day"
						name="advert_end_date_day"
						value="<?php 
								if($advert['end_date']!=0){
									echo(date('d',$advert['end_date']));}
							?>"
						style="width: 20px"
						maxlength="2" />
					<input
						type="text"
						id="advert_end_date_month"
						name="advert_end_date_month"
						value="<?php
								if($advert['end_date']!=0){
									echo(date('m',$advert['end_date']));}
							 ?>"
						style="width: 20px"
						maxlength="2" />
					<input
						type="text"
						id="advert_end_date_year"
						name="advert_end_date_year"
						value="<?php
								if($advert['end_date']!=0){
									echo(date('y',$advert['end_date']));}
							?>"
						style="width: 20px"
						maxlength="2" />
				<div style="float:left; margin:10px 0 0 10px; font-style:italic;"">DD MM YY</div>
			</fieldset>
			<fieldset>
				<input type="submit" name="submit_save_advert" value="Save" style="float: right;" />
			</fieldset>
		</form>
	</div>

	<div class="BlueBox">
		<h2>advert options</h2>
	<?php
		if ($advert['is_live'] == true) {
	?>
		<p>
			pull advert from the site so it is no longer in rotation
		</p>
	<?php
		echo('	<form class="form" action="/office/advertising/edit/'.$advert['id'].'" method="post">'."\n");
	?>
			<fieldset>
	<?php
		echo('			<input type="hidden" id="advert_id" name="advert_id" value="'.$advert['id'].'" />'."\n");
	?>
			</fieldset>
			<fieldset>
				<input type="submit" name="submit_pull_advert" value="Pull Advert" style="float: right;" />
			</fieldset>
		</form>
	<?php
		}
		else {
	?>
		<p>
			add the advert to the advert rotation system on the site
		</p>
	<?php
		echo('	<form class="form" action="/office/advertising/edit/'.$advert['id'].'" method="post">'."\n");
	?>
			<fieldset>
	<?php
		echo('			<input type="hidden" id="advert_id" name="advert_id" value="'.$advert['id'].'" />'."\n");
	?>
			</fieldset>
			<fieldset>
				<input type="submit" name="submit_make_advert_live" value="Make Advert Live" style="float: right;" />
			</fieldset>
		</form>
	<?php
		}
	?>
		<p>
			delete this advert
		</p>
	<?php
		echo('	<form class="form" action="/office/advertising/edit/'.$advert['id'].'" method="post">'."\n");
	?>
			<fieldset>
	<?php
		echo('			<input type="hidden" id="advert_id" name="advert_id" value="'.$advert['id'].'" />'."\n");
	?>
			</fieldset>
			<fieldset>
				<input type="submit" name="submit_delete_advert" value="Delete Advert" style="float: right;" />
			</fieldset>
		</form>
	</div>
</div>
