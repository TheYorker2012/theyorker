<div class="clear">&nbsp;</div>
<?php
if(empty($organisation['cards'])) {
?>
<div align="center" style='padding: 100px 0px 50px 0px;'>
	<b>This organisation has not listed any of its members.</b>
</div>
<?php
}
?>
<div class='RightToolbar'>
	<div class='RightToolbarHeader'>
	Teams
	</div>
	<div style='padding: 10px 5px 10px 5px;'>
		<p>
			<a href='#'>All (6)</a><br />
			<a href='#'>Team 1 (3)</a><br />
			<a href='#'>Team 2 (2)</a><br />
			<a href='#'>Team 3 (1)</a>
		</p>
	</div>
	<div class='RightToolbarHeader'>
	Facts
	</div>
	<div style='padding: 10px 5px 10px 5px;'>
		<p>
			Number of members : 1337<br />
			Society Founded : 100 ad<br />
			Member last joined : 5 hours ago<br />
			Attempts for world domination : 0
			Mean weight of members : rather heavy<br />
		</p>
	</div>
</div>
<div style="width: 400px; margin: 0px; padding-right: 3px; ">
<?php
foreach ($organisation['cards'] as $member) {
?>
<div style="border: 1px solid #2DC6D7; padding: 5px; font-size: small; margin-bottom: 4px; ">
<div style='float:right;'>
	<img src='/images/prototype/news/benest.png'/>
</div>
<span style="font-size: large;  color: #2DC6D7; "><?php echo $member['name']."<br />".$member['title']; ?></span>
<p style='font-size:small;'><?php echo $member['blurb']; ?></p>
<p>
<?php
if (!empty($member['course'])) {
?>
Studying: <?php echo $member['course']; ?><br />
<?php
}
if (!empty($member['email'])) {
?>
<img alt="Email" name="Email" src="/images/prototype/directory/email.gif" /> <a href='mailto:<?php echo $member['email']; ?>'><?php echo $member['email']; ?></a><br />
<?php
}
if (!empty($member['postal_address'])) {
?>
<img alt="Address" name="Address" src="/images/prototype/directory/flag.gif" /> <?php echo $member['postal_address']; ?><br />
<?php
}
?>
<img alt="Phone" name="Phone" src="/images/prototype/directory/phone.gif" /> 
<?php if (!empty($member['phone_internal'])) {
echo $member['phone_internal'].", ";
}
if (!empty($member['phone_external'])) {
echo $member['phone_external'].", ";
}
if (!empty($member['phone_mobile'])) {
echo $member['phone_mobile'].", ";
} ?>
</p>
</div>
<?php
}
?>
</div>