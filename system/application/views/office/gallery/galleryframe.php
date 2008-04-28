<div id="RightColumn">
	<h2 class="first">Actions</h2>
	<form id="clearform" class="form" method="post" action="<?php echo(site_url('office/gallery')); ?>">
		<div class="Entry">
			<ul>
			<input type="hidden" name="clear" value="clear" />
			<li><a href="<?php echo(site_url('office/gallery/upload')); ?>">Upload New Photos</a></li>
			<li><a href="<?php echo(site_url('office')); ?>">Return to the Office</a></li>
			<?php if($this->uri->segment(4)) {
				echo('<li><a href="'.site_url('office/gallery').'">Return to the Gallery</a></li>');
				echo('<li><a href="'.site_url('office/gallery/return').'">Select this Photo</a></li>');
			}?>
			<li><a href="javascript:document.getElementById('clearform').submit()">Start a new Search</a></li>
		</div>
	</form>
	<h2>Search by...</h2>
	<form class="form" method="post" action="<?php echo(site_url('office/gallery')); ?>">
		<div class="Entry">
			<fieldset>
			<p>
				<input type="radio" style="width: auto;" name="searchcriteria" value="title" checked />Title<br />
				<input type="radio" style="width: auto;" name="searchcriteria" value="date" />Date<br />
				<input type="radio" style="width: auto;" name="searchcriteria" value="photographer" />Photographer
			</p>
			Search Criteria:<input type="text" name="search" />
			<input type="submit" class="button" name="submit" value="Search" />
			</fieldset>
		</div>
		<h2>Advanced</h2>
		<div class="Entry">
			<fieldset>
			<p>
				Order by:<br />
				<input style="width: auto;" type="radio" name="order" value="title"/>Title<br />
				<input style="width: auto;" type="radio" name="order" value="date"/>Date<br />
				<input style="width: auto;" type="radio" name="order" value="photographer"/>Photographer
			</p>
			Show only tag:<br />
			<select name="tag">
				<option value="null" selected></option>
				<?php if ($tags->num_rows() > 0) foreach($tags->result() as $tag):?>
				<option value="<?php echo $tag->tag_id; ?>"><?php echo(xml_escape($tag->tag_name)); ?></option>
				<?php endforeach;?>
			</select><br /><br />
			Show only photographers:<br />
			<select name="photographer">
				<option value="null" selected></option>
				<?php if ($photographer->num_rows() > 0) foreach($photographer->result() as $person) { ?>
				<option value="<?php echo($person->user_entity_id); ?>"><?php echo(xml_escape($person->user_firstname.' '.$person->user_surname)); ?></option>
				<?php }?>
			</select><br /><br />
			</fieldset>
		</div>
	</form>
</div>
<div id="MainColumn">
	<?php
		// Load a subview.
		$content[0]->Load();

		echo($pageNumbers);
	?>
</div>
