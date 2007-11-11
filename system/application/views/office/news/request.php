	<script type='text/javascript' src='/javascript/calendar_select.js'></script>
	<script type='text/javascript' src='/javascript/calendar_select-en.js'></script>
	<script type='text/javascript' src='/javascript/calendar_select-setup.js'></script>

	<div class='RightToolbar'>
		<h4><?php echo $heading; ?></h4>
		<?php echo $intro; ?>
	</div>

	<form name='new_request' id='new_request' action='/office/news/request' method='post' class='form'>
		<div class='blue_box'>
			<fieldset>
				<label for='r_title'>Title:</label>
				<input type='text' name='r_title' id='r_title' value='<?php echo($this->validation->r_title); ?>' size='30' />
				<br />
				<label for='r_brief'>Brief:</label>
				<textarea name='r_brief' id='r_brief' cols='25' rows='5'><?php echo($this->validation->r_brief); ?></textarea>
			    <br />
				<?php if ($user_level == 'editor') { ?>
					<label for='deadline_trigger'>Deadline:</label>
					<div id='r_deadline_show' style='float: left; margin: 5px 10px;'>None</div>
					<input type='hidden' name='r_deadline' id='r_deadline' value='<?php echo($this->validation->r_deadline); ?>' />
					<br />
					<button id='deadline_trigger' style='margin: 0 0 5px 125px;'>Select</button>
					<br />
				<?php } ?>
			 	<label for='r_box'>Box:</label>
				<select name='r_box' id='r_box' size='1'>
				<?php foreach ($boxes as $box) { ?>
		  			<option value='<?php echo $box['code']; ?>'<?php if ($this->validation->r_box == $box['code']) { echo(' selected="selected"'); } ?>><?php echo $box['name']; ?></option>
				<?php } ?>
				</select>
		  		<br />
				<?php if ($user_level == 'editor') { ?>
					<label for='r_reporter'>Reporter(s):</label>
					<select name='r_reporter[]' id='r_reporter' size='4' multiple='multiple'>
					<?php foreach ($reporters as $reporter) {
						echo('<option value="'.$reporter['id'].'"');
						if (is_array($this->input->post('r_reporter'))) {
							if (array_search($reporter['id'],$this->input->post('r_reporter')) !== FALSE) {
								echo(' selected="selected"');
							}
						}
						echo('>'.$reporter['firstname'] . ' ' . $reporter['surname'].'</option>');
					} ?>
					</select>
					<i>Hold down Ctrl to select more than one.</i>
			 		<br />
				<?php } ?>
			</fieldset>
		</div>
	<div style='width: 422px;'>
	 	<input type='submit' name='submit' id='submit' value='Submit <?php echo $status; ?>' class='button' />
	</div>
	</form>
	<?php if ($user_level == 'editor') { ?>
		<script type='text/javascript'>
		Calendar.setup(
			{
				inputField	: 'r_deadline',
				ifFormat	: '%s',
				displayArea	: 'r_deadline_show',
				daFormat	: '%a %e %b, %Y @ %H:%M',
				button		: 'deadline_trigger',
				singleClick	: false,
				firstDay	: 1,
				weekNumbers	: false,
				range		: [<?php echo (date('Y') . ',' . (date('Y') + 1)); ?>],
				showsTime	: true,
				timeFormat	: '24'
			}
		);
		</script>
	<?php } ?>