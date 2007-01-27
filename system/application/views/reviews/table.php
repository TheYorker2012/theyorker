<div class="WholeContainer">
	<span class="SmallText"><span class="FaintText">Showing</span> <?php echo count($reviews['review_title']); ?> <span class="FaintText">entries from</span> Italian Food <span class="FaintText">ordered by</span> Star Rating<br /><br />
	<table class="ReviewList">
		<tr class="ReviewListTop">
			<td><a href="/reviews/table/name" name="top">Name</a></td>
			<td><a href="/reviews/table/star"><span class="SortedBy"><img style="display: inline;" src="/images/prototype/reviews/sortarrow.gif" alt="Ascending Order" /> Star Rating</span></a></td>
			<td><a href="/reviews/table/price">Price Rating</a></td>
			<td><a href="/reviews/table/rating">User Rating</a></td>
		</tr>

<?php
	//Probaility in error but keeping it the same with $flip anyway just in case... - frb501
	$flip = 1;
	for ($list = 0; $list < count($reviews['review_title']); $list++)
	{
	$flip++;
	if ($flip == 3) $flip = 1;
		echo '<tr class="ReviewElement'.$flip.'">
				<td>
				<a href="'.$review_link[$list].'"><img src="'.$reviews['review_image'][$list].'" alt="#" /></a>
				<h3><a href="/context/evil_eye_lounge/food">'.$reviews['review_title'][$list].'</a></h3><br />
				<a href="'.$reviews['review_website'][$list].'">'.$reviews['review_website'][$list].'</a><br />
				<a href="#">&gt;Food</a>&nbsp;&nbsp;<a href="#">&gt;Drink</a><br />
			</td>
			<td>'.$reviews['review_rating'][$list].' Stars</td>
			<td>'.$reviews['review_cost_type'][$list].'</td>
			<td>'.$reviews['review_user_rating'][$list].'/10</td>
		</tr>';
	}
?>

		<tr class="ReviewElementEnd">
			<td colspan="4">
				<a href="#top">&gt;Go back to top</a>&nbsp;&nbsp;<a href="food">&gt;Go back to food</a>
			</td>
		</tr>
	</table>
</div>
