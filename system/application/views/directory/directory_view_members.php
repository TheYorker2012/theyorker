<?php
foreach ($members as $member) {
?>
<div class='MemberboxPhoto'>
		<div style='text-align:center;'>
			<img src='/images/prototype/directory/members/1562876378523.jpg'/>
		</div>
		<p>
			<?php
			if (!empty($member['study'])) {
			?>
			Studying: <?php echo $member['study']; ?><br />
			<?php
			}
			if (!empty($member['business_card_email'])) {
			?>
			E-mail: <a href='mailto:<?php echo $member['business_card_email']; ?>'><?php echo $member['business_card_email']; ?></a><br />
			<?php
			}
			if (!empty($member['business_card_postal_address'])) {
			?>
			Address: <?php echo $member['business_card_postal_address']; ?><br />
			<?php
			}
			if (!empty($member['business_card_phone_internal']) or !empty($member['business_card_phone_external']) or !empty($member['business_card_mobile'])) {
			?>
			Phone numbers:
			<ul>
				<?php if (!empty($member['business_card_phone_internal'])) {
				echo '<li>Internal: '.$member['business_card_phone_internal'].'</li>';
				} ?>
				<?php if (!empty($member['business_card_phone_external'])) {
				echo '<li>External: '.$member['business_card_phone_external'].'</li>';
				} ?>
				<?php if (!empty($member['business_card_mobile'])) {
				echo '<li>Mobile: '.$member['business_card_mobile'].'</li>';
				} ?>
			</ul>
			<?php
			}
			?>
		</p>
</div>
<div class='Memberbox'>
	<h2><?php echo $member['business_card_name']; ?></h2>
	<h3><?php echo $member['business_card_title']; ?></h3>
	<p><?php echo $member['business_card_blurb']; ?></p>
</div>
<div class="clear">&nbsp;</div>
<?php
}
?>