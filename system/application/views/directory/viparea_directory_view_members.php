<div id="RightColumn">
	<h2 class="first">Groups</h2>
	<div class="Entry">
<?php
	$no_groups = true;
	foreach ($organisation['groups'] as $group) {
?>
		<a href='<?php echo(htmlspecialchars($group['href'])); ?>'>
			<?php
			if($current_group['id']==$group['id']){
				echo("<b>".htmlspecialchars($group['name'])."</b>");
			}else{
				echo(htmlspecialchars($group['name']));
			}
			?>
		</a>&nbsp;&nbsp;<a href='<?php echo vip_url('directory/contacts/deletegroup/'.$group['id']); ?>'><b>X</b></a><br />
<?php
	$no_groups = false;
	}
?>
	<br/>
	</div>
	<h2>Add a group</h2>
	<div class="entry">
	<?php
	if($no_groups){
	echo "<p>You need to create at least one group before you can begin creating contact cards</p>";
	}
	?>
		<form name='add_group' method='post' action='<?php vip_url('directory/contacts'); ?>' class='form'>
			<fieldset>
				<label for="group_name"></label>
				<input type="text" name="group_name">
				<input name='add_group_button' type='submit' id='add_group_button' value='Add' class='button' />
			<fieldset>
		</form>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
<?php
	if(empty($organisation['cards'])) {
?>
		<div align="center">
			<b>This organisation has no members listed in this group.</b>
		</div>
<?php
	} else {
	?>
	<h2>Editing group <?php echo $current_group['name'];?></h2>
	<?php
		foreach ($organisation['cards'] as $business_card) {
			$this->load->view('directory/business_card',array(
				'business_card' => $business_card,
				'editmode' => isset($organisation['editmode']),
			));
		}
	}
	?>
	</div>
	<?php
	if($no_groups==false){
	?>
	<div class="BlueBox">
	<h2>Add a card</h2>
<form name='add_card' method='post' action='<?php echo vip_url('directory/contacts'); ?>' class='form'>
	<fieldset>
		<label for='card_name'>Name:</label>
		<input type='text' name='card_name' value='<?php if(!empty($card_form['card_name'])){echo $card_form['card_name'];} ?>'/>
		<br />
		<label for='card_title'>Title:</label>
		<input type='text' name='card_title' value='<?php if(!empty($card_form['card_title'])){echo $card_form['card_title'];} ?>'/>
		<br />
		<label for='group_id'>Group:</label>
		<select name='group_id'>
		<?php
		foreach ($organisation['groups'] as $group) {
			?>
			<option value='<?php echo $group['id'] ?>'
			<?php if(!empty($card_form['group_id']))
					{
						if ($group['id']==$card_form['group_id'])
						{echo 'selected';}
					}
				?>
				>
				<?php echo $group['name'] ?></option>
			<?php
		}
		?>
		</select>
		<br />
		<label for='card_username'>Yorker Username:</label>
		<input type='text' name='card_username' value='<?php if(!empty($card_form['card_username'])){echo $card_form['card_username'];} ?>'/>
		<br />
		<label for='card_course'>Course:</label>
		<input type='text' name='card_course' value='<?php if(!empty($card_form['card_course'])){echo $card_form['card_course'];} ?>'/>
		<br />
		<label for='email'>Email:</label>
		<input type='text' name='email' value='<?php if(!empty($card_form['email'])){echo $card_form['email'];} ?>'/>
		<br />
		<label for='card_about'>About:</label>
		<textarea name='card_about' cols='28' rows='7'><?php if(!empty($card_form['card_about'])){echo $card_form['card_about'];} ?></textarea>
		<br />
		<label for='postal_address'>Postal Address:</label>
		<textarea name='postal_address' cols='25' rows='4'><?php if(!empty($card_form['postal_address'])){echo $card_form['postal_address'];} ?></textarea>
		<br />
		<label for='phone_mobile'>Phone Mobile:</label>
		<input type='text' name='phone_mobile' value='<?php if(!empty($card_form['phone_mobile'])){echo $card_form['phone_mobile'];} ?>'/>
		<br />
		<label for='phone_internal'>Phone Internal:</label>
		<input type='text' name='phone_internal' value='<?php if(!empty($card_form['phone_internal'])){echo $card_form['phone_internal'];} ?>'/>
		<br />
		<label for='phone_external'>Phone External:</label>
		<input type='text' name='phone_external' value='<?php if(!empty($card_form['phone_external'])){echo $card_form['phone_external'];} ?>'/>
		<br />
		<label for='phone_internal'>Phone Internal:</label>
		<input type='text' name='phone_internal' value='<?php if(!empty($card_form['phone_internal'])){echo $card_form['phone_internal'];} ?>'/>
		<br />
		<label for='phone_external'>Phone External:</label>
		<input type='text' name='phone_external' value='<?php if(!empty($card_form['phone_external'])){echo $card_form['phone_external'];} ?>'/>
		<br />
		<label for='card_addbutton'></label>
		<input name='card_addbutton' type='submit' id='card_addbutton' value='Add' class='button' />
		</fieldset>
	</form>
	</div>
	<?php
	}
	?>
</div>
