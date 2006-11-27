	<div id='newsnav'>
		<ul id='newsnavlist'>
		<li><a href='<?php echo site_url('news/'); ?>'><img src='<?php echo site_url('images/prototype/news/uk.png'); ?>' alt='News' title='News' /> News</a></li>
		<li><a href='<?php echo site_url('news/national/'); ?>'><img src='<?php echo site_url('images/prototype/news/earth.png'); ?>' alt='National' title='National' /> National</a></li>
		<li><a href='<?php echo site_url('news/features/'); ?>'><img src='<?php echo site_url('images/prototype/news/feature.gif'); ?>' alt='Feature' title='Feature' /> Features</a></li>
		<li><a href='<?php echo site_url('news/lifestyle/'); ?>'><img src='<?php echo site_url('images/prototype/news/feature.gif'); ?>' alt='Lifestyle' title='Lifestyle' /> Lifestyle</a></li>
		<li><a href='<?php echo site_url('news/archive/'); ?>' id='current'><img src='<?php echo site_url('images/prototype/news/archive.png'); ?>' alt='Archive' title='Archive' /> Archive</a></li>
		</ul>
	</div>
	<div id='clear'>&nbsp;</div>
	    <form name='a_search_form' id='a_search_form' action='<?php echo site_url('news/archive/'); ?>' method='post'>
		<fieldset id='SearchForm' title='Search Archive'>
		    <legend>Search Archive</legend>
			<p><label for='a_category'>Category:</label>
			<select name='a_category' id='a_category' size='1'>
			<option value='' selected='selected'></option>
			<option value='News'>News</option>
			<option value='National'>National</option>
	 		<option value='Features'>Features</option>
	 		<option value='Lifestyle'>Lifestyle</option>
			</select></p>
			<p><label for='a_reporter'>Reporter:</label>
			<select name='a_reporter' id='a_reporter' size='1'>
			<option value='' selected='selected'></option>
			<option value='1'>Ian Benest</option>
			<option value='2'>Dan Ashby</option>
			</select></p>
			<p><label for='a_text'>Text:</label> <input type='text' name='a_text' id='a_text' value='' /></p>
		    <p><label for='a_submit'>&nbsp;</label> <input type='submit' name='a_submit' id='a_submit' value='Search' class='submit' /></p>
		</fieldset>
		</form>
	</div>