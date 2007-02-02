<div class='RightToolbar'>
	<h4><?php echo $sections['sidebar_vote']['title']; ?></h4>
	<?php $sidebar_vote_text = str_replace("%%name%%", "User's Name", $sections['sidebar_vote']['text']); ?>
	<?php echo $sidebar_vote_text; ?>#TODO<br /><br /><!--ad padding, and the submit button-->
	
	<h4><?php echo $sections['sidebar_other_campaigns']['title']; ?></h4>
	<p style="margin-top: 0px; padding: 8px;">
	<?php
        foreach ($campaign_list as $key => $campaigns)
	{
		if ($key != $selected_campaign)
			echo '<b><a href="'.site_url('campaign/details/').'/'.$key.'">'.$campaigns['name'].'</a></b><br />';
	};
	?>
	</p>
	
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

<div class='grey_box'>
	<h2><?php echo $sections['article']['heading']; ?></h2>
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

<?php
/*
echo '<pre>';
echo print_r($data);
echo '</pre>';
*/
?>