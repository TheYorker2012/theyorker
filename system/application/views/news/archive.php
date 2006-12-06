	<div id='newsnav'>
		<ul id='newsnavlist'>
		<li><a href='/news/'><img src='/images/prototype/news/uk.png' alt='Campus News' title='Campus News' /> Campus News</a></li>
		<li><a href='/news/national/'><img src='/images/prototype/news/earth.png' alt='National News' title='National News' /> National News</a></li>
		<li><a href='/news/features/'><img src='/images/prototype/news/feature.gif' alt='Features' title='Features' /> Features</a></li>
		<li><a href='/news/lifestyle/'><img src='/images/prototype/news/feature.gif' alt='Lifestyle' title='Lifestyle' /> Lifestyle</a></li>
		<li class='right'><a href='/news/archive/' id='current'><img src='/images/prototype/news/archive.png' alt='Archive' title='Archive' /> Archive</a></li>
		</ul>
	</div>
	<div id='clear'>&nbsp;</div>

	<form name='archive_search' id='archive_search' action='<?php echo site_url('news/archive/'); ?>' method='post' class='form'>
	    <fieldset>
            <legend>Search Archive</legend>
			<label for='a_category'>Category:</label>
			<select name='a_category' id='a_category' size='1'>
			    <option value='' selected='selected'></option>
				<option value='Campus News'>Campus News</option>
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
			<label for='a_subject'>Subject:</label>
			<select name='a_subject' id='a_subject' size='1'>
  		        <option value='' selected='selected'></option>
  		   		<option value='Event'>Event</option>
  		   		<option value='Christmas'>Christmas</option>
  		   		<option value='Easter'>Easter</option>
  		   		<option value='Police'>Police</option>
  		   		<option value='Vanbrugh'>Vanbrugh</option>
  		   		<option value='Football'>Football</option>
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

		<fieldset style='margin-top: 20px;'>
			<legend>Search Results</legend>
			<div class='NewsOther'>
				<h3></h3>
				<p>
					<a href='/news/article/1'><img src='/images/prototype/news/thumb3.jpg' alt='Some Spy' title='Some Spy' /></a>
					<p class='Headline'><a href='/news/article/1'>Ex-spy death inquiry stepped up.</a></p>
					<p class='Writer'><a href='/directory/view/1'>Jo Shelley</a></p>
					<p class='Date'>2nd December 2006</p>
					<p class='More'><?php echo anchor('news/article/1', 'Read more...'); ?></p>
				</p>
		
				<p>
					<a href='/news/article/2'><img src='/images/prototype/news/thumb2.jpg' alt='Some Spy' title='Some Spy' /></a>
					<p class='Headline'><a href='/news/article/2'>Blair 'sorrow' over slave trade.</a></p>
					<p class='Writer'><a href='/directory/view/1'>Jo Shelley</a></p>
					<p class='Date'>1st December 2006</p>
					<p class='More'><?php echo anchor('news/article/2', 'Read more...'); ?></p>
				</p>
				
				<p>
					<a href='/news/article/3'><img src='/images/prototype/news/thumb3.jpg' alt='Some Spy' title='Some Spy' /></a>
					<p class='Headline'><a href='/news/article/3'>Israel vows ceasefire 'patience'.</a></p>
					<p class='Writer'><a href='/directory/view/1'>Owen Jones</a></p>
					<p class='Date'>30th November 2006</p>
					<p class='More'><?php echo anchor('news/article/3', 'Read more...'); ?></p>
				</p>
		
				<p>
					<a href='/news/article/4'><img src='/images/prototype/news/thumb2.jpg' alt='Some Spy' title='Some Spy' /></a>
					<p class='Headline'><a href='/news/article/4'>Ex-spy death inquiry stepped up.</a></p>
					<p class='Writer'><a href='/directory/view/1'>Owen Jones</a></p>
					<p class='Date'>29th November 2006</p>
					<p class='More'><?php echo anchor('news/article/4', 'Read more...'); ?></p>
				</p>
		
				<p>
					<a href='/news/article/5'><img src='/images/prototype/news/thumb3.jpg' alt='Some Spy' title='Some Spy' /></a>
					<p class='Headline'><a href='/news/article/5'>Ex-spy death inquiry stepped up.</a></p>
					<p class='Writer'><a href='/directory/view/1'>Owen Jones</a></p>
					<p class='Date'>28th November 2006</p>
					<p class='More'><?php echo anchor('news/article/5', 'Read more...'); ?></p>
				</p>
			</div>
		</fieldset>
	</form>