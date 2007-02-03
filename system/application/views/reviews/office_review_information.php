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

<form id='reviewinfo' name='reviewinfo' action='/viparea/reviews/<?php echo 'shortname'; ?>/<?php echo 'content_type'; ?>/edit' method='POST' class='form'>
<div class='blue_box'>
	<h2>objective blurb</h2>
	You are currently editing <span class="orange">Bills Bike Shop</span><br />
	<textarea name='reviewinfo_about' cols='48' rows='10'><?php echo 'blurb'; ?></textarea>
	<textarea name='reviewinfo_about' cols='48' rows='4'><?php echo 'rating quote'; ?></textarea>
</div>
<div class='grey_box'>
<h2>details</h2>
	<fieldset>
		<label for='reviewinfo_rating'>Rating:</label>
		<select name="rating">
			<option>0</option>
			<option>0.5</option>
			<option>1</option>
			<option>1.5</option>
			<option selected>2</option>
			<option>3</option>
			<option>3.5</option>
			<option>4</option>
			<option>4.5</option>
			<option>5</option>
		</select>
		<br />
		<label for='reviewinfo_recommended'>Recommended Item:</label>
		<input type='text' name='reviewinfo_recommended' style='width: 220px;' value='<?php echo 'reviewinfo_recommended'; ?>'/>
		<br />
		<label for='reviewinfo_recommended_price'>Recommended Item Price:</label>
		<input type='text' name='reviewinfo_recommended_price' style='width: 220px;' value='<?php echo 'reviewinfo_recommended_price'; ?>'/>
		<br />
		<label for='reviewinfo_average_price_lower'>Average Price:</label>
		&nbsp;&nbsp;£<input type='text' name='reviewinfo_average_price_lower' style='width: 50px;' />&nbsp; - £<input type='text' name='reviewinfo_average_price_upper' style='width: 50px;' />
		<br />
		<label for='reviewinfo_book_online'>Book Online:</label>
		<input type='checkbox' name='reviewinfo_book_online' />
	</fieldset>
</div>
<div class='blue_box'>
	<h2>directions</h2>
	<textarea name='reviewinfo_directions' cols='48' rows='10'><?php echo 'directions'; ?></textarea>
</div>
<div class='grey_box'>
	<h2>save changes</h2>
	If you wish to save the changes you have made to this review information please click Submit.<br /><br />
	<input type="submit" value="Submit" />
</div>
</form>
