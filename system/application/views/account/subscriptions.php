	<script type='text/javascript' src='/javascript/prototype.js'></script>
	<script type='text/javascript' src='/javascript/scriptaculous.js?load=effects'></script>
	<script type='text/javascript' src='/javascript/slideshow_new.js'></script>
	<script type='text/javascript' src='/javascript/subscriptions.js'></script>
	<script type='text/javascript'>
	var societies = Array();
	<?php foreach ($organisations as $soc) { ?>
	societies['<?php echo $soc['id']; ?>'] = Array();
	societies['<?php echo $soc['id']; ?>']['name'] = '<?php echo str_replace("'", "\'", $soc['name']); ?>';
	societies['<?php echo $soc['id']; ?>']['directory'] = '<?php echo str_replace("'", "\'", $soc['directory']); ?>';
	societies['<?php echo $soc['id']; ?>']['url'] = '<?php echo str_replace("'", "\'", $soc['url']); ?>';
	<?php } ?>
	var subscription_type = '<?php echo($type); ?>';
	</script>

	<div class="BlueBox">
		<h2><?php echo $heading; ?></h2>
		<?php echo $intro; ?>
	</div>

	<div class="BlueBox" style="width: 49%;">
		<h2><?php echo($friendly_name); ?></h2>

		<div id='subscription_container'>
			<?php foreach ($organisations as $soc) {
				echo '<div id=\'soc' . $soc['id'] . '\' class=\'';
				if (array_search($soc['id'],$organisation_subscriptions) !== FALSE) {
					echo 'selected';
				} else {
					echo 'unselected';
				}
				echo '\'><a href=\'/register/' . $type . '/' . $soc['id']. '/\' onclick="return get_info(\'' . $soc['id'] . '\');">' . $soc['name'] . '</a></div>';
			} ?>
		</div>
		<!--
		<select name='society' id='society' size='10' onChange='get_info(this);' style='width: 200px;'>
			<?php foreach ($organisations as $soc) { ?>
			<option value='<?php echo $soc['id']; ?>'><?php echo $soc['name']; ?></option>
			<?php } ?>
		</select>
		-->
	</div>

	<div id="subscription_info" class="BlueBox">
		<h2 id='subscription_name'>&nbsp;</h2>

		<div id="SlideShowContainer">
			<div id="SlideShow">
			   <img id="SlideShowImage" src="/images/prototype/prefs/image_load.jpg" alt="Subscription Image" title="Subscription Image" />
			</div>
		</div>
		<form id='form_subscribe' action='/register/<?php echo($type); ?>/' method='post' class='form' onsubmit='return orgSubscribe();'>
			<div style='text-align:center;'>
				<img src='/images/prototype/prefs/loading.gif' alt='Loading' title='Loading' id='subscription_loading' class='hide' style='float:right;' />
				<input type='submit' name='subscription_subscribe' id='subscription_subscribe' value='Subscribe' class='button hide' />
				<br style='clear: both;' />
			</div>
		</form>
		<h3>Description...</h3>
		<div id='subscription_desc' class='blue_box'></div>
	</div>

	<br style='clear: both;' />
	<form action='<?php echo($button_next); ?>' method='post' class='form'>
		<div style='margin-top: 1em;'>
		 	<input type='submit' name='submit' id='submit' value='Next >' class='button' />
			<input type='button' name='back' id='back' value='< Back' class='button' onclick="window.location='<?php echo($button_back); ?>'" />
		</div>
	</form>
