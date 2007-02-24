	<span class="grey">Showing</span> <?php echo count($entries); ?>
	<span class="grey">entries from</span> <?php echo $item_type; ?>
	<span class="grey">ordered by</span> <?php echo $sorted_by; ?>
	<br /><br />
	<table class="ReviewList">
		<tr class="ReviewListTop">
			<td><a href="/reviews/table/<?php echo $item_type; ?>/name<?php if ($item_filter_by!='') echo '/'.$item_filter_by.'/'.$where_equal_to; ?>" name="tabletop">Name</a></td>
			<td><a href="/reviews/table/<?php echo $item_type; ?>/star<?php if ($item_filter_by!='') echo '/'.$item_filter_by.'/'.$where_equal_to; ?>"><span class="sorted_by"><img style="display: inline;" src="/images/prototype/reviews/sortarrow.gif" alt="v" /> Star Rating</span></a></td>
			<td><a href="/reviews/table/<?php echo $item_type; ?>/user<?php if ($item_filter_by!='') echo '/'.$item_filter_by.'/'.$where_equal_to; ?>">User Rating</a></td>

<?php
//Tag names at top of table
if (isset($review_tags))
{
	foreach ($review_tags as &$tag)
		{
			echo '<td><a href="/reviews/table/'.$item_type.'/'.$tag;
			if ($item_filter_by != '') echo '/'.$item_filter_by.'/'.$where_equal_to;
			echo '">'.$tag.'</a></td>';
		}
}
?>
		</tr>

<?php

	//For each row in the table
	foreach ($entries as &$entry)
	{
		echo '<tr class="ReviewElement">
				<td>
				<h3><a href="'.$entry['review_table_link'].'">'.$entry['review_title'].'</a></h3><br />
				<a href="'.$entry['review_website'].'">'.$entry['review_website'].'</a><br />
				<a href="#">&gt;Food</a>&nbsp;&nbsp;<a href="#">&gt;Drink</a><br />
			    </td>
			    <td>'.$entry['review_rating'].' Stars</td>
			    <td>'.$entry['review_user_rating'].'/10</td>';
	//Tag handing
	foreach ($entry['tagbox'] as &$taglist)
	{
		echo '<td>';
		foreach ($taglist as &$tag)
			{
				echo $tag.'<br />';
			}
		echo '</td>';
	}

		echo '</tr>'; //End of table
	}

echo	'<tr class="ReviewElementEnd">
			<td colspan="0">
				<a href="#tabletop">&gt;Go back to top</a>&nbsp;&nbsp;<a href="/reviews/'.$item_type.'">&gt;Go back to '.$item_type.' </a>
			</td>
		</tr>
	</table>';
?>
