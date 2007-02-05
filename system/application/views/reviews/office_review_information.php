<div class='RightToolbar'>
	<h4>Areas for Attention</h4>
	<div class="Entry">
		<div class="information_box">
			The following reviews are waiting to be published:
			<ul>
				<li><a href='#'>Dan Ashby 02/02/2007</a></li>
				<li><a href='#'>Charlotte Chung 02/02/2007</a></li>
			</ul>
		</div>
		<div class="information_box">
			<img src="/images/prototype/homepage/warning.png" />
			<a href='#'>Information</a> has been updated and is waiting to be published.
		</div>
		<div class="information_box">
			There are <a href='#'>Comments</a> that have been reported for abuse.
		</div>
	</div>
	<h4>Revisions</h4>
	<div class="Entry">
		<ol>
			<li>Dan Ashby 04/02/2007 3:39PM
			<li>Nick Evans 04/02/2007 3:20PM <span class="orange">(Published)</span>
			<li>Dan Ashby 03/02/2007 3:11PM 
			<li>John Smith 03/02/2007 3:11PM 
			<li>Rich Rout 02/02/2007 1:11AM 
		</ol>
	</div>

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
		<label for='reviewinfo_quote'>Summary Quote:</label>
		<textarea name='reviewinfo_quote' cols='25' rows='4'><?php echo ''; ?></textarea>
		<br />
		<label for='reviewinfo_recommended'>Recommended Item:</label>
		<input type='text' name='reviewinfo_recommended' style='width: 220px;' value='<?php echo ''; ?>'/>
		<br />
		<label for='reviewinfo_recommended_price'>Average Price:</label>
		<input type='text' name='reviewinfo_averageprice' style='width: 220px;' value='<?php echo ''; ?>'/>
		<br />
		<label for='reviewinfo_serving_hours'>Serving Hours:</label>
		<textarea name='reviewinfo_serving_hours' cols='25' rows='4'><?php echo ''; ?></textarea>
		<br />
		<label for='reviewinfo_deals'>Deals:</label>
		<textarea name='reviewinfo_deal' cols='25' rows='4'><?php echo ''; ?></textarea>
		<br />
		<label for='reviewinfo_deal_expires'>Deal Expires:</label>
		<input type='text' name='reviewinfo_deal_expires' style='width: 220px;' value='<?php echo ''; ?>'/>
	</fieldset>
</div>

<div class='grey_box'>
	<h2>save changes</h2>
	If you wish to save the changes you have made to this review information please click Submit.<br /><br />
	<input type="submit" value="Submit" />
</div>
</form>
