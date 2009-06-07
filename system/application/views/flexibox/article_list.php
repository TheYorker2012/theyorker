<?php
switch ($size) {
	case '2/3':
		$box_size = 'Box23';
		break;
	default:
		$box_size = 'Box13';
}
?>

<div class="ArticleListBox FlexiBox <?php echo($box_size); if (!empty($last)) echo(' FlexiBoxLast'); ?>">
	<div class="ArticleListTitle">
		<a href="<?php echo($title_link); ?>"><?php echo($title); ?></a>
	</div>
	<?php foreach ($articles as $article) { ?>
	<div>
		<a href="/news/<?php echo(xml_escape($article['id'])); ?>">
			<img src="/photos/small/<?php echo(xml_escape($article['photo_id'])); ?>" alt="<?php echo(xml_escape($article['photo_title'])); ?>" title="<?php echo(xml_escape($article['photo_title'])); ?>" />
			<?php echo(xml_escape($article['headline'])); ?>
		</a>
		<div class="Date"><?php echo(xml_escape(date('l, jS F Y', $article['date']))); ?></div>
		<div class="clear"></div>
	</div>
	<?php } ?>
</div>