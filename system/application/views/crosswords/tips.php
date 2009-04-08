<?php
/**
 * @file views/crosswords/tips.php
 * @param $Categories array
 * @param $SelfUri string URI of current page.
 */
?>

<div class="BlueBox">
	<h2>crossword tips</h2>
	<p>
		Handy crossword tips are published with each crossword.
		This section lists all these tips by category.
	</p>
	<ul>
		<li><a href="<?php echo(site_url('crosswords')); ?>">Return to crosswords home</a></li>
	</ul>
</div>

<?php
foreach ($Categories as $category) {
	?><div class="BlueBox"><?php
		?><h2><?php
			?><a href="<?php echo(site_url('crosswords/tips/'.(int)$category['id'])); ?>"><?php
				echo(xml_escape($category['name']));
			?></a><?php
		?></h2><?php
		?><p><?php
			echo(nl2br(xml_escape($category['description'])));
		?></p><?php
		?><ul><?php
			?><li><?php
				?><a href="<?php echo(site_url('crosswords/tips/'.(int)$category['id'])); ?>"><?php
					?>View this tip category<?php
				?></a><?php
			?></li><?php
		?></ul><?php
	?></div><?php
}
?>
