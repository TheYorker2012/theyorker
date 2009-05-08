<div class='BlueBox'>
	<h2>scheduled and live</h2>

	<p><a href="/office/article/create">Create New Article</a></p>
	<p><a href="/office/articles">Articles with deadlines</a></p>

	<table width="100%">
	<tr>
	<thead>
	<th>Publish Date</th>
	<th>Live</th>
	<th>Headline</th>
	<th>Authors</th>
	<th>Box</th>
	<th>Updated Date</th>
	</thead>
	</tr>

	<?php foreach($articlelist as $article) { ?>

	<tr>
	<td><?php echo($article['publish_date']); ?></td>
	<td><?php echo(($article['is_live'] ? 'Live' : 'Waiting')); ?></td>
	<td><a href="/office/article/<?php echo($article['article_id']); ?>/" title="Edit this article"><?php echo(xml_escape($article['headline'])); ?></a></td>
	<td><?php echo(xml_escape($article['authors'])); ?></td>
	<td><?php echo(xml_escape($article['content_type_name'])); ?></td>
	<td><?php echo($article['updated_date']); ?></td>
	</tr>

	<?php } ?>
	</table>

</div>
