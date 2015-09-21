<div class="FlexiBox Box23">
	<div id="DisplayBox">
		<div id="DisplayBoxBg"><?php echo(xml_escape($articles['uninews'][0]['heading'])); ?></div>
		<div id="DisplayBoxText"><a href="/news/<?php echo(xml_escape($articles['uninews'][0]['article_type'] . '/' . $articles['uninews'][0]['id'])); ?>"><?php echo(xml_escape($articles['uninews'][0]['heading'])); ?></a></div>
		<a href="/news/<?php echo(xml_escape($articles['uninews'][0]['article_type'] . '/' . $articles['uninews'][0]['id'])); ?>"><img src="/photos/home/<?php echo(xml_escape($articles['uninews'][0]['photo_id'])); ?>" alt="<?php echo(xml_escape($articles['uninews'][0]['photo_title'])); ?>" /></a>
	</div>
</div>

<table cellpadding="0" cellspacing="0">
	<thead>
	<th>
			<td>PRODUCT ID</td>
			<td>PRODUCT NAME</td>
			<td>CATEGORY</td>
			<td>PRICE</td>
	</th>
	</thead>

	<tbody>
		{row}
	</tbody>

</table>

<script type="text/javascript">
function changePreview (option, article_id, article_type, photo_id, photo_title) {
	document.getElementById('ArticleRolloverLink').href = '/news/' + article_type + '/' + article_id;
	document.getElementById('ArticleRolloverImg').src = '/photos/preview/' + photo_id;
	document.getElementById('ArticleRolloverImg').alt = photo_title;
	document.getElementById('ArticleRolloverImg').title = photo_title;
	var x = 1;
	var ele = document.getElementById('articleRollover_' + x);
	while ((ele != undefined) && (ele != null)) {
		if (x == option) {
			ele.className = 'selected';
		} else {
			ele.className = '';
		}
		x = x + 1;
		var ele = document.getElementById('articleRollover_' + x);
	}
}
</script>

<div class="ArticleRolloverBox FlexiBox Box13 FlexiBoxLast">
	<div class="ArticleListTitle">
		<a href="/news">latest news</a>
	</div>
	<div class="ArticleRolloverImage">
		<a id="ArticleRolloverLink" href="/news/uninews/234">
			<img id="ArticleRolloverImg" src="/photos/preview/<?php echo(xml_escape($articles['uninews'][1]['photo_id'])); ?>" alt="<?php echo(xml_escape($articles['uninews'][1]['photo_title'])); ?>" title="<?php echo(xml_escape($articles['uninews'][1]['photo_title'])); ?>" />
		</a>
	</div>
	<div class="ArticleRolloverList">
		<?php for ($x = 1; $x < count($articles['uninews']); $x++) { ?>
			<div id="articleRollover_<?php echo($x); ?>" <?php if ($x == 1) echo('class="selected" '); ?>onmouseover="changePreview(<?php echo($x); ?>, <?php echo(xml_escape($articles['uninews'][$x]['id'])); ?>, 'uninews', <?php echo(xml_escape($articles['uninews'][$x]['photo_id'])); ?>, <?php echo(xml_escape(js_literalise($articles['uninews'][$x]['photo_title']))); ?>);">
				<a href="/news/<?php echo(xml_escape($articles['uninews'][$x]['article_type'])); ?>/<?php echo(xml_escape($articles['uninews'][$x]['id'])); ?>">
					<?php echo(xml_escape($articles['uninews'][$x]['heading'])); ?>
				</a>
			</div>
		<?php } ?>
	</div>
</div>
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
	<!--
	<a href="http://www.facebook.com/event.php?eid=111317892288">
		<img src="/images/adverts/BrownsAdSm.jpg" alt="Vote Browns for British Sandwich Week" />
	</a>
	-->
</div>
<div class="clear"></div>

<?php function ArticleList ($section, $articles, $last = false) { ?>
	<div class="ArticleListBox FlexiBox Box13<?php if ($last) echo(' FlexiBoxLast'); ?>">
		<div class="ArticleListTitle">
			<a href="/<?php echo($section); ?>">latest <?php echo($section); ?></a>
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

<?php ArticleList('sport', $articles['sport']); ?>
<?php ArticleList('arts', $articles['arts']); ?>
<?php ArticleList('comment', $articles['comment']); ?>
<?php ArticleList('lifestyle', $articles['lifestyle'], true); ?>
<div class="clear"></div>

<?php $latest_comments->Load(); ?>


<div class="FlexiBox FlexiBoxLast" style="background-color:#f2f2f2;text-align:center;border:none;">
<?php foreach ($photos as $photo) { ?>
	<a href="http://www.flickr.com/photos/<?php echo(xml_escape($photo['owner_id'])); ?>/<?php echo($photo['id']); ?>">
		<img src="http://farm<?php echo($photo['farm']); ?>.static.flickr.com/<?php echo($photo['server']); ?>/<?php echo($photo['id']); ?>_<?php echo($photo['secret']); ?>_t.jpg" alt="<?php echo(xml_escape($photo['title'])); ?>" title="<?php echo(xml_escape($photo['title'])); ?>" />
	</a>
<?php } ?>
</div>
<div class="clear"></div>

<div class="FlexiBox Box12" style="background-color:#f2f2f2;text-align:center;">
	<script type="text/javascript">
	<!--
	google_ad_client = "pub-8676956632365960";
	/* 468x60, created 07/05/09 */
	google_ad_slot = "5537704417";
	google_ad_width = 468;
	google_ad_height = 60;
	//-->
	</script>
	<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</div>



<div class="ArticleListBox FlexiBox Box12">
	<div class="ArticleListTitle">
		<a href="/crosswords">
			latest crosswords
		</a>
	</div>
	<?php $crosswords->Load(); ?>
</div>
<div class="FlexiBox Box12">
	<div class="ArticleListTitle">upcoming events</div>
	<?php $events->Load(); ?>
</div>
