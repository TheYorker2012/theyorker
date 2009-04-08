<?php
/**
 * @file views/crosswords/tips.php
 * @param $Categories array
 * @param $SelfUri string URI of current page.
 */
?>

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
