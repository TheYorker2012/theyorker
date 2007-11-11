<div id="RightColumn">
	<h2 class="first">Groups</h2>
	<div class="Entry">
<?php
	foreach ($organisation['groups'] as $group) {
?>
		<a href='<?php echo(htmlspecialchars($group['href'])); ?>'>
			<?php echo(htmlspecialchars($group['name'])); ?>
		</a><br />
<?php
	}
?>
	</div>

	<h2>Facts</h2>
	<div class="entry">
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
<?php
	if(empty($organisation['cards'])) {
?>
		<div align="center">
			<b>This organisation has not listed any members in this team.</b>
		</div>
<?php
	} else {
		foreach ($organisation['cards'] as $business_card) {
			$this->load->view('directory/business_card',array(
				'business_card' => $business_card,
				'editmode' => isset($organisation['editmode']),
			));
		}
	}
	?>
	</div>
</div>
