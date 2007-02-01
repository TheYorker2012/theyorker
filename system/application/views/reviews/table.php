<span class="SmallText">
	<span class="FaintText">Showing</span> 0
	<span class="FaintText">entries from</span> Italian Food 
	<span class="FaintText">ordered by</span> Star Rating
	<br />
	<br />
	<table class="ReviewList">
		<tr class="ReviewListTop">
			<td><a href="/reviews/table/food/name" name="top">Name</a></td>
			<td><a href="/reviews/table/food/star"><span class="SortedBy"><img style="display: inline;" src="/images/prototype/reviews/sortarrow.gif" alt="Ascending Order" /> Star Rating</span></a></td>
			<td><a href="/reviews/table/food/price">Price Rating</a></td>
			<td><a href="/reviews/table/food/rating">User Rating</a></td>
<!--
			<td><a href="/reviews/table/food/rating">Atmosphere</a></td>
			<td><a href="/reviews/table/food/rating">Cuisine</a></td>
-->
		</tr>

<?php
	$flip = 1;
	foreach ($entries as &$entry)
	{
		echo '<tr class="ReviewElement' . $flip . '">
				<td>
				<a href="' . $entry['review_table_link'] . '"><img src="' . $entry['review_image'] . '" alt="#" /></a>
				<h3><a href="'.$entry['review_table_link'].'">'.$entry['review_title'].'</a></h3><br />
				<a href="'.$entry['review_website'].'">'.$entry['review_website'].'</a><br />
				<a href="#">&gt;Food</a>&nbsp;&nbsp;<a href="#">&gt;Drink</a><br />
			</td>
			<td>'.$entry['review_rating'].' Stars</td>
			<td>'.$entry['review_cost_type'].'</td>
			<td>'.$entry['review_user_rating'].'/10</td>
<!--
			<td>'.$entry['review_tags']['Atmosphere'] .'</td>
			<td>'.$entry['review_tags']['Cuisine'] .'</td>
-->
		</tr>';
		if ($flip == 1) $flip = 2;
		else $flip = 1;
	}
?>

		<tr class="ReviewElementEnd">
			<td colspan="6">
				<a href="#top">&gt;Go back to top</a>&nbsp;&nbsp;<a href="food">&gt;Go back to food</a>
			</td>
		</tr>
	</table>
