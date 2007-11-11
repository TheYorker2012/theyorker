<div class='blue_box' style="width:auto">
	<h2>scheduled and live</h2>
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
	<td><?php echo $article['publish_date']; ?></td>
	<td><?php echo ($article['is_live'] ? 'Live' : 'Waiting'); ?></td>
	<td><a href="/office/news/<?php echo $article['article_id']; ?>/" title="Edit this article"><?php echo $article['headline']; ?></a></td>
	<td><?php echo $article['authors']; ?></td>
	<td><?php echo $article['content_type_name']; ?></td>
	<td><?php echo $article['updated_date']; ?></td>
	</tr>

	<? } ?>
	</table>

</div>