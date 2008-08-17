<style type="text/css">
div.tag_photos img {
	padding: 5px;
	border: 1px #999 dotted;
}

div.tag_photos img:hover, div.tag_photos img.selected {
	border: 1px #999 solid;
}
</style>

<div class="BlueBox">
	<h2><?php echo(xml_escape($photo_title)); ?></h2>
	<?php echo($photo_xhtml); ?>

	<?php foreach ($tags as $tag) { ?>
		<h2>'<?php echo($tag['tag_name']); ?>' tagged photos...</h2>
		<div class="tag_photos">
			<?php foreach ($tags_photos[$tag['tag_id']] as $photo) { ?>
				<a href="/gallery/<?php echo($photo['id']); ?>"><?php echo($photo['xhtml']); ?></a>
			<?php } ?>
		</div>
	<?php } ?>
</div>