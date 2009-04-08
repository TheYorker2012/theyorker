<?php
/**
 * @file views/crosswords/office/tips_list.php
 * @param $Tips array
 * @param $AddForm null,InputInterfaces
 * @param $SelfUri string Uri to post to.
 */
?>
<div>
<?php
	foreach ($Tips as &$tip) {
		?><div class="crosswordTip"><?php
			?><h3><?php
				echo(xml_escape($tip['category_name']));
			?></h3><?php
			echo($tip['content_xml']);
		?></div><?php
	}
	if (null !== $AddForm) {
		?><h2>add tip</h2><?php
		?><form class="form" method="post" action="<?php echo(xml_escape($SelfUri)); ?>"><?php
			?><fieldset><?php
				$AddForm->Load();

				?><input class="button" type="submit" value="Add tip" /><?php
			?></fieldset><?php
		?></form><?php
	}
?>
</div>
