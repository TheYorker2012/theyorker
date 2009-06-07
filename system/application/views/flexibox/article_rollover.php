<script type="text/javascript">
function changePreview (option, article_id, photo_id, photo_title) {
	document.getElementById('ArticleRolloverLink').href = '/news/' + article_id;
	document.getElementById('ArticleRolloverImg').src = '/photos/preview/' + photo_id;
	document.getElementById('ArticleRolloverImg').alt = photo_title;
	document.getElementById('ArticleRolloverImg').title = photo_title;
	var x = 0;
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
		<a href="<?php echo($title_link); ?>">
			<?php echo($title); ?>
		</a>
	</div>
	<div class="ArticleRolloverImage">
		<a id="ArticleRolloverLink" href="/news/<?php echo(xml_escape($articles[0]['id'])); ?>">
			<img id="ArticleRolloverImg" src="/photos/preview/<?php echo(xml_escape($articles[0]['photo_id'])); ?>" alt="<?php echo(xml_escape($articles[0]['photo_title'])); ?>" title="<?php echo(xml_escape($articles[0]['photo_title'])); ?>" />
		</a>
	</div>
	<div class="ArticleRolloverList">
		<?php for ($x = 0; $x < count($articles); $x++) { ?>
			<div id="articleRollover_<?php echo($x); ?>" <?php if ($x == 0) echo('class="selected" '); ?>onmouseover="changePreview(<?php echo($x); ?>, <?php echo(xml_escape($articles[$x]['id'])); ?>, <?php echo(xml_escape($articles[$x]['photo_id'])); ?>, <?php echo(xml_escape(js_literalise($articles[$x]['photo_title']))); ?>);">
				<a href="/news/<?php echo(xml_escape($articles[$x]['id'])); ?>">
					<?php echo(xml_escape($articles[$x]['headline'])); ?>
				</a>
			</div>
		<?php } ?>
	</div>
</div>