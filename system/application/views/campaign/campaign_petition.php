<div id="RightColumn">
	<h2 class="first"><?php echo $sidebar_petition['title']; ?></h2>
	<div class="Entry">
		<h3><?php echo str_replace("%%count%%", $campaign['signatures'], $sidebar_petition['text']); ?></h3>
	</div>
	
	<h2><?php echo $sidebar_sign['title']; ?></h2>
	<div class="Entry">
		<?php
		if ($user == TRUE) 
		{
			if ($user['vote_id'] == FALSE)
			{
				echo str_replace("%%name%%", $user['firstname'].' '.$user['surname'], $sidebar_sign['new_text']);
		?>
		<form action="/campaign/signpetition" method="post" class="form">
			<fieldset>
				<input type="hidden" name="r_redirecturl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
				<input type="password" name="a_password" />
				<input type="submit" value="Sign" class="button" name="r_sign" />
			</fieldset>
		</form>
		<?php
			}
			else
			{
				echo str_replace("%%name%%", $user['firstname'].' '.$user['surname'], $sidebar_sign['withdraw_text']);
		?>
		<form action="/campaign/withdrawsignature" method="post" class="form">
			<fieldset>
				<input type="hidden" name="r_redirecturl" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
				<input type="submit" value="Withdraw" class="button" name="r_withdraw" />
			</fieldset>
		</form>
		<?php
			}
		}
		else
			echo $sidebar_sign['not_logged_in'];
		?>
	</div>
	
	<h2><?php echo $sidebar_more['title']; ?></h2>
	<div class="Entry">
		<?php echo $sidebar_more['text']; ?>
	</div>
	
	<h2><?php echo $sidebar_related['title']; ?></h2>
	<div class="Entry">
		<?php
		foreach ($article['related_articles'] as $related_articles)
		{
			echo '<b><a href="http://www.google.com/">'.$related_articles['heading'].'</a></b><br />';
		};
		?>
	</div>
	
	<h2><?php echo $sidebar_external['title']; ?></h2>
	<div class="Entry">
		<?php
 	       foreach ($article['links'] as $links)
		{
			echo '<b><a href="'.$links['url'].'">'.$links['name'].'</a></b><br />';
		};
		?>
	</div>
	
    	<h2><?php echo $sidebar_comments['title']; ?></h2>
	<div class="Entry">
		#TODO
	</div>
</div>


<div id="MainColumn">
	<div class="BlueBox">
		<span style="font-size: x-large;  color: #BBBBBB; "><?php echo $our_campaign['title']; ?></span><br />
		<?php echo $article['text']; ?>
	</div>

	<?php
		foreach ($article['fact_boxes'] as $fact_box)
		{
			echo '<div class="BlueBox">';
			echo '<h2>'.$fact_box['title'].'</h2>';
			echo $fact_box['wikitext'];
			echo '</div>';
		}
	?>

	<?php
		if (isset($sections['progress_reports']['entries']))
		{
			echo '<div class="BlueBox">';
			echo '<span style="font-size: x-large;  color: #BBBBBB; ">'.$progress_reports['title'].'</span><br />';
			foreach ($sections['progress_reports']['entries'] as $pr_entry)
			{
				echo '<br>';
				echo '<span style="font-size: large;  color: #BBBBBB; ">'.$pr_entry['date'].'</span><br />';
				echo $pr_entry['text'].'<br />';
			}
			if ($sections['progress_reports']['totalcount'] > 3)
				echo '<a href="/campaign/preports/">show more...</a>';
			echo '</div>';
	}
	?>
</div>

<?php
/*
echo '<div class="BlueBox"><pre>';
echo print_r($data);
echo '</pre></div>';
*/
?>
