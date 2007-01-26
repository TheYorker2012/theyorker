<div id='newsnav'>
	<ul id='newsnavlist'>
	<li><a href='<?php echo site_url('admin/'); ?>'><img src='/images/prototype/news/archive.png' alt='Admin' title='Admin' /> Admin</a></li>
	<li><a href='<?php echo site_url('admin/news/'); ?>'><img src='/images/prototype/news/uk.png' alt='News' title='News' /> News</a></li>
	<li><a href='<?php echo site_url('admin/news/request/new/'); ?>' id='current'><img src='/images/prototype/news/earth.png' alt='Request' title='Request' /> New Request</a></li>
	</ul>
</div>
<div id='clear'>&nbsp;</div>

<form name='' id='' action='' method='post' class='form'>
    <fieldset>
        <legend>Request Details</legend>
		<label for='r_title'>Title:</label>
		<input type='text' name='r_title' id='r_title' value='' size='30' />
		<br />
		<label for='r_brief'>Brief:</label>
		<textarea name='r_brief' id='r_brief' cols='40' rows='5'></textarea>
	    <br />
		<label for='r_deadline_day'>Deadline:</label>
		<select name='r_deadline_day' id='r_deadline_day' size='1'>
		    <option value='' selected='selected'></option>
			<?php
			for ($day = 1; $day <= 31; $day++) {
			    echo("<option value='$day'>$day</option>");
			} ?>
		</select> /
		<select name='r_deadline_month' id='r_deadline_month' size='1'>
		    <option value='' selected='selected'></option>
			<?php
			for ($month = 1; $month <= 12; $month++) {
			    echo("<option value='$month'>$month</option>");
			} ?>
		</select>
		<br />
    </fieldset>
    <fieldset>
        <legend>Request Assignment</legend>
	 	<label for='r_box'>Box:</label>
		<select name='r_box' id='r_box' size='1'>
		    <option value='' selected='selected'></option>
  			<option value='News'>News</option>
  		   	<option value='Features'>Features</option>
			<option value='Lifestyle'>Lifestyle</option>
		</select>
  		<br />
		<label for='r_reporter'>Reporter(s):</label>
		<select name='r_reporter' id='r_reporter' size='4' multiple='multiple'>
  		    <option value='' selected='selected'></option>
  		   	<option value='Dan Ashby'>Dan Ashby</option>
 			<option value='Nick Evans'>Nick Evans</option>
			<option value='Chris Travis'>Chris Travis</option>
  		   	<option value='John Doe'>John Doe</option>
  		   	<option value='Jane Doe'>Jane Doe</option>
  		   	<option value='Alan Smith'>Alan Smith</option>
  		   	<option value='Danielle Gerrard'>Danielle Gerrard</option>
		</select>
		<i>Hold down Ctrl to select more than one.</i>
 		<br />
    </fieldset>
    <fieldset>
		<label for='r_submit'></label>
 		<input type='submit' name='r_submit' id='r_submit' value='Submit' class='button' />
 		<input type='reset' name='r_clear' id='r_clear' value='Clear' class='button' />
        <br />
	</fieldset>
</form>