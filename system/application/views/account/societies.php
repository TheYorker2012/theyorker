<script src='/javascript/prototype.js' type='text/javascript'></script>
<script src='/javascript/scriptaculous.js' type='text/javascript'></script>
<script type='text/javascript' src='/javascript/slideshow.js'></script>
<script type='text/javascript'>
function get_info (socid) {
	document.getElementById('socdesc').innerHTML = "<div class='ajax_loading'><img src='/images/prototype/prefs/loading.gif' alt='Loading' title='Loading' /> Retrieving Description</div>";
	xajax_getInfo(socid.options[socid.selectedIndex].value);
}

// pre-load ajax loading image
imageObj = new Image();
imageObj.src = '/images/prototype/prefs/loading.gif';

function ss_load () { Slideshow.load(); }
function ss_add (img) { Slideshow.add(img); }
function ss_reset () { Slideshow.reset(); }

</script>
<style type='text/css'>
.hide {
	display: none;
}
#ss {
	height: 165px;
	width: 220px;
	float: right;
}
</style>

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
	<div id='socinfo' style='float: right; width: 50%;'>
		fkjd sfjkdsbfj sdbglfsg daf afadf adf da fef e fad fdfd
	</div>
	<div id='ss'>
		<img id='changeme' src='/images/prototype/prefs/image_load.jpg' alt='Society Image' title='Society Image' />
	</div>
	<div style='clear:both;'></div>
	<div id='socdesc' style='border: 1px #000 solid; padding: 5px; font-size: small; float:right; width: 45%; overflow: auto; height: 60px;'>
	fdhsufghdskf s<br />
	fdsf sf dsf dsf dsfdshhkjhk<br />
	hfh ghgoph koghkjlkfmh kp<br />
	p[kop kopgjk ophjg pjkp<br />
	hpog pohjkpjkfpo gpfdjg  fd<br />
	fdhsufghdskf s<br />
	fdsf sf dsf dsf dsfdshhkjhk<br />
	hfh ghgoph koghkjlkfmh kp<br />
	p[kop kopgjk ophjg pjkp<br />
	hpog pohjkpjkfpo gpfdjg  fd<br />
	fdhsufghdskf s<br />
	fdsf sf dsf dsf dsfdshhkjhk<br />
	hfh ghgoph koghkjlkfmh kp<br />
	p[kop kopgjk ophjg pjkp<br />
	hpog pohjkpjkfpo gpfdjg  fd<br />
	fdhsufghdskf s<br />
	fdsf sf dsf dsf dsfdshhkjhk<br />
	hfh ghgoph koghkjlkfmh kp<br />
	p[kop kopgjk ophjg pjkp<br />
	hpog pohjkpjkfpo gpfdjg  fd<br />
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