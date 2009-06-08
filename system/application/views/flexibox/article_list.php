<?php
switch ($size) {
	case '1/2':
		$box_size = 'Box12';
		$box_width = '50%';
		break;
	case '1/3':
		$box_size = 'Box13';
		$box_width = '';
		break;
	case '2/3':
		$box_size = 'Box23';
		$box_width = '50%';
		break;
	default:
		$box_size = '';
		$box_width = '';
}
?>

<div class="ArticleListBox FlexiBox<?php if (!empty($box_size)) { echo(' ' . $box_size); } if (!empty($last)) { echo(' FlexiBoxLast'); } ?>"<?php if (!empty($position)) { echo(' style="float:' . $position . ';clear:' . $position . ';"'); } ?>>
	<div class="<?php echo(empty($title_image) ? 'ArticleListTitle' : 'ArticleListTitleImg'); ?>">
<?php if (!empty($title_link)) { ?>
		<a href="<?php echo($title_link); ?>">
<?php } ?>
<?php if (!empty($title_image)) { ?>
			<img src="<?php echo($title_image); ?>" alt="<?php echo($title); ?>" title="<?php echo($title); ?>" />
<?php } else { ?>
			<?php echo($title); ?>
<?php } ?>
<?php if (!empty($title_link)) { ?>
		</a>
<?php } ?>
	</div>
	<?php foreach ($articles as $article) { ?>
	<div<?php if (!empty($box_width)) echo(' style="float:left;width:' . $box_width . ';"'); ?>>
		<a href="/news/<?php echo(xml_escape($article['id'])); ?>">
			<img src="/photos/small/<?php echo(xml_escape($article['photo_id'])); ?>" alt="<?php echo(xml_escape($article['photo_title'])); ?>" title="<?php echo(xml_escape($article['photo_title'])); ?>" />
			<?php echo(xml_escape($article['headline'])); ?>
		</a>
		<div class="Date"><?php echo(xml_escape(date('l, jS F Y', $article['date']))); ?></div>
		<div class="clear"></div>
	</div>
	<?php } ?>
</div>