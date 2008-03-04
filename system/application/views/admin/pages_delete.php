<?php if ($confirm) { ?>
<div class="RightToolbar">
	<?php if (isset($main_text)) { ?>
		<h4>What's this?</h4>
		<p><?php echo($main_text); ?></p>
	<?php } ?>
</div>
<div class="blue_box">
	<H2>Confirm page deletion</H2>
	<?php
	if ($information['default']) {
		$this->load->view('general/message', array(
			'class' => 'information',
			'text' => 'There are default values built into the site for this page, so this operation will revert the page and page properties to these default values. Any alterations that have been made will be lost.',
		));
	}
	?>
	<p><em>Are you sure you want to delete this page and its associated properties?</em></p>
	Codename: <?php echo(xml_escape($information['codename'])); ?><br />
	Title: <?php echo(xml_escape($information['head_title'])); ?><br />
	Description: <?php echo(xml_escape($information['description'])); ?><br />
	Keywords: <?php echo(xml_escape($information['keywords'])); ?><br />
	<br />
	<?php echo(count($information['properties'])); ?> Properties:<br />
	<?php
	foreach ($information['properties'] as $label => &$labelProperties) {
		foreach ($labelProperties as $type => &$property) {
		?>
			&nbsp;&nbsp;Property: <?php echo(xml_escape($label)); ?> (<?php echo(xml_escape($type)); ?>)<br />
		<?php
		}
	}
	?>
	<br />
	<form action="<?php echo $target; ?>" method="POST" class="form">
		<fieldset>
			<input type="hidden" name="confirm_delete" value="confirm" />
			<input type="submit" class="button" name="submit" value="Yes, Delete" />
		</fieldset>
	</form>
</div>
<?php } ?>
<a href="/admin/pages">Back to Pages Administration</a>