<div id="RightColumn">
	<h2 class="first">Groups</h2>
	<div class="Entry">
<?php
	foreach ($organisation['groups'] as $group) {
?>
		<a href='<?php echo(htmlspecialchars($group['href'])); ?>'>
			<?php echo(htmlspecialchars($group['name'])); ?>
		</a>&nbsp;<a href='<?php echo vip_url('directory/contacts/deletegroup/'.$group['id']); ?>'>Del</a><br />
<?php
	}
?>
	<br/>
	</div>
	<h2>Add a group</h2>
	<div class="entry">
		<form name='add_group' method='post' action='<?php vip_url('directory/contacts'); ?>' class='form'>
			<fieldset>
				<label for="group_name"></label>
				<input type="text" name="group_name">
				<input name='add_group_button' type='submit' id='add_group_button' value='Add' class='button' />
			<fieldset>
		</form>
	</div>
	<h2>Add a card</h2>
	<div class="entry">
	You can't add any contact cards untill you have at least one group.
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
