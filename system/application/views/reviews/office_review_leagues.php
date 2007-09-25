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
			<img src="/images/prototype/homepage/infomark.png" />
			<a href='#'>Information</a> has been updated and is waiting to be published.
		</div>
		<div class="information_box">
			There are <a href='#'>Comments</a> that have been reported for abuse.
		</div>
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


	<div class="blue_box">
	<h2>current leagues</h2>
	<p>
	
	<?php
	echo form_open('office/reviews/delleague');
	echo form_hidden('organisation_name',$organisation_name);
	echo form_hidden('context_type',$context_type);
	?>
	
	<select size="5" name="league_id" style="float: none; width: 350px;">
	
	<?php
	foreach ($existing_leagues as $existing_league)
	{
		echo '<option value="'.$existing_league['id'].'">'.$existing_league['section_name'].' -> '.$existing_league['name'].' ( '.$existing_league['rank'].' / '.$existing_league['size'].' )</option>';
	}
	?>
	
	</select><br />
 	<input type="submit" value="Delete league" style="float: none;">
	</form>
	
	</p>
	</div>
	
	<div class="blue_box">
	<h2>potential leagues</h2>
	<p>
	
	<?php
	echo form_open('office/reviews/addleague');
	echo form_hidden('organisation_name',$organisation_name);
	echo form_hidden('context_type',$context_type);
	?>
	
	<select size="8" name="league_id" style="float: none;  width: 350px;">
	
	<?php
	foreach ($new_leagues as $new_league)
	{
		echo '<option value="'.$new_league['id'].'">'.$new_league['section_name'].' -> '.$new_league['name'].'</option>';
	}
	?>
	
	</select><br />
 	<input type="submit" value="Add new league" style="float: none;">
	</form>
	
	</p>
	</div>

