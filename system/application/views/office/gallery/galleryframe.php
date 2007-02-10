<div class="RightToolbar">
	<h4>Search by...</h4>
	<div class="Entry">
		<form class="form">
			<fieldset>
			<input type="radio" name="searchcriteria" />Title<br />
			<input type="radio" name="searchcriteria" />Tag<br />
			<input type="radio" name="searchcriteria" />Photographer<br /><br />
			Search Criteria:<input type="text" />
			<input type="submit" class="button" value="Search" />
			</fieldset>
		</form>
	</div>
	<h4>Advanced</h4>
	<div class="Entry">
		<form class="form" method="post" action="<?=site_url('office/gallery')?>">
			<fieldset>
			Order by:<br />
			<input type="radio" name="order" value="title"/>Title<br />
			<input type="radio" name="order" value="date"/>Date<br />
			<input type="radio" name="order" value="photographer"/>Photographer<br /><br />
			Show only tag:<br />
			<select name="tag">
				<?php foreach($tags as $tag):?>
				<option value="<?=$tag->tag_id?>"><?=$tag->name?></option>
				<?php endforeach;?>
			</select><br /><br />
			Show only photographers:<br />
			<select name="tag">
				<?php foreach($photographer as $person):?>
				<option value="<?=$person->user_id?>"><?=$person->user_firstname?> <?=$person->user_surname?></option>
				<?php endforeach;?>
			</select><br /><br />
			<input type="submit" class="button" value="Display" />
			</fieldset>
		</form>
	</div>
</div>
<div class="blue_box">
	<?php
		// Load a subview.
		$content[0]->Load();
		
		echo $pageNumbers;
	?>
</div>
