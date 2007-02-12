<div class='RightToolbar'>
	<h4>Areas for Attention</h4>
	<div class="Entry">
		<div class="information_box">
			The following reviews are waiting to be published:
			<ul>
			<?php
			$new = false;
			for ($i = (count($revisions) - 1); $i >= 0; $i--)
			{
    			if ($new) echo '<li><a href=\'\'>'.$revisions[$i]['name'].' '.date('d/m/Y').'</a></li>';
    			elseif ($revisions[$i]['is_published']) $new = true;
			}
			?>
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
		<?php
		foreach ($revisions as $revision)
		{
    		echo '<li>'.$revision['name'].' '.date('d/m/Y h:iA', $revision['timestamp']);
    		if ($revision['is_published']) echo ' <span class="orange">(Published)</span>';
		}
		?>
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
	You are currently editing <span class="orange"><?php echo $organisation['name']; ?></span><br />
	<textarea name='reviewinfo_about' cols='48' rows='10'><?php echo $content_blurb; ?></textarea>
</div>
<div class='grey_box'>
<h2>details</h2>
	<fieldset>
		<label for='reviewinfo_rating'>Rating:</label>
		<select name="rating">
		<?php
		for ($rating = 0; $rating <= 10; $rating++)
		{
    		echo '<option';
    		if ($rating == $content_rating) echo ' selected';
    		echo '>'.($rating / 2).'</option>';
		}
		?>
		</select>
		<br />
		<label for='reviewinfo_quote'>Summary Quote:</label>
		<textarea name='reviewinfo_quote' cols='25' rows='4'><?php echo $content_quote; ?></textarea>
		<br />
		<label for='reviewinfo_recommended'>Recommended Item:</label>
		<input type='text' name='reviewinfo_recommended' style='width: 220px;' value='<?php echo $recommended_item; ?>'/>
		<br />
		<label for='reviewinfo_recommended_price'>Average Price:</label>
		<input type='text' name='reviewinfo_averageprice' style='width: 220px;' value='<?php echo $average_price; ?>'/>
		<br />
		<label for='reviewinfo_serving_hours'>Serving Hours:</label>
		<textarea name='reviewinfo_serving_hours' cols='25' rows='4'><?php echo $serving_times; ?></textarea>
		<br />
		<label for='reviewinfo_deals'>Deals:</label>
		<textarea name='reviewinfo_deal' cols='25' rows='4'><?php echo $deal; ?></textarea>
		<br />
		<label for='reviewinfo_deal_expires'>Deal Expires:</label>
		<input type='text' name='reviewinfo_deal_expires' style='width: 220px;' value='<?php echo $deal_expires; ?>'/>
	</fieldset>
</div>

<div class='grey_box'>
	<h2>save changes</h2>
	If you wish to save the changes you have made to this review information please click Submit.<br /><br />
	<input type="submit" value="Submit" />
</div>
</form>
