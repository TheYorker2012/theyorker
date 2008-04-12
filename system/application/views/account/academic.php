<script type='text/javascript'>
// <![CDATA[
	function get_modules (course) {
		document.getElementById('soc_container').innerHTML = "<div class='ajax_loading'><img src='/images/prototype/prefs/loading.gif' alt='Loading' title='Loading' /> Retrieving Module List</div>";
		xajax_getModules(course.options[course.selectedIndex].value);
	}

	function update_subscriptions (module) {
		document.getElementById('current_modules').innerHTML = "<div class='ajax_loading'><img src='/images/prototype/prefs/loading.gif' alt='Loading' title='Loading' /> Updating Your Modules</div>";
		document.getElementById('soc' + module).className = 'loading';
		xajax_updateModules(module);
		return false;
	}
	
	// pre-load ajax loading image
	imageObj = new Image();
	imageObj.src = '/images/prototype/prefs/loading.gif';
// ]]>
</script>
	
<div class='BlueBox' style='width: auto;'>
	<h2><?php echo(xml_escape($heading)); ?></h2>
	<?php echo($intro); ?>
</div>

<div style='float: left; width: 320px;'>
	<h4>Course</h4>
	<div style='text-align:center; margin: 1em 0;'>
		<select name='course' id='course' size='1' onChange='get_modules(this);'>
			<option value='' selected='selected'></option>
		<?php foreach ($courses as $course) { ?>
			<option value='<?php echo($course['department_id']); ?>'><?php echo(xml_escape($course['department_name'])); ?></option>
		<?php } ?>
		</select>
	</div>

	<h4>Modules</h4>
	<div id='soc_container'></div>
</div>

<div style='float: right; width: 320px;'>
	<h4>Subscribed Modules</h4>
	<div id='current_modules' style='font-size: small;'>
		<ul>
		<?php foreach ($current as $module) { ?>
			<li><?php echo(xml_escape($module['module_name'])); ?></li>
		<?php } ?>
		</ul>
	</div>
</div>

<br style='clear: both;' />
<form action='/register/societies/' method='post' class='form'>
	<div style='margin-top: 1em;'>
		<input type='submit' name='submit' id='submit' value='Next >' class='button' />
		<input type='button' name='back' id='back' value='< Back' class='button' onclick="window.location='/register'" />
	</div>
</form>
