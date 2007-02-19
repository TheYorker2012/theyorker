<div class="RightToolbar ">
	<h4><?php echo $sidebar_petition['title']; ?></h4>
	<div class="Entry">
		<h5><?php echo str_replace("%%count%%", $campaign['signatures'], $sidebar_petition['text']); ?></h5>
	</div>
	
	<h4><?php echo $sidebar_sign['title']; ?></h4>
	<div class="Entry">
		<?php
		if ($user == TRUE) 
		{
			if ($user['vote_id'] == FALSE)
			{
				echo str_replace("%%name%%", $user['firstname'].' '.$user['surname'], $sidebar_sign['new_text']);
				echo '<form name="sign" action="/campaign/signpetition" method="POST" class="form">
						<fieldset>
						<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
						<input type="password" name="a_password" />
						<input type="submit" value="Sign" class="button" name="r_sign" />
						</fieldset>
						</form>';
			}
			else
			{
				echo str_replace("%%name%%", $user['firstname'].' '.$user['surname'], $sidebar_sign['withdraw_text']);
				echo '<form name="sign" action="/campaign/withdrawsignature" method="POST" class="form">
						<fieldset>
						<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />
						<input type="submit" value="Withdraw" class="button" name="r_withdraw" />
						</fieldset>
						</form>';
			}
		}
		else
			echo $sidebar_sign['not_logged_in'];
		?>
		</p>
	</div>
	
	<h4><?php echo $sidebar_more['title']; ?></h4>
	<div class="Entry">
		<?php echo $sidebar_more['text']; ?>
	</div>
	
	<h4><?php echo $sidebar_related['title']; ?></h4>
	<div class="Entry">
		<?php
		foreach ($article['related_articles'] as $related_articles)
		{
			echo '<b><a href="http://www.google.com/">'.$related_articles['heading'].'</a></b><br />';
		};
		?>
	</div>
	
	<h4><?php echo $sidebar_external['title']; ?></h4>
	<div class="Entry">
		<?php
 	       foreach ($article['links'] as $links)
		{
			echo '<b><a href="'.$links['url'].'">'.$links['name'].'</a></b><br />';
		};
		?>
	</div>
	
    	<h4><?php echo $sidebar_comments['title']; ?></h4>
	<div class="Entry">
		#TODO
	</div>
</div>

	 
	
<div class="grey_box ">
	<span style="font-size: x-large;  color: #BBBBBB; "><?php echo $our_campaign['title']; ?></span><br />
		<?php echo $article['text']; ?>
</div>

<?php
	foreach ($article['fact_boxes'] as $fact_box)
	{
		echo '<div class="blue_box">';
		echo '<h2>'.$fact_box['title'].'</h2>';
		echo $fact_box['wikitext'];
		echo '</div>';
	}
?>

<?php
	if (isset($progress_reports['entries']))
	{
		echo '<div class="grey_box">';
		echo '<span style="font-size: x-large;  color: #BBBBBB; ">'.$progress_reports['title'].'</span><br />';
		foreach ($progress_reports['entries'] as $pr_entry)
		{
			echo '<br>';
			echo '<span style="font-size: large;  color: #BBBBBB; ">'.$pr_entry['date'].'</span><br />';
			echo $pr_entry['text'].'<br />';
		}
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
