<?php if (!empty($spotlight)) { ?>
<div class="FlexiBox Box23">
	<div id="DisplayBox">
		<div id="DisplayBoxBg"><?php echo(xml_escape($spotlight[0]['headline'])); ?></div>
		<div id="DisplayBoxText"><a href="/news/<?php echo(xml_escape($spotlight[0]['id'])); ?>"><?php echo(xml_escape($spotlight[0]['headline'])); ?></a></div>
		<a href="/news/<?php echo(xml_escape($spotlight[0]['id'])); ?>"><img src="/photos/home/<?php echo(xml_escape($spotlight[0]['photo_id'])); ?>" alt="" /></a>
	</div>
</div>
<?php } ?>

<?php function SpecialList ($section, $articles) {
	if (empty($articles)) return; ?>
	<div class="ArticleListBox FlexiBox Box13 FlexiBoxLast" style="float:right;clear:right;">
		<div class="ArticleListTitle">
			<a href="/news/<?php echo($articles[0]['id']); ?>">
				<?php echo(xml_escape($section)); ?>
			</a>
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
<?php } ?>

<?php
SpecialList($features['title'], $features['articles']);
?>

<?php function ArticleList ($section, $articles, $last = false) {
	if (empty($articles)) return; ?>
	<div class="ArticleListBox FlexiBox Box23<?php if ($last) echo(' FlexiBoxLast'); ?>">
		<div class="ArticleListTitle">
			<a href="/news/<?php echo($articles[0]['id']); ?>"><?php echo($section); ?></a>
		</div>
		<?php foreach ($articles as $article) { ?>
		<div style="width:50%;float:left;">
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

<div class="FlexiBox Box13 FlexiBoxLast" style="background-color:#f2f2f2;text-align:center;">
	<script type="text/javascript"><!--
	google_ad_client = "pub-8676956632365960";
	/* 234x60, created 02/06/09 */
	google_ad_slot = "4255960768";
	google_ad_width = 234;
	google_ad_height = 60;
	//-->
	</script>
	<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</div>

<?php
SpecialList($blogs['title'], $blogs['articles']);
?>

<?php
/*
<!--
GO AWAY IN MY VIEW!
<div class="ArticleListBox FlexiBox Box23">
	<div class="ArticleListTitleImg">
		<a href="/news/<?php echo(xml_escape($inmyview[0]['id'])); ?>">
			<img src="/images/version2/banners/in_my_view2.png" alt="In My View" title="In My View" />
		</a>
	</div>
	<?php foreach ($inmyview as $article) { ?>
	<div style="width:50%;float:left;">
		<a href="/news/<?php echo(xml_escape($article['id'])); ?>">
			<img src="/photos/small/<?php echo(xml_escape($article['photo_id'])); ?>" alt="<?php echo(xml_escape($article['photo_title'])); ?>" title="<?php echo(xml_escape($article['photo_title'])); ?>" />
			<?php echo(xml_escape($article['headline'])); ?>
		</a>
		<div class="Date"><?php echo(xml_escape(date('l, jS F Y', $article['date']))); ?></div>
		<div class="clear"></div>
	</div>
	<?php } ?>
</div>
-->
*/
?> 


<div class="clear"></div>
