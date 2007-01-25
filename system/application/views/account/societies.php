<script type='text/javascript'>
function get_info (socid) {
	document.getElementById('socinfo').innerHTML = "<div class='ajax_loading'><img src='/images/prototype/prefs/loading.gif' alt='Loading' title='Loading' /> Retrieving Society Information</div>";
	xajax_getInfo(socid.options[socid.selectedIndex].value);
}

// pre-load ajax loading image
imageObj = new Image();
imageObj.src = '/images/prototype/prefs/loading.gif';
</script>

<h4>Account Preferences</h4>
<form class='form' action='/register/societies/' method='post'>
<h5 style='color: #fff;'>Society Subscriptions</h5>
<fieldset>
	<div style='float: left; width: 50%;'>
		<select name='society' id='society' size='10' onChange='get_info(this);' style='width: 200px;'>
			<?php foreach ($societies as $soc) { ?>
			<option value='<?php echo $soc['id']; ?>'><?php echo $soc['name']; ?></option>
			<?php } ?>
		</select>
	</div>
	<div id='socinfo' style='float: left; width: 50%;'>
		fkjd sfjkdsbfj sdbglfsg daf afadf adf da fef e fad fdfd
	</div>
	<br style='clear: both;' />
</fieldset>
<fieldset>
	<label for='submit'></label>
	<input type='button' name='back' id='back' value='< Back' class='button' onClick="window.location='/register/academic'" />
 		<input type='submit' name='submit' id='submit' value='Next >' class='button' />
        <br />
</fieldset>
</form>