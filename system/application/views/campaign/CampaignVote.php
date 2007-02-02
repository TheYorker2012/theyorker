<div class="RightToolbar ">
	<h4><?php echo $sections['sidebar_petition']['title']; ?></h4>
	<div class="Entry">
		<h5><?php echo str_replace("%%count%%", $campaign['signatures'], $sections['sidebar_petition']['text']); ?></h5>
	</div>
	
	<h4><?php echo $sections['sidebar_sign']['title']; ?></h4>
	<div class="Entry">
		<?php
		if ($user == TRUE) {
			echo str_replace("%%name%%", $user['firstname'].' '.$user['surname'], $sections['sidebar_sign']['text']);
			echo '	<form id="form1" name="form1" action="#" method="POST" class="form">
					<fieldset>
					<input type="text" />
					<input type="submit" value="Vote" class="button" />
					</fieldset>
					</form>';
		}
		else
			echo $sections['sidebar_sign']['not_logged_in'];
		?>
		</p>
	</div>
	
	<h4><?php echo $sections['sidebar_more']['title']; ?></h4>
	<div class="Entry">
		<?php echo $sections['sidebar_more']['text']; ?>
	</div>
	
	<h4><?php echo $sections['sidebar_related']['title']; ?></h4><!--Next 2 sections basically the same with different data and links-->
	<div class="Entry">
		<?php
		foreach ($sections['article']['related_articles'] as $related_articles)
		{
			echo '<b><a href="http://www.google.com/">'.$related_articles['heading'].'</a></b><br />';
		};
		?>
	</div>
	
	<h4><?php echo $sections['sidebar_external']['title']; ?></h4>
	<div class="Entry">
		<?php
 	       foreach ($sections['article']['links'] as $links)
		{
			echo '<b><a href="'.$links['url'].'">'.$links['name'].'</a></b><br />';
		};
		?>
	</div>
	
    	<h4><?php echo $sections['sidebar_comments']['title']; ?></h4>
	<div class="Entry">
		#TODO
	</div>
</div>

	 
	
<div class="grey_box ">
	<span style="font-size: x-large;  color: #BBBBBB; "><?php echo $sections['our_campaign']['title']; ?></span><br />
		<?php echo $sections['article']['text']; ?>
</div>

<?php
	foreach ($sections['article']['fact_boxes'] as $fact_box)
	{
		echo '<div class="blue_box">';
		echo '<h2>'.$fact_box['title'].'</h2>';
		echo $fact_box['wikitext'];
		echo '</div>';
	}
?>
	
<div class="grey_box">
	<span style="font-size: x-large;  color: #BBBBBB; "><?php echo $sections['progress_reports']['title']; ?></span><br />
	<?php
		foreach ($sections['progress_reports']['entries'] as $pr_entry)
		{
			echo '<br>';
			/*
			<div style="float: right; ">
			<a href='/news/oarticle/2'><img src='/images/prototype/news/thumb3.jpg' alt='Some Spy' title='Some Spy' /></a>
			</div>
			*/
			echo '<span style="font-size: large;  color: #BBBBBB; ">'.$pr_entry['date'].'</span><br />';
			echo $pr_entry['text'].'<br />';
		}
	?>
</div>

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>
