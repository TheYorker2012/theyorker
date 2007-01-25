<script type='text/javascript'>
function get_modules (course) {
	document.getElementById('module_list').innerHTML = "<div class='ajax_loading'><img src='/images/prototype/prefs/loading.gif' alt='Loading' title='Loading' /> Retrieving Module List</div>";
	xajax_getModules(course.options[course.selectedIndex].value);
}

function update_subscriptions (module) {
	document.getElementById('current_modules').innerHTML = "<div class='ajax_loading'><img src='/images/prototype/prefs/loading.gif' alt='Loading' title='Loading' /> Updating Your Modules</div>";
	xajax_updateModules(module);
}

// pre-load ajax loading image
imageObj = new Image();
imageObj.src = '/images/prototype/prefs/loading.gif';
</script>

<h4>Account Preferences</h4>
<form class='form' action='/register/societies/' method='post'>
<h5 style='color: #fff;'>Academic Module Subscriptions</h5>
<fieldset>
	<label for='course'>Course:</label>
	<select name='course' id='course' size='1' onChange='get_modules(this);'>
		<option value='' selected='selected'></option>
	<?php foreach ($courses as $course) { ?>
		<option value='<?php echo $course['department_id']; ?>'><?php echo $course['department_name']; ?></option>
	<?php } ?>
	</select>
	<br />
	<div id='module_list' style='width: 100%; float: left; margin-top: 15px;'></div>
	<br />
</fieldset>
<h5 style='color: #fff;'>Current Subscriptions</h5>
<fieldset>
	<div id='current_modules' style='width: 100%; float: left; margin-top: 15px;'>
		<div class='table-box'>
			<table>
			<thead>
				<tr>
				<th>&nbsp;</th>
				<th>Course</th>
				<th>Module Title</th>
				<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
				<td align='center' colspan='4'>No Modules Found</td>
				</tr>
			</tbody>
			</table>
		</div>
	</div>
</fieldset>
<fieldset>
	<label for='submit'></label>
	<input type='button' name='back' id='back' value='< Back' class='button' onClick="window.location='/register/general'" />
 		<input type='submit' name='submit' id='submit' value='Next >' class='button' />
        <br />
</fieldset>
</form>