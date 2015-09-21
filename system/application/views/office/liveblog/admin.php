<script type="text/javascript">
function update_count(control) {
	var display = document.getElementById('character_count');
	display.innerHTML = 140 - control.value.length;
}
</script>

<div class="BlueBox">
	<h2>LIVE BLOGGING ADMIN -
	<?php
	if ($article_id == 4663) echo('ROSES-LIVEBLOG');
	elseif ($article_id == 4640) echo('FRIDAY');
	elseif ($article_id == 4661) echo('SATURDAY');
	elseif ($article_id == 4662) echo('SUNDAY');
	elseif ($article_id == 2858) echo('Elections 2009');
	else echo('UNKNOWN');
	?>
	</h2>
	<p>
		<a href="/office/liveblog/admin/4663">Roses Liveblog</a> |
		<a href="/office/liveblog/admin/4640">FRIDAY</a> |
		<a href="/office/liveblog/admin/4661">SATURDAY</a> |
		<a href="/office/liveblog/admin/4662">SUNDAY</a>
		<!--
		<a href="/office/liveblog/admin/2858">Elections 2009</a>
		-->
	</p>
	<form action="/office/liveblog/admin/<?php echo($article_id); ?>" method="post">
		<div style="float:right;clear:both;">
			<input type="submit" name="postnew" value="Post" style="float:none;margin:0;" /><br />
			<input type="checkbox" name="posttwitter" value="twitter" /><br />
			<div id="character_count">140</div>
		</div>
		<div><textarea cols="40" rows="3" name="postcontent" id="postcontent" style="width:700px;" onclick="update_count(this);" onfocus="update_count(this);" onblur="update_count(this);" onkeyup="update_count(this);" onkeydown="update_count(this);"></textarea></div>
	</form>
</div>

<div class="BlueBox">
	<div style="float:right">
		<a href="/news/<?php echo($article_id); ?>" target="_blank">VIEW ARTICLE</a>
	</div>
	<h2>Current Entries</h2>
	<?php foreach ($content['rows'] as $entry) { ?>
		<form action="/office/liveblog/admin/<?php echo($article_id); ?>/<?php echo($entry->article_liveblog_id); ?>" method="post">
			<div style="clear:both"><i><?php echo(date('r', $entry->article_liveblog_posted_time)); ?></i> by <b><?php echo($entry->user_firstname . ' ' . $entry->user_surname); ?></b></div>
			<div style="float:right;clear:both;">
				<input type="submit" name="edit<?php echo($entry->article_liveblog_id); ?>" value="Edit" style="float:none;margin:0;" /><br />
				<input type="submit" name="delete<?php echo($entry->article_liveblog_id); ?>" value="Delete" style="float:none;margin:0;" onclick="return confirm('Are you sure you want to delete this entry?');" />
			</div>
			<div><textarea cols="40" rows="3" name="entry<?php echo($entry->article_liveblog_id); ?>" id="entry<?php echo($entry->article_liveblog_id); ?>" style="width:700px;"><?php echo($entry->article_liveblog_wikitext); ?></textarea></div>
		</form>
	<?php } ?>
</div>
