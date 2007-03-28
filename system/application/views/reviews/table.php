<div class="BlueBox">
	<h2><?php echo($item_type); ?> reviews ordered by <?php echo($sorted_by); ?></h2>
	<table id="ReviewSearchResults">
		<tr>
			<th><a href="/reviews/table/<?php echo $item_type; ?>/name<?php if ($item_filter_by!='') echo '/'.$item_filter_by.'/'.$where_equal_to; ?>" name="tabletop">Name</a></th>
			<th><a href="/reviews/table/<?php echo $item_type; ?>/star<?php if ($item_filter_by!='') echo '/'.$item_filter_by.'/'.$where_equal_to; ?>"><span class="sorted_by"><img src="/images/prototype/reviews/sortarrow.gif" alt="v" /> Star Rating</span></a></th>
			<th><a href="/reviews/table/<?php echo $item_type; ?>/user<?php if ($item_filter_by!='') echo '/'.$item_filter_by.'/'.$where_equal_to; ?>">User Rating</a></th>
<?php
if (isset($review_tags)) {
	foreach ($review_tags as $tag) {
		echo('			');
		echo('<th><a href="/reviews/table/'.rawurlencode($item_type).'/'.rawurlencode($tag));
		if ($item_filter_by != '')
			echo('/'.rawurlencode($item_filter_by).'/'.rawurlencode($where_equal_to));
		echo('">'.$tag.'</a></th>'."\n");
	}
}
?>
		</tr>
<?php
foreach($entries as $entry) {
	echo('		<tr>'."\n");
	echo('			<td>'."\n");
	echo('				');
	echo('<h3><a href="'.$entry['review_table_link'].'">'.$entry['review_title'].'</a></h3>'."\n");
	echo('				');
	echo('<a href="'.$entry['review_website'].'">'.$entry['review_website'].'</a><br />'."\n");
	echo('				');
	echo('<a href="#">&gt;Food</a>&nbsp;<a href="#">&gt;Drink</a><br />'."\n");
	echo('			</td>'."\n");
	echo('			<td>'.$entry['review_rating'].' Stars</td>'."\n");
	echo('			<td>'.$entry['review_user_rating'].'/10</td>'."\n");
	foreach ($entry['tagbox'] as $taglist) {
		echo('			');
		echo('<td>');
		foreach ($taglist as $tag) {
			echo($tag.'<br />');
		}
		echo('</td>'."\n");
	}
	echo('		</tr>'."\n");
}
?>
	</table>

	<p>
		<a href="#ReviewSearchResults">&gt;Go back to top</a>&nbsp;&nbsp;
		<a href="/reviews/<?php echo($item_type); ?>">&gt;Go back to <?php echo($item_type); ?></a>
	</p>
</div>
