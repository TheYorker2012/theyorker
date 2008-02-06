	<script type='text/javascript' src='/javascript/calendar_select.js'></script>
	<script type='text/javascript' src='/javascript/calendar_select-en.js'></script>
	<script type='text/javascript' src='/javascript/calendar_select-setup.js'></script>

	<div class='RightToolbar'>
		<h4>Creating an Article</h4>
		By completing this form you will be creating an article which you can immediately start
		writing without having to wait for approval by an editor. The article will, however, still
		need to be approved by an editor before it can be published onto the website and can be
		cancelled by an editor at any point.
	</div>

	<form name='new_request' id='new_request' action='/office/news/create' method='post' class='form'>
		<div class='blue_box'>
			<fieldset>
				<label for='r_title'>Title:</label>
				<input type='text' name='r_title' id='r_title' value='<?php echo(xml_escape($this->validation->r_title)); ?>' size='30' />
				<br />
				<label for='r_brief'>Brief:</label>
				<textarea name='r_brief' id='r_brief' cols='25' rows='5'><?php echo(xml_escape($this->validation->r_brief)); ?></textarea>
			    <br />
				<label for='deadline_trigger'>Deadline:</label>
				<div id='r_deadline_show' style='float: left; margin: 5px 10px;'>None</div>
				<input type='hidden' name='r_deadline' id='r_deadline' value='<?php echo(xml_escape($this->validation->r_deadline)); ?>' />
				<br />
				<button id='deadline_trigger' style='margin: 0 0 5px 125px;'>Select</button>
				<br />
			 	<label for='r_box'>Section:</label>
				<select name='r_box' id='r_box' size='1'>
				<?php foreach ($boxes as $box) { ?>
		  			<option value='<?php echo(xml_escape($box['code'])); ?>'<?php if ($this->validation->r_box == $box['code']) { echo(' selected="selected"'); } ?>><?php echo(xml_escape($box['name'])); ?></option>
				<?php } ?>
				</select>
		  		<br />
			</fieldset>
		</div>
	<div style='width: 422px;'>
	 	<input type='submit' name='submit' id='submit' value='Create Article' class='button' />
	</div>
	</form>

	<script type='text/javascript'>
	// <![CDATA[
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
			range		: [<?php echo((int)date('Y') . ',' . ((int)date('Y') + 1)); ?>],
			showsTime	: true,
			timeFormat	: '24'
		}
	);
	// ]]>
	</script>