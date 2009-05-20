<?php if (!empty($spotlight)) { ?>
<div class="FlexiBox Box23">
	<div id="DisplayBox">
		<div id="DisplayBoxBg"><?php echo(xml_escape($spotlight[0]['headline'])); ?></div>
		<div id="DisplayBoxText"><a href="/news/<?php echo(xml_escape($spotlight[0]['id'])); ?>"><?php echo(xml_escape($spotlight[0]['headline'])); ?></a></div>
		<a href="/news/<?php echo(xml_escape($spotlight[0]['id'])); ?>"><img src="/photos/home/<?php echo(xml_escape($spotlight[0]['photo_id'])); ?>" alt="" /></a>
	</div>
</div>
<?php } ?>

<?php if (!empty($features['articles'])) { ?>
	<div class="ArticleListBox FlexiBox Box13 FlexiBoxLast" style="float:right">
		<div class="ArticleListTitle">
			<a href="/news/<?php echo(xml_escape($features['articles'][0]['id'])); ?>">
				<?php echo(xml_escape($features['title'])); ?>
			</a>
		</div>
		<?php foreach ($features['articles'] as $article) { ?>
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
<?php } ?>

<?php function ArticleList ($section, $articles, $last = false) {
	if (count($articles) == 0) return; ?>
	<div class="ArticleListBox FlexiBox Box13<?php if ($last) echo(' FlexiBoxLast'); ?>">
		<div class="ArticleListTitle">
			<a href="/news/<?php echo($articles[0]['id']); ?>"><?php echo($section); ?></a>
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
	<?php if ($last) { ?><div class="clear"></div><?php } ?>
<?php } ?>

<?php
$column = 0;
foreach ($sections as $section) {
	$column++;
	ArticleList($section['title'], $section['articles'], ($column % 3) == 0 ? true : false);
}
?>
<div class="clear"></div>
