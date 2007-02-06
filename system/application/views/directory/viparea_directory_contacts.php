<div class='RightToolbar'>
	<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
	<h4>Tasks</h4>
	<ul>
		<li><a href='/viparea/directory/<?php echo $organisation; ?>/contacts/add'>Add a new contact card.</a></li>
		<li><a href='/viparea/directory/<?php echo $organisation; ?>/contacts/editgroups'>Edit groups</a></li>
	</ul>
</div>
<div class='blue_box'>
<h2>current cards</h2>
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
</p>
</div>
<?php
}
?>
</div>
<div class='grey_box'>
	<form name='member' method='post' action='/viparea/directory/<?php echo $organisation['shortname']; ?>/contacts/update' class='form'>
		<h2>edit member</h2>
		<fieldset>
		<label for='member_name'>Name:</label>
		<input type='text' name='member_name' value='<?php if(!empty($editmember['name'])){echo $editmember['name'];} ?>'/>
		<br />
		<label for='member_title'>Title:</label>
		<input type='text' name='member_title' value='<?php if(!empty($editmember['title'])){echo $editmember['title'];} ?>'/>
		<br />
		<label for='member_group'>Group:</label>
		<input type='text' name='member_group' value='<?php if(!empty($editmember['group'])){echo $editmember['group'];} ?>'/>
		<br />
		<label for='member_title'>Image:</label>
		<input type='file' name='member_image'/>
		<br />
		<label for='member_course'>Course:</label>
		<input type='text' name='member_course' value='<?php if(!empty($editmember['course'])){echo $editmember['course'];} ?>'/>
		<br />
		<label for='member_email'>Email:</label>
		<input type='text' name='member_email' value='<?php if(!empty($editmember['email'])){echo $editmember['email'];} ?>'/>
		<br />
		<label for='member_about'>About:</label>
		<textarea name='member_about' cols='30' rows='7'><?php if(!empty($editmember['about'])){echo $editmember['about'];} ?></textarea>
		<br />
		<label for='member_address'>Postal Address:</label>
		<textarea name='member_address' cols='25' rows='4'><?php if(!empty($editmember['postal_address'])){echo $editmember['postal_address'];} ?></textarea>
		<br />
		<label for='member_phone_mobile'>Phone Mobile:</label>
		<input type='text' name='member_phone_mobile' value='<?php if(!empty($editmember['mobile_phone'])){echo $editmember['mobile_phone'];} ?>'/>
		<br />
		<label for='member_phone_internal'>Phone Internal:</label>
		<input type='text' name='member_phone_internal' value='<?php if(!empty($editmember['phone_internal'])){echo $editmember['phone_internal'];} ?>'/>
		<br />
		<label for='member_phone_external'>Phone External:</label>
		<input type='text' name='member_phone_external' value='<?php if(!empty($editmember['phone_external'])){echo $editmember['phone_external'];} ?>'/>
		<br />
		<label for='member_addbutton'></label>
		<input name='member_addbutton' type='submit' id='member_addbutton' value='Add/Edit' class='button' />
		</fieldset>
	</form>
</div>
<a href='/viparea/'>Back to the vip area.</a>