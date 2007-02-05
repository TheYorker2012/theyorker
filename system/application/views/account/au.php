	<script type='text/javascript' src='/javascript/prototype.js'></script>
	<script type='text/javascript' src='/javascript/scriptaculous.js'></script>
	<script type='text/javascript' src='/javascript/slideshow.js'></script>
	<script type='text/javascript' src='/javascript/subscriptions.js'></script>
	<script type='text/javascript'>
	var societies = Array();
	<?php foreach ($societies as $soc) { ?>
	societies['<?php echo $soc['id']; ?>'] = Array();
	societies['<?php echo $soc['id']; ?>']['name'] = '<?php echo $soc['name']; ?>';
	societies['<?php echo $soc['id']; ?>']['directory'] = '<?php echo $soc['directory']; ?>';
	societies['<?php echo $soc['id']; ?>']['url'] = '<?php echo $soc['url']; ?>';
	<?php } ?>
	</script>

	<div class='blue_box' style='width: auto;'>
		<h2><?php echo $heading; ?></h2>
		<?php echo $intro; ?>
	</div>
	<div style='float: left; width: 320px;'>
		<h4>Clubs</h4>
		<div id='soc_container'>
			<?php foreach ($societies as $soc) {
				echo '<div id=\'soc' . $soc['id'] . '\' class=\'';
				if (array_search($soc['id'],$society_subscriptions) !== FALSE) {
					echo 'selected';
				} else {
					echo 'unselected';
				}
				echo '\'><a href=\'/register/au/' . $soc['id']. '/\' onclick="return get_info(\'' . $soc['id'] . '\');">' . $soc['name'] . '</a></div>';
			} ?>
		</div>
		<!--
		<select name='society' id='society' size='10' onChange='get_info(this);' style='width: 200px;'>
			<?php foreach ($societies as $soc) { ?>
			<option value='<?php echo $soc['id']; ?>'><?php echo $soc['name']; ?></option>
			<?php } ?>
		</select>
		-->
	</div>
	<div style='float: right; width: 320px;'>
		<h4 id='socname'>&nbsp;</h4>
		<div align='center'>
			<div id='ss' style='text-align:left;'>
				<img id='changeme' src='/images/prototype/prefs/image_load.jpg' alt='Society Image' title='Society Image' />
			</div>
		</div>
		<div style='clear:both;'>&nbsp;</div>
		<b>Description...</b>
		<div id='socdesc' class='blue_box' style='overflow:auto; height: 80px; width: auto;'></div>
		<div id='socinfo'></div>
		<div style='text-align:center;'>
			<form id='form_subscribe' action='/register/au/' method='post' class='form' onsubmit='return socSubscribe();'>
			<input type='submit' name='soc_subscribe' id='soc_subscribe' value='Subscribe' class='button hide' />
			<img src='/images/prototype/prefs/loading.gif' alt='Loading' title='Loading' id='sub_loading' class='hide' style='float:right;' />
			</form>
		</div>
	</div>
	<br style='clear: both;' />
	<form action='/register/au/' method='post' class='form'>
		<div style='margin-top: 1em;'>
		 	<input type='submit' name='submit' id='submit' value='Next >' class='button' />
			<input type='button' name='back' id='back' value='< Back' class='button' onclick="window.location='/register/societies'" />
		</div>
	</form>