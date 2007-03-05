	<script type='text/javascript' src='/javascript/calendar_select.js'></script>
	<script type='text/javascript' src='/javascript/calendar_select-en.js'></script>
	<script type='text/javascript' src='/javascript/calendar_select-setup.js'></script>

	<div class='RightToolbar'>
		<h4><?php echo $heading; ?></h4>
		<?php echo $intro_text; ?>
	</div>

	<form name='publish_request' id='publish_request' action='/office/news/<?php echo $article['id']; ?>' method='post' class='form'>
		<div class='blue_box'>
			<fieldset>
				<label for='r_title'>Title:</label>
				<div id='r_title' style='float: left; margin: 5px 10px;'><?php echo $article['request_title']; ?></div>
				<br />
			 	<label for='r_box'>Box:</label>
				<div id='r_box' style='float: left; margin: 5px 10px;'><?php echo $article['box_name']; ?></div>
		  		<br />
				<label for='publish_trigger'>Publish Date:</label>
				<div id='r_publish_show' style='float: left; margin: 5px 10px;'><?php echo date('D jS F Y @ H:i',$article['date_deadline']); ?></div>
					<input type='hidden' name='r_publish' id='r_publish' value='<?php echo $article['date_deadline']; ?>' />
					<br />
					<button id='publish_trigger' style='margin: 0 0 5px 125px;'>Select</button>
				<br />
			</fieldset>
		</div>
	<div style='width: 422px;'>
		<input type='hidden' name='publish' id='publish' value='Publish Article' />
	 	<input type='submit' name='confirm_publish' id='confirm_publish' value='Publish' class='button' />
	</div>
	</form>

	<script type='text/javascript'>
	Calendar.setup(
		{
			inputField	: 'r_publish',
			ifFormat	: '%s',
			displayArea	: 'r_publish_show',
			daFormat	: '%a %e %b, %Y @ %H:%M',
			button		: 'publish_trigger',
			singleClick	: false,
			firstDay	: 1,
			date		: '<?php echo $article['date_deadline']; ?>',
			weekNumbers	: false,
			range		: [<?php echo (date('Y') . ',' . (date('Y') + 1)); ?>],
			showsTime	: true,
			timeFormat	: '24'
		}
	);
	</script>