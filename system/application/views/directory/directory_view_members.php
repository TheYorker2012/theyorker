<?php
if(empty($organisation['cards'])) {
?>
<div align="center">
	<b>This organisation has not listed any of its members.</b>
</div>
<?php
} else {
?>
<div class='RightToolbar'>
	<h4>Facts</h4>
	<div style='padding: 10px 5px 10px 5px;'>
		<p>	Number of members : 1337 </p>
		<p>	Member last joined : 5 hours ago</p>
		<p>	Male to female ratio: 2:1</p>
	</div>
	<h4>Groups</h4>
	<div style='padding: 10px 5px 10px 5px;'>
		<p>
			<?php
			foreach ($organisation['groups'] as $group) {
			?>

			<a href='<?php echo $group['href'] ?>'><?php echo $group['name'] ?></a><br />

			<?php
			}
			?>
		</p>
	</div>
</div>
<div style="width: 420px; margin: 0px; padding-right: 3px; ">
<?php
foreach ($organisation['cards'] as $member) {
?>
<div style="border: 1px solid #999; padding: 5px; font-size: small; margin-bottom: 4px; ">
<div style='float:right;'>
	<img src='/images/prototype/news/benest.png'/>
</div>
<span style="font-size: large;  color: #2DC6D7; "><?php echo $member['name']."<br />".$member['title']; ?></span>
<p style='font-size:small;'><?php echo $member['blurb']; ?></p>
<p>
<?php
if (!empty($member['course'])) {
?>
<img alt="Course" name="Course" src="/images/prototype/directory/scroll.gif" /> <?php echo $member['course']; ?><br />
<?php
}
if (!empty($member['email'])) {
?>
<img alt="Email" name="Email" src="/images/prototype/directory/email.gif" /> <a href='mailto:<?php echo $member['email']; ?>'><?php echo $member['email']; ?></a><br />
<?php
}
if (!empty($member['postal_address'])) {
?>
<img alt="Address" name="Address" src="/images/prototype/directory/address.gif" /> <?php echo $member['postal_address']; ?><br />
<?php
}
if(!empty($member['phone_internal']) or !empty($member['phone_external']) or !empty($member['phone_mobile'])){
?>
<img alt="Phone" name="Phone" src="/images/prototype/directory/phone.gif" />
<?php
	if (!empty($member['phone_internal'])) {
	echo $member['phone_internal'].", ";
	}
	if (!empty($member['phone_external'])) {
	echo $member['phone_external'].", ";
	}
	if (!empty($member['phone_mobile'])) {
	echo $member['phone_mobile'].", ";
	}
	echo "<br />";
}else{}
?>
<?php
if ($organisation['editmode']) {
?>
<form name='member' method='post' action='/viparea/directory/<?php echo $organisation['shortname']; ?>/editcontact/<?php echo $member['id']; ?>' class='form'>
<input name='member_edit_button' type='submit' id='member_edit_button' value='Edit' class='button' /><br />
</form>
<?php
}
?>
</p>
</div>
<?php
}
?>
</div>
<?php
}
?>
<div style="width: 420px; margin: 0px; padding-right: 3px; ">
Add a new member to this group [Add]
</div>