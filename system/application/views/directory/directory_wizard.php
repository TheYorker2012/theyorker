<div class='RightToolbar'>
<h4>What's this?</h4>
	<div class="Entry">
		<?php echo $main_text; ?>
	</div>
</div>
<form id='entry_general' name='entry_general' action='/directorywizard/' method='POST' class='form'>
	<div class='grey_box'>
	<h2>General information</h2>
		<fieldset>
			<label for='organisations_name'>Organisation's name:</label>
			<input type='text' name='organisations_name' style='width: 220px;' value='<?php if (!empty($information['organisations_name'])) { echo $information['organisations_name']; }?>'/>
			<br />
			<label for='organisation_type'>Organisation type:</label>
			<select name="organisation_type" size="1">
				<?php
				foreach($org_types as $org_type){
					echo "<option value='".$org_type['organisation_type_id']."' ";
					if (!empty($information['organisation_type'])){
						if($information['organisation_type'] == $org_type['organisation_type_id']){
						echo "selected";
						}
					}
					echo ">".$org_type['organisation_type_name']."</option>";
				}
				?>
			</select>
			<br />
			<label for='organisations_description'>Description:</label>
			<textarea name='organisations_description' cols='35' rows='10'><?php if (!empty($information['organisations_description'])) { echo $information['organisations_description']; }?></textarea>
		</fieldset>
	</div>
	<div class='blue_box'>
		<h2>Your details</h2>
		<fieldset>
			<label for='suggestors_name'>Your name:</label>
			<input type='text' name='suggestors_name' style='width: 220px;' value='<?php if (!empty($information['suggestors_name'])) { echo $information['suggestors_name']; }?>'/>
			<br />
			<label for='suggestors_position'>Your position:</label>
			<input type='text' name='suggestors_position' style='width: 220px;' value='<?php if (!empty($information['suggestors_position'])) { echo $information['suggestors_position']; }?>'/>
		</fieldset>
	</div>
	
	<div class='grey_box' id='organisation_details_fake_div' style="display: block">
		<h2>Organisation's details</h2>
		<fieldset>
			<input type='button' name='show_org_details' value='Add details' onclick="document.getElementById('organisation_details_div').style.display = 'block'; document.getElementById('organisation_details_fake_div').style.display = 'none';" class='button' />
		</fieldset>
	</div>
	
	<div class='grey_box' id='organisation_details_div' style="display:none ">
		<h2>Organisation's details</h2>
		<fieldset>
			<label for='organisation_address'>Address:</label>
			<textarea name='organisation_address' style='width: 220px;' rows='4'><?php if (!empty($information['organisation_address'])) { echo $information['organisation_address']; }?></textarea>
			<br />
			<label for='organisation_postcode'>Postcode:</label>
			<input type='text' name='organisation_postcode' style='width: 150px;' value='<?php if (!empty($information['organisation_postcode'])) { echo $information['organisation_postcode']; }?>'/>
			<br />
			<label for='opening_times'>Opening/Meeting Times:</label>
			<textarea name='opening_times' style='width: 150px;' rows='4'><?php if (!empty($information['opening_times'])) { echo $information['opening_times']; }?></textarea>
			<br />
			<label for='contact_email'>Contact email:</label>
			<input type='text' name='contact_email' style='width: 220px;' value='<?php if (!empty($information['contact_email'])) { echo $information['contact_email']; }?>'/>
			<br />
			<label for='phone_internal'>Phone Internal:</label>
			<input type='text' name='phone_internal' style='width: 150px;' value='<?php if (!empty($information['phone_internal'])) { echo $information['phone_internal']; }?>'/>
			<br />
			<label for='phone_external'>Phone External:</label>
			<input type='text' name='phone_external' style='width: 150px;' value='<?php if (!empty($information['phone_external'])) { echo $information['phone_external']; }?>'/>
			<br />
			<label for='fax_number'>Fax Number:</label>
			<input type='text' name='fax_number' style='width: 150px;' value='<?php if (!empty($information['fax_number'])) { echo $information['fax_number']; }?>'/>
			<br />
			<label for='organisation_website'>Website:</label>
			<input type='text' name='organisation_website' style='width: 150px;' value='<?php if (!empty($information['organisation_website'])) { echo $information['organisation_website']; }?>'/>
			<br />
			<input type='button' name='show_org_details' value='Hide details' onclick="document.getElementById('organisation_details_div').style.display = 'none'; document.getElementById('organisation_details_fake_div').style.display = 'block';" class='button' />
			</fieldset>
	</div>
	
	<div class='blue_box' id='map_fake_div' style="display: block">
		<h2>Map</h2>
		<fieldset>
			<input type='button' name='show_org_details' value='Create map' onclick="document.getElementById('map_div').style.display = 'block'; document.getElementById('map_fake_div').style.display = 'none';" class='button' />
		</fieldset>
	</div>
	<div class='blue_box' id='map_div' style="display: none">
		<h2>Map</h2>
		<fieldset>
			<input type='button' name='show_org_details' value='Hide map' onclick="document.getElementById('map_div').style.display = 'none'; document.getElementById('map_fake_div').style.display = 'block';" class='button' />
		</fieldset>
	</div>

	<div class='grey_box' id='photos_fake_div' style="display: block">
		<h2>Photos</h2>
		<fieldset>
			<input type='button' name='show_org_details' value='Hide photos' onclick="document.getElementById('photos_div').style.display = 'block'; document.getElementById('photos_fake_div').style.display = 'none';" class='button' />
		</fieldset>
	</div>
	<div class='grey_box' id='photos_div' style="display: none">
		<h2>Photos</h2>
		<fieldset>
			<input type='button' name='show_org_details' value='Add photos' onclick="document.getElementById('photos_div').style.display = 'none'; document.getElementById('photos_fake_div').style.display = 'block';" class='button' />
		</fieldset>
	</div>

	<div class='blue_box'>
		<h2>Submit for review</h2>
		<p><?php echo $submit_text; ?></p>
		<fieldset>
			<label for='submitbutton'></label>
			<input type='submit' name='submitbutton' value='Submit' class='button' />
		</fieldset>
	</div>
</form>