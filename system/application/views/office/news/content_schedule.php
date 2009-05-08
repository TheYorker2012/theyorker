<div class='BlueBox'>
	<h2>content schedule</h2>

	<p><a href="/office/article/create">Create New Article</a></p>
	<p><a href="/office/articles/live">Scheduled and Live Articles</a>

	<table width="98%">
	<tr>
	<thead>
	<th>Deadline</th>
	<th>Box</th>
	<th>Story</th>
	<th>Editor</th>
	<th>Writer(s)</th>
	<th>Status</th>
	</thead>
	</tr>

	<?php $publish_date = null; ?>
	<?php foreach($articlelist as $article) {
		$new_publish_date = date('D jS M',strtotime($article['publish_date']));
		if ($publish_date != null && $publish_date != $new_publish_date) {
			echo('<tr><td colspan="6"><hr /></td></tr>');
		}
		$publish_date = $new_publish_date;
	?>
	<tr>
	<td style="<?php if($article['overdue']) echo('color: red;');?>"><?php echo($publish_date); ?></td>
	<td><?php echo(xml_escape($article['content_type_name'])); ?></td>
	<td><a href="/office/article/<?php echo($article['article_id']); ?>/" title="Edit this article"><?php echo((strlen($article['headline']) == 0 ? 'No title' : xml_escape($article['headline'] ))); ?></a></td>
	<td><?php echo(xml_escape($article['editor'])); ?></td>
	<td><?php echo(nl2br(xml_escape($article['authors']))); ?></td>
	<td><?php echo(($article['is_accepted'] ? '<img src="/images/prototype/news/accepted.gif" title="Accepted" alt="Accepted" />' : ($article['is_requested'] ? '<img src="/images/prototype/news/requested.gif" title="Requested" alt="Requested" />' : '<img src="/images/prototype/news/unassigned.png" title="Unassigned" alt="Unassigned" />') )); ?></td>
	</tr>

	<?php } ?>
	</table>

</div>
