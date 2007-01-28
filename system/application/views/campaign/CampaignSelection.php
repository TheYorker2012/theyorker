<div class='RightToolbar'>
	<h4>About Campaigns</h4>
	<div class='Entry'>
		<a href='/news/article/2'><img src='/images/prototype/news/thumb3.jpg' alt='Some Spy' title='Some Spy' /></a>
		<h5>What are these about?</h5>
		<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>
	</div>
	<h4>What now?</h4>
	<div class='Entry'>
		<a href='/news/article/2'><img src='/images/prototype/news/thumb2.jpg' alt='Some Spy' title='Some Spy' /></a>
		<h5>What do I do now?</h5>
		<p>Well my friend, you wait and wait then see the results. This page will change to something else because you smell.</p>
	</div>
</div>

<div class='blue_box'>
	<h2><?php echo $sections['current_campaigns']['title']; ?></h2>
	<?php echo $sections['current_campaigns']['blurb']; ?><br /><br />
	<table width="100%">
		<?php
		$total_votes = 0;
                        foreach ($data['vars']['Campaign_List'] as $campaigns)
		{
			$total_votes += $campaigns['votes'];
		}
                        foreach ($data['vars']['Campaign_List'] as $campaigns)
		{
			$percentage = $campaigns['votes']/$total_votes*100;
			echo '<tr>
				<td>
				<b><a href="'.site_url('campaign/details/').'/'.$campaigns['id'].'">'.$campaigns['name'].'</a></b>
				</td><td style="width:40%; border: thin solid teal;">
				<div style="float: left; width: '.$percentage.'%; background-color: teal;">&nbsp</div>
				<div stlye="float: right;">&nbsp;'.round($percentage).'%</div>
				</td>
				</tr>';
		}
		?>
	</table>
	<br />
	<?php echo $sections['current_campaigns']['deadline_text']; ?>
</div>

<div class='grey_box'>
	<h2><?php echo $sections['vote_campaigns']['title']; ?></h2>
	<?php echo $sections['vote_campaigns']['blurb']; ?>
	<form id='form1' name='form1' action='#' method='POST'>
	</form>
	<form id='form1' name='form1' action='#' method='POST' class='form'>
	<table width="80%">
	<?php
                foreach ($data['vars']['Campaign_List'] as $campaigns)
                {
		echo '<tr><td style="text-align: right;">'.$campaigns['name'];
		echo '</td><td><fieldset style="display: inline;"><input type="submit" name="addorgform_addbutton" value="Vote" class="button" /></fieldset><br /></td></tr>';
	}
	?>
	</table>
	</form>
</div>
