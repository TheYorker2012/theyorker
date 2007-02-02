	<div style="width: 220px; margin: 0; padding-left: 3px; float: right; ">
	<h4><?php echo $sections['sidebar_petition']['title']; ?></h4>
	<p style="color: #FF9933; margin-bottom: 10px; font-size:x-large; text-align: center; margin-top: 10px;">
		<?php echo str_replace("%%count%%", $campaign['signatures'], $sections['sidebar_petition']['text']); ?>
	</p>
	
	<h4><?php echo $sections['sidebar_sign']['title']; ?></h4>
	<div class='Entry'>
	<?php
	if ($user == TRUE)
		echo str_replace("%%name%%", $user['firstname'].' '.$user['surname'], $sections['sidebar_sign']['text']);
	else
		echo $sections['sidebar_sign']['not_logged_in'];
	?>
	
	#TODO</p>
	</div>
	
	<h4><?php echo $sections['sidebar_more']['title']; ?></h4>
	<?php echo $sections['sidebar_more']['text']; ?>
	
	<h4><?php echo $sections['sidebar_related']['title']; ?></h4><!--Next 2 sections basically the same with different data and links-->
	<p style="margin-top: 0px; padding: 8px;">
	<?php
        foreach ($sections['article']['related_articles'] as $related_articles)
	{
		echo '<b><a href="http://www.google.com/">'.$related_articles['heading'].'</a></b><br />';
	};
	?>
	</p>
	
	<h4><?php echo $sections['sidebar_external']['title']; ?></h4>
	<p style="margin-top: 0px; padding: 8px;">
	<?php
        foreach ($sections['article']['links'] as $links)
	{
		echo '<b><a href="'.$links['url'].'">'.$links['name'].'</a></b><br />';
	};
	?>
	</p>
	
    	<h4><?php echo $sections['sidebar_comments']['title']; ?></h4>
	<p style="margin-top: 0px; padding: 8px;">#TODO</p>
	
	</div>

	 
	
<div style="width: 420px; margin: 0px; padding-right: 3px; ">

	<div style="border: 1px solid #BBBBBB; padding: 6px; font-size: small; margin-bottom: 4px; ">
	<span style="font-size: x-large;  color: #BBBBBB; "><?php echo $sections['our_campaign']['title']; ?></span><br />
		<?php echo $sections['article']['text']; ?>
	</div>


<?php
	foreach ($sections['article']['fact_boxes'] as $fact_box)
	{
		echo '<div class=\'blue_box\'>';
		echo '<h2>'.$fact_box['title'].'</h2>';
		echo $fact_box['wikitext'];
		echo '</div>';
	}
?>
	
	<div style="border: 1px solid #BBBBBB; padding: 6px; font-size: small; margin-bottom: 4px; ">
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

</div>

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>