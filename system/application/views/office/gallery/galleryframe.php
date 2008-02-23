<div class="RightToolbar">
	<h4>Actions</h4>
	<form class="form" method="post" action="<?=site_url('office/gallery')?>">
		<div class="Entry">
			<ul>
			<input type="hidden" name="clear" value="clear" />
			<li><a href="<?=site_url('office/gallery/upload')?>">Upload New Photos</a></li>
			<li><a href="<?=site_url('office')?>">Return to the Office</a></li>
			<?php if($this->uri->segment(4)) {
				echo('<li><a href="'.site_url('office/gallery').'">Return to the Gallery</a></li>');
				echo('<li><a href="'.site_url('office/gallery/return').'">Select this Photo</a></li>');
			}?>
			<li><a href="javascript:document.clearform.submit()">Start a new Search</a></li>
		</div>
	</form>
	<h4>Search by...</h4>
	<form class="form" method="post" action="<?=site_url('office/gallery')?>">
		<div class="Entry">
			<fieldset>
			<input type="radio" name="searchcriteria" value="title" checked />Title<br />
			<input type="radio" name="searchcriteria" value="date" />Date<br />
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
	<?php
		// Load a subview.
		$content[0]->Load();

		echo($pageNumbers);
	?>
</div>
