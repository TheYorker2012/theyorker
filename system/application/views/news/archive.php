	<div id='newsnav'>
		<ul id='newsnavlist'>
		<li><a href='<?php echo site_url('news/'); ?>'><img src='/images/prototype/news/uk.png' alt='News' title='News' /> News</a></li>
		<li><a href='<?php echo site_url('news/national/'); ?>'><img src='/images/prototype/news/earth.png' alt='National' title='National' /> National</a></li>
		<li><a href='<?php echo site_url('news/features/'); ?>'><img src='/images/prototype/news/feature.gif' alt='Feature' title='Feature' /> Features</a></li>
		<li><a href='<?php echo site_url('news/lifestyle/'); ?>'><img src='/images/prototype/news/feature.gif' alt='Lifestyle' title='Lifestyle' /> Lifestyle</a></li>
		<li><a href='<?php echo site_url('news/archive/'); ?>' id='current'><img src='/images/prototype/news/archive.png' alt='Archive' title='Archive' /> Archive</a></li>
		</ul>
	</div>
	<div id='clear'>&nbsp;</div>

	<form name='archive_search' id='archive_search' action='<?php echo site_url('news/archive/'); ?>' method='post' class='form'>
	    <fieldset>
            <legend>Search Archive</legend>
			<label for='a_category'>Category:</label>
			<select name='a_category' id='a_category' size='1'>
			    <option value='' selected='selected'></option>
				<option value='News'>News</option>
	 			<option value='Features'>Features</option>
	 			<option value='Lifestyle'>Lifestyle</option>
			</select>
			<br />
			<label for='a_reporter'>Reporter:</label>
			<select name='a_reporter' id='a_reporter' size='1'>
  		        <option value='' selected='selected'></option>
  		   		<option value='Dan Ashby'>Dan Ashby</option>
 				<option value='Nick Evans'>Nick Evans</option>
				<option value='Chris Travis'>Chris Travis</option>
  		   		<option value='John Doe'>John Doe</option>
  		   		<option value='Jane Doe'>Jane Doe</option>
  		   		<option value='Alan Smith'>Alan Smith</option>
  		   		<option value='Danielle Gerrard'>Danielle Gerrard</option>
			</select>
			<br />
			<label for='a_text'>Text:</label>
			<input type='text' name='a_text' id='a_text' value='' />
			<br />
	    </fieldset>
	    <fieldset>
			<label for='r_submit'></label>
	 		<input type='submit' name='r_submit' id='r_submit' value='Search' class='button' />
	 		<input type='reset' name='r_clear' id='r_clear' value='Clear' class='button' />
	        <br />
		</fieldset>
	</form>