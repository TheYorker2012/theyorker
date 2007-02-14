<div class='RightToolbar'>
<h4>Areas for Attention</h4>
The following reviews are waiting to be published:
<ul>
	<li><a href='#'>Dan Ashby 02/02/2007</a></li>
	<li><a href='#'>Charlotte Chung 02/02/2007</a></li>
</ul>
<p>
<a href='#'>Information</a> has been updated and is waiting to be published.
</p>
<p>
There are <a href='#'>Comments</a> that have been reported for abuse.
</p>
<h4>What's this?</h4>
	<p>
		<?php echo 'whats_this'; ?>
	</p>
<h4>Other tasks</h4>
<ul>
	<li><a href='#'>Maintain my account</a></li>
	<li><a href='#'>Remove this directory entry</a></li>
</ul>
</div>

<div class="blue_box">
	<h2>user comments</h2>

<?php
	//If not empty
	if (! empty($comments))
	{
		//Show all comments
		for ($commentno = count($comments['comment_date']) - 1; $commentno > -1; $commentno--)
		{
		//Is reported box
		if ($comments['comment_reported_count'][$commentno] > 2)
		{
		echo '<div class="information_box"><img src="/images/prototype/homepage/infomark.png" alt="!" />This comment has been reported for abuse '.$comments['comment_reported_count'][$commentno].' times. You may wish to consider removing it</div>';
		}

		//Print Main Comment

		if ($comments['comment_reported_count'][$commentno] > 2)
			{
				echo '<b>'.strip_tags($comments['comment_author'][$commentno]).' | '.$comments['comment_date'][$commentno].'</b><br /><span class="orange">'.strip_tags($comments['comment_content'][$commentno]).'</span><br /><br />Reported ';
				echo '<span class="orange">'.$comments['comment_reported_count'][$commentno].'</span>';
				echo ' times<br /><a href="#">[remove]</a><br /><hr>';
			}
			else
			{
				echo '<b>'.strip_tags($comments['comment_author'][$commentno]).' | '.$comments['comment_date'][$commentno].'</b><br />'.strip_tags($comments['comment_content'][$commentno]).'<br /><br />Reported ';
				echo $comments['comment_reported_count'][$commentno];
				echo ' times<br /><a href="#">[remove]</a><br /><hr>';
			}
		}
	}

?>
	
</div>
