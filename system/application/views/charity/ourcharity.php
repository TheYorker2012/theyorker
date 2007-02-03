<div class='RightToolbar'>
	<h4><?php echo $sections['sidebar_goal']['title']; ?></h4>
	<div class='Entry'>
		<h5><?php echo $sections['sidebar_goal']['subtitle']; ?></h5>
		<p><?php echo $sections['charity']['target_text']; ?></p>
	</div>
	
	<h4><?php echo $sections['sidebar_funding']['title']; ?></h4>
	<div class='Entry'>
		<h5><?php echo $sections['sidebar_funding']['text']; ?></h5>
	</div>
	
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
</div>





<div class='grey_box'>
	<h2><?php echo $sections['article']['heading']; ?></h2>
	<span class="black"><?php echo $sections['article']['text']; ?></span>
</div>


<?php
if (isset($sections['progress_reports']['entries']))
{
	echo '<div class="blue_box">';
	echo '<span style="font-size: x-large;  color: #BBBBBB; ">'.$sections['progress_reports']['title'].'</span><br />';
	foreach ($sections['progress_reports']['entries'] as $pr_entry)
	{
		echo '<br>';
		echo '<span style="font-size: large;  color: #BBBBBB; ">'.$pr_entry['date'].'</span><br />';
		echo $pr_entry['text'].'<br />';
	}
	echo '</div>';
}
?>

<div class='grey_box'>
	<h2><?php echo $sections['section_help']['text']; ?></h2>
	<?php echo $sections['section_help']['title']; ?>
</div>

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>