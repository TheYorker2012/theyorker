<?php
switch ($size) {
	case '1/3':
		$box_size = 'Box13';
		break;
	case '2/3':
		$box_size = 'Box23';
		break;
	default:
		$box_size = '';
}
?>

<div class="ClearFlexiBox<?php if (!empty($box_size)) { echo(' ' . $box_size); } if (!empty($last)) { echo(' FlexiBoxLast'); } ?>">
<?php foreach ($photos as $photo) { ?>
	<a href="http://www.flickr.com/photos/<?php echo(xml_escape($photo['owner_id'])); ?>/<?php echo($photo['id']); ?>">
		<img src="http://farm<?php echo($photo['farm']); ?>.static.flickr.com/<?php echo($photo['server']); ?>/<?php echo($photo['id']); ?>_<?php echo($photo['secret']); ?>_t.jpg" alt="<?php echo(xml_escape($photo['title'])); ?>" title="<?php echo(xml_escape($photo['title'])); ?>" />
	</a>
<?php } ?>
</div>