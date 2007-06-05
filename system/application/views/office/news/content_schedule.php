<div class='blue_box' style="width:auto">
	<h2>content schedule</h2>

	<a href="/office/news/request">Make request</a> (this will add an entry here).

	<table width="100%">
	<tr>
	<thead>
	<th>Deadline</th>
	<th>Box</th>
	<th>Editor</th>
	<th>Status</th>
	<th>Request Title</th>
	<th>Authors</th>
	</thead>
	</tr>

	<?php foreach($articlelist as $article) { ?>

	<tr>
	<td style="<?php if($article['overdue']) echo 'color: red;';?>"><?php echo date('D jS M',strtotime($article['publish_date'])); ?></td>
	<td><?php echo $article['content_type_name']; ?></td>
	<td><?php echo $article['editor']; ?></td>
	<td><?php echo ($article['is_accepted'] ? '<img src="/images/prototype/news/accepted.gif" title="Accepted" alt="Accepted" />' : ($article['is_requested'] ? '<img src="/images/prototype/news/requested.gif" title="Requested" alt="Requested" />' : '<img src="/images/prototype/news/unassigned.png" title="Unassigned" alt="Unassigned" />') ); ?></td>
	<td><a href="/office/news/<?php echo $article['article_id']; ?>/" title="Edit this article"><?php echo (strlen($article['headline']) == 0 ? 'No title' : $article['headline'] ); ?></a></td>
	<td><?php echo $article['authors']; ?></td>
	</tr>

	<? } ?>
	</table>

</div>