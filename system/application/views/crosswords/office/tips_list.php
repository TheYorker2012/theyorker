<?php
/**
 * @file views/crosswords/office/tips_list.php
 * @param $Tips array
 * @param $AddForm null,InputInterfaces
 * @param $SelfUri string Uri to post to.
 */
?>
<div>
	tips list goes here
	<?php var_dump($Tips); ?>

<?php
	if (null !== $AddForm) {
		?><h2>add tip</h2><?php
		?><form class="form" method="post" action="<?php echo(xml_escape($SelfUri)); ?>"><?php
			?><fieldset><?php
				$AddForm->Load();
			?></fieldset><?php
		?></form><?php
	}
?>
</div>
