<div style="border: 1px solid #999; padding: 5px; font-size: small; margin-bottom: 4px; ">
	<div style='float:right;'>
<?php if ($business_card['image_id'] == NULL) {
	$business_card['image_id'] = 0;
}
echo $this->image->getImage($business_card['image_id'], 'userimage');
?>
	</div>
	<span style="font-size: large;  color: #2DC6D7; ">
	<?php
	if (	isset($editmode) && $editmode &&
			isset($business_card['user_id']) && NULL != $business_card['user_id']) {
		echo '<a href="'.vip_url('members/info/'.$business_card['user_id']).'">'.$business_card['name'].'</a>';
	} else {
		echo $business_card['name'];
	}
	echo '<br />'.$business_card['title'];
	?>
	</span>
	<p style='font-size:small;'><?php echo $business_card['blurb']; ?></p>
	<p>
		<?php
		if (!empty($business_card['course'])) {
			?>
			<img alt="Course" name="Course" src="/images/icons/script.png" /> <?php echo $business_card['course']; ?><br />
			<?php
		}
		if (!empty($business_card['email'])) {
			if ($this->user_auth->isLoggedIn) {
			?>
			<img alt="Email" name="Email" src="/images/icons/email.png" /> <a href='mailto:<?php echo $business_card['email']; ?>'><?php echo $business_card['email']; ?></a><br />
			<?php
			} else {
			?>
			<img alt="Email" name="Email" src="/images/icons/email.png" /> Hidden. Please log in.<br />
			<?php
			}
		}
		if (!empty($business_card['postal_address'])) {
			?>
			<img alt="Address" name="Address" src="/images/icons/map.png" /> <?php echo $business_card['postal_address']; ?><br />
			<?php
		}
		if(!empty($business_card['phone_internal']) or !empty($business_card['phone_external']) or !empty($business_card['phone_mobile'])){
		?>
			<img alt="Phone" name="Phone" src="/images/icons/phone.png" />
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
			<form name='member' method='post' action='<?php echo vip_url('directory/contacts/deletecard/'.$business_card['id']); ?>' class='form'>
			<fieldset>
				<?php
				if($business_card['approved']){
					echo "<small>This card is live.</small>";
				}else{
					if (PermissionsSubset('office', GetUserLevel())){
					?>
					<input name='member_approve_button' type='button' onClick="parent.location='<?php echo vip_url('directory/contacts/approvecard/'.$business_card['id']); ?>'"value='Approve' class='button' />
					<?php
					}else{
					echo "<small>Waiting approval.</small>";
					}
				}
				if (PermissionsSubset('office', GetUserLevel())){ ?>
				<input name='member_delete_button' type='submit' onClick="return confirm('Are you sure you want to delete <?php echo $business_card['name']; ?>s contact card?');" value='Delete' class='button' />
				<?php }?>
				<input name='member_edit_button' type='button' onClick="parent.location='<?php echo vip_url('directory/cards/'.$business_card['id'].'/edit'); ?>'"value='Edit' class='button' />
			</fieldset>
			</form>
		<?php
		}
		?>
	</p>
</div>