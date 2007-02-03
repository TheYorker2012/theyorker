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
</div>





<div class='grey_box'>
	<h2><?php echo $sections['article']['heading']; ?></h2>
	<span class="black"><?php echo $sections['article']['text']; ?></span>
</div>


<?php
if (isset($sections['progress_reports']['entries']))
{
	echo '<div class="blue_box">';
	echo '<h2>'.$sections['progress_reports']['title'].'</h2>';
	foreach ($sections['progress_reports']['entries'] as $pr_entry)
	{
		echo '<h3 style="display: inline"><span class="grey">'.$pr_entry['date'].'</span></h3><br />';
		echo $pr_entry['text'].'<br /><br />';
	}
	echo '</div>';
}
?>

<div class='grey_box'>
	<h2><?php echo $sections['section_help']['title']; ?></h2>
	<?php echo $sections['section_help']['text']; ?>
</div>

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>
