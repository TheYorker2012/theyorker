<div class="BlueBox">
	<h2>search archive</h2>
	<form name="archive_search" id="archive_search" action="<?php echo site_url('news/archive/'); ?>" method="post">
		<fieldset>
			<label for="a_category">Category:</label>
				<select name="a_category" size="1">
					<option value="" selected="selected">&nbsp;</option>
					<option value="Campus News">Campus News</option>
					<option value="Features">Features</option>
					<option value="Lifestyle">Lifestyle</option>
				</select><br />

			<label for="a_reporter">Reporter:</label>
				<select name="a_reporter" size="1">
					<option value="" selected="selected">&nbsp;</option>
					<option value="Dan Ashby">Dan Ashby</option>
					<option value="Nick Evans">Nick Evans</option>
					<option value="Chris Travis">Chris Travis</option>
					<option value="John Doe">John Doe</option>
					<option value="Jane Doe">Jane Doe</option>
					<option value="Alan Smith">Alan Smith</option>
					<option value="Danielle Gerrard">Danielle Gerrard</option>
				</select><br />

			<label for="a_subject">Subject:</label>
				<select name="a_subject" id="a_subject" size="1">
					<option value="" selected="selected">&nbsp;</option>
					<option value="Event">Event</option>
					<option value="Christmas">Christmas</option>
					<option value="Easter">Easter</option>
					<option value="Police">Police</option>
					<option value="Vanbrugh">Vanbrugh</option>
					<option value="Football">Football</option>
				</select><br />

			<label for="a_text">Text:</label>
				<input type="text" name="a_text" value="" /><br />
		</fieldset>
		<fieldset>
			<input type="submit" name="r_submit" value="Search" class="button" />
		</fieldset>
	</form>
</div>

<div class="BlueBox">
	<h2>search results</h2>
	<div class="NewsBrief">
		<a href='/news/article/1'><img src='/images/prototype/news/thumb3.jpg' alt='Some Spy' title='Some Spy' /></a>
		<h3><a href='/news/article/1'>Ex-spy death inquiry stepped up.</a></h3>
		<p><a href="/contact">Jo Shelley</a></p>
		<p>2nd December 2006</p>
		<p><?php echo(anchor('news/article/1', 'Read more...')); ?></p>
		<!-- Blurb should also be here -->
	</div>
	<div class="NewsBrief">
		<a href='/news/article/1'><img src='/images/prototype/news/thumb2.jpg' alt='Some Spy' title='Some Spy' /></a>
		<h3><a href='/news/article/1'>Blair 'sorrow' over slave trade.</a></h3>
		<p><a href="/contact">Jo Shelley</a></p>
		<p>1st December 2006</p>
		<p><?php echo(anchor('news/article/2', 'Read more...')); ?></p>
		<!-- Blurb should also be here -->
	</div>
</div>
