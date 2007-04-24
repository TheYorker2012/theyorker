<?php
if($business_card['image_id'] == NULL)
{
	$business_card['image'] = '/images/prototype/directory/members/no_image.png';
} else {
	$business_card['image'] = photoLocation($business_card['image_id']);
}
?>
<div style="border: 1px solid #999; padding: 5px; font-size: small; margin-bottom: 4px; ">
	<div style='float:right;'>
		<img src='<?php echo $business_card['image']; ?>' alt='<?php echo $business_card['name']; ?>' />
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
				<input name='member_edit_button' type='button' onClick="parent.location='<?php echo vip_url('members/cards/'.$business_card['id'].'/edit'); ?>'"value='Edit' class='button' />
			</fieldset>
			</form>
		<?php
		}
		?>
	</p>
</div>