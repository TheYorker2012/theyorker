<div style="border: 1px solid #999; padding: 5px; font-size: small; margin-bottom: 4px; ">
	<div style='float:right;'>
		<img src='<?php echo $business_card['image']; ?>' alt='<?php echo $business_card['name']; ?>' />
	</div>
	<span style="font-size: large;  color: #2DC6D7; "><?php echo $business_card['name']."<br />".$business_card['title']; ?></span>
	<p style='font-size:small;'><?php echo $business_card['blurb']; ?></p>
	<p>
		<?php
		if (!empty($business_card['course'])) {
			?>
			<img alt="Course" name="Course" src="/images/prototype/directory/scroll.gif" /> <?php echo $business_card['course']; ?><br />
			<?php
		}
		if (!empty($business_card['email'])) {
			?>
			<img alt="Email" name="Email" src="/images/prototype/directory/email.gif" /> <a href='mailto:<?php echo $business_card['email']; ?>'><?php echo $business_card['email']; ?></a><br />
			<?php
		}
		if (!empty($business_card['postal_address'])) {
			?>
			<img alt="Address" name="Address" src="/images/prototype/directory/address.gif" /> <?php echo $business_card['postal_address']; ?><br />
			<?php
		}
		if(!empty($business_card['phone_internal']) or !empty($business_card['phone_external']) or !empty($business_card['phone_mobile'])){
		?>
			<img alt="Phone" name="Phone" src="/images/prototype/directory/phone.gif" />
			<?php
			if (!empty($business_card['phone_internal'])) {
			echo $business_card['phone_internal'].", ";
			}
			if (!empty($business_card['phone_external'])) {
			echo $business_card['phone_external'].", ";
			}
			if (!empty($business_card['phone_mobile'])) {
			echo $business_card['phone_mobile'].", ";
			}
			echo "<br />";
		}
		?>
		<?php
		if (isset($editmode) && $editmode) {
		?>
			<form name='member' method='post' action='<?php vip_url('members/cards/'.$business_card['id'].'/edit'); ?>' class='form'>
			<input name='member_edit_button' type='submit' id='member_edit_button' value='Edit' class='button' /><br />
			</form>
		<?php
		}
		?>
	</p>
</div>