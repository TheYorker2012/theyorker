	<span class="grey">Showing</span> <?php echo count($entries); ?>
	<span class="grey">entries from</span> <?php echo $this->uri->segment(3); ?>
	<span class="grey">ordered by</span> <?php echo $this->uri->segment(4); ?>
	<br /><br />
	<table class="ReviewList">
		<tr class="ReviewListTop">
			<td><a href="/reviews/table/<?php echo $this->uri->segment(3); ?>/name<?php if ($this->uri->segment(5)!='') echo '/'.$this->uri->segment(5).'/'.$this->uri->segment(6); ?>" name="tabletop">Name</a></td>
			<td><a href="/reviews/table/<?php echo $this->uri->segment(3); ?>/star<?php if ($this->uri->segment(5)!='') echo '/'.$this->uri->segment(5).'/'.$this->uri->segment(6); ?>"><span class="sorted_by"><img style="display: inline;" src="/images/prototype/reviews/sortarrow.gif" alt="v" /> Star Rating</span></a></td>
			<td><a href="/reviews/table/<?php echo $this->uri->segment(3); ?>/user<?php if ($this->uri->segment(5)!='') echo '/'.$this->uri->segment(5).'/'.$this->uri->segment(6); ?>">User Rating</a></td>

<?php
//Tag names at top of table
if (isset($review_tags))
{
	foreach ($review_tags as &$tag)
		{
			echo '<td><a href="/reviews/table/'.$this->uri->segment(3).'/'.$tag;
			if ($this->uri->segment(5) != '') echo '/'.$this->uri->segment(5).'/'.$this->uri->segment(6);
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
	$colnumber = 3; //3 starting columns Name/Star/User
	foreach ($entry['tagbox'] as &$taglist)
	{
	$colnumber++; //For ending tag to be correct
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
			<td colspan="'.$colnumber.'">
				<a href="#tabletop">&gt;Go back to top</a>&nbsp;&nbsp;<a href="/reviews/'.$this->uri->segment(3).'">&gt;Go back to '.$this->uri->segment(3).'</a>
			</td>
		</tr>
	</table>';
?>
