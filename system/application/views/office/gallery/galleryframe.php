<div class="RightToolbar">
	<h4>Actions</h4>
	<form class="form" method="post" name="clearform" action="<?=site_url('office/gallery')?>">
		<div class="Entry">
			<input type="hidden" name="clear" value="clear" />
			<a href="<?=site_url('office')?>">Return to the Office</a><br />
			<?php if($this->uri->segment(4)) {
				echo '<a href="'.site_url('office/gallery').'">Return to the Gallery</a><br />';
			}?>
			<a href="javascript:document.clearform.submit()">Start a new Search</a>
			<a href="<?=site_url('office/gallery/upload')?>">Upload New Photos</a><br />
		</div>
	</form>
	<h4>Search by...</h4>
	<form class="form" method="post" action="<?=site_url('office/gallery')?>">
		<div class="Entry">
			<fieldset>
			<input type="radio" name="searchcriteria" value="title" selected />Title<br />
			<input type="radio" name="searchcriteria" value="date" />Tag<br />
			<input type="radio" name="searchcriteria" value="photographer" />Photographer<br /><br />
			Search Criteria:<input type="text" name="search" />
			<input type="submit" class="button" name="submit" value="Search" />
			</fieldset>
		</div>
		<h4>Advanced</h4>
		<div class="Entry">
			<fieldset>
			Order by:<br />
			<input type="radio" name="order" value="title"/>Title<br />
			<input type="radio" name="order" value="date"/>Date<br />
			<input type="radio" name="order" value="photographer"/>Photographer<br /><br />
			Show only tag:<br />
			<select name="tag">
				<option value="null" selected></option>
				<?php if ($tags->num_rows() > 0) foreach($tags->result() as $tag):?>
				<option value="<?=$tag->tag_id?>"><?=$tag->tag_name?></option>
				<?php endforeach;?>
			</select><br /><br />
			Show only photographers:<br />
			<select name="photographer">
				<option value="null" selected></option>
				<?php if ($photographer->num_rows() > 0) foreach($photographer->result() as $person):?>
				<option value="<?=$person->user_id?>"><?=$person->user_firstname?> <?=$person->user_surname?></option>
				<?php endforeach;?>
			</select><br /><br />
			</fieldset>
		</div>
	</form>
</div>
<div class="blue_box">
	<?php
		// Load a subview.
		$content[0]->Load();

		echo $pageNumbers;
	?>
</div>
