<div class='RightToolbar'>
	<h4>Leagues</h4>
	<div class='Entry'>

<?php
//Display leagues

if (isset($league_data) == 1)
{
	foreach ($league_data as $league_entry)
	{
		if ($league_entry['league_image_id'] != 0) //Don't display if no image otherwise alt text floods out
		{
		echo
		"
		<div class='LifestylePuffer'>
		<a href='/reviews/leagues/".$league_entry['league_codename']."'>
		<img src='/images/images/".$league_entry['league_image_id'].".gif' alt='".$league_entry['league_name']."' />
		</a>
		</div>
		";
		}
	}
}
?>

</div>
</div>
<div class='grey_box'>
	<h2>browse by</h2>
	<span class="black"><?php echo $main_blurb; ?></span><br /><br />

<?php
//As far as I can tell we are going to show the first 2 columns only on this page
//Hence a for loop is probaility not worth it...

echo '<div class="half_right"><h3 style="display: inline;">';

//Check that it exists before trying to display
if (isset($table_data[$table_data['tag_group_names'][1]]) == 1)
{
	echo $table_data['tag_group_names'][1];
	echo '</h3><br />';

	foreach($table_data[$table_data['tag_group_names'][1]] as $tag)
	{
		echo anchor('reviews/table/food/star/'.$table_data['tag_group_names'][1].'/'.$tag, $tag).'<br />';
	}

}
//All types
echo anchor('reviews/table/food/name','All types');

echo'</div>';

echo '<div class="half_left"><h3 style="display: inline;">';

//Check that it exists before trying to display
if (isset($table_data[$table_data['tag_group_names'][0]]) == 1)
{

echo $table_data['tag_group_names'][0];
echo '</h3><br />';

foreach($table_data[$table_data['tag_group_names'][0]] as $tag)
{
	echo anchor('reviews/table/food/star/'.$table_data['tag_group_names'][0].'/'.$tag, $tag).'<br />';
}
}

//All types
echo anchor('reviews/table/food/name','All types');

echo'</div>';

?>

</div>

<div class='blue_box'>
		<h2>featured article</h2>
		<?php echo '<a href="'.$article_link.'">'; ?><img style="float: right;" src='/images/prototype/news/thumb4.jpg' alt='Soldier about to get run over by a tank' title='Soldier about to get run over by a tank' /></a>
		<h3><?php echo anchor($article_link, $article_title); ?></h3>
		<span style='font-size: medium;'><b><?php echo "<a href='".$article_author_link."'>".$article_author."</a>"; ?></b></span><br />
		<?php echo $article_date ?><br />
		<span class="orange"><?php echo anchor($article_link, 'Read more...', array('class' => 'orange')); ?></span>
	        <p>
			<?php echo $article_content; ?>
		</p>
</div>
