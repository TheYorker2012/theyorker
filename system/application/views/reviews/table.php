<span class="SmallText">
	<span class="FaintText">Showing</span> <?php echo count($entries); ?>
	<span class="FaintText">entries from</span> <?php echo $this->uri->segment(3); ?>
	<span class="FaintText">ordered by</span> <?php echo $this->uri->segment(4); ?>
	<br />
	<br />
	<table class="ReviewList">
		<tr class="ReviewListTop">
			<td><a href="/reviews/table/food/name" name="top">Name</a></td>
			<td><a href="/reviews/table/food/star"><span class="sorted_by"><img style="display: inline;" src="/images/prototype/reviews/sortarrow.gif" alt="Ascending Order" /> Star Rating</span></a></td>
			<td><a href="/reviews/table/food/user">User Rating</a></td>

<?php
//Tag names at top of table
	foreach ($review_tags as &$tag)
		{
			echo '<td><a href="/reviews/table/food/any">'.$tag.'</a></td>';
		}
?>

		</tr>

<?php

	//For each row in the table
	foreach ($entries as &$entry)
	{
		echo '<tr class="ReviewElement1">
				<td>
				<a href="' . $entry['review_table_link'] . '"><img src="' . $entry['review_image'] . '" alt="#" /></a>
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
?>

		<tr class="ReviewElementEnd">
			<td colspan="6">
				<a href="#top">&gt;Go back to top</a>&nbsp;&nbsp;<a href="food">&gt;Go back to food</a>
			</td>
		</tr>
	</table>
