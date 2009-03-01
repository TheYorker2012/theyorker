<div class="FlexiBox Box23">
	<div id="DisplayBox">
		<div id="DisplayBoxBg"><?php echo(xml_escape($main_articles[0]['heading'])); ?></div>
		<div id="DisplayBoxText"><a href="/news/<?php echo(xml_escape($main_articles[0]['article_type'] . '/' . $main_articles[0]['id'])); ?>"><?php echo(xml_escape($main_articles[0]['heading'])); ?></a></div>
		<a href="/news/<?php echo(xml_escape($main_articles[0]['article_type'] . '/' . $main_articles[0]['id'])); ?>"><img src="/photos/home/<?php echo(xml_escape($main_articles[0]['photo_id'])); ?>" alt="" /></a>
	</div>
</div>

<?php function ArticleList ($section, $articles, $last = false) {
	if (count($articles) == 0) return; ?>
	<div class="ArticleListBox FlexiBox Box13<?php if ($last) echo(' FlexiBoxLast'); ?>">
		<div class="ArticleListTitle">
			<a href="/news/<?php echo($articles[0]['article_type'] . '/' . $articles[0]['id']); ?>">latest <?php echo($section); ?></a>
		</div>
		<?php foreach ($articles as $article) { ?>
		<div>
			<a href="/news/<?php echo(xml_escape($article['article_type'] . '/' . $article['id'])); ?>">
				<img src="/photos/small/<?php echo(xml_escape($article['photo_id'])); ?>" alt="<?php echo(xml_escape($article['photo_title'])); ?>" title="<?php echo(xml_escape($article['photo_title'])); ?>" />
				<?php echo(xml_escape($article['heading'])); ?>
			</a>
			<div class="Date"><?php echo(xml_escape($article['date'])); ?></div>
			<div class="clear"></div>
		</div>
		<?php } ?>
	</div>
<?php } ?>

<?php
$column = 2;
foreach ($section_articles as $section => $articles) {
	$column++;
	ArticleList($section, $articles, ($column % 3) == 0 ? true : false);
}
?>
<div class="clear"></div>