<div class='RightToolbar'>
<h4>Areas for Attention</h4>
The following reviews are waiting to be published:
<ul>
	<li><a href='#'>Dan Ashby 02/02/2007</a></li>
	<li><a href='#'>Charlotte Chung 02/02/2007</a></li>
</ul>
<p>
<a href='#'>Information</a> has been updated and is waiting to be published.
</p>
<p>
There are <a href='#'>Comments</a> that have been reported for abuse.
</p>
<h4>What's this?</h4>
	<p>
		<?php echo 'whats_this'; ?>
	</p>
<h4>Other tasks</h4>
<ul>
	<li><a href='#'>Maintain my account</a></li>
	<li><a href='#'>Remove this directory entry</a></li>
</ul>
</div>

<form class='form'>
<div class="blue_box">
	<h2>edit review</h2>
	<fieldset>
		<label for='review_author'>Author:</label>
		<select name="rating">
			<option>Bob</option>
			<option>Bill</option>
			<option>Jim McJim</option>
			<option>Fat Man Scoop</option>
			<option selected>ADO</option>
			<option>Lord of Darkness</option>
			<option>Tom</option>
			<option>Andy</option>
			<option>Nick</option>
			<option>James</option>
		</select>
		<label for='review'>Review:</label>
		<textarea name='reviewinfo_about' cols='40' rows='10'><?php echo 'review'; ?></textarea>
	</fieldset>
	<input type="submit" value="Submit" />
</div>
</form>
<div class="grey_box">
	<h2>delete review</h2>
	If you wish to delete this review, click the button to do so...<br />
	<form>
		<input type="submit" value="Delete" />
	</form><br />
</div>
