<div class="BlueBox">
	<h2>search results</h2>
	<?php foreach ($photos as $thumbnail) { ?>
		<div class="thumbnail_small">
			<a href="/office/gallery/show/<?php echo($thumbnail->photo_id); ?>"><?php echo($this->image->getThumb($thumbnail->photo_id, 'small')); ?></a>
			<div>
				<span class="orange">Image Title: </span><?php echo(xml_escape($thumbnail->photo_title)); ?><br />
				<span class="orange">Date: </span><?php echo($thumbnail->photo_timestamp); ?><br />
				<span class="orange">Photographer: </span><?php echo(xml_escape(fullname($thumbnail->photo_author_user_entity_id))); ?><br />
			</div>
		</div>
	<?php } ?>
</div>