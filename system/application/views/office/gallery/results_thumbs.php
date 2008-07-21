<?php foreach ($photos as $thumbnail) { ?>
	<div class="gallery_thumb_view">
		<a href="/office/gallery/show/<?php echo($thumbnail->photo_id); ?>">
			<img src="/photos/medium/<?php echo($thumbnail->photo_id); ?>" alt="<?php echo(xml_escape($thumbnail->photo_title)); ?>" title="<?php echo(xml_escape($thumbnail->photo_title)); ?>" />
		</a>
	</div>
<?php } ?>