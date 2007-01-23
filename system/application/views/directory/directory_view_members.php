<?php
foreach ($organisation['cards'] as $member) {
?>
<div class='MemberboxPhoto'>
		<div style='text-align:center;'>
			<img src='/images/prototype/directory/members/1562876378523.jpg'/>
		</div>
		<p>
			<?php
			if (!empty($member['course'])) {
			?>
			Studying: <?php echo $member['course']; ?><br />
			<?php
			}
			if (!empty($member['email'])) {
			?>
			E-mail: <a href='mailto:<?php echo $member['email']; ?>'><?php echo $member['email']; ?></a><br />
			<?php
			}
			if (!empty($member['postal_address'])) {
			?>
			Address: <?php echo $member['postal_address']; ?><br />
			<?php
			}
			if (!empty($member['phone_internal']) or !empty($member['phone_external']) or !empty($member['phone_mobile'])) {
			?>
			Phone numbers:
			<ul>
				<?php if (!empty($member['phone_internal'])) {
				echo '<li>Internal: '.$member['phone_internal'].'</li>';
				} ?>
				<?php if (!empty($member['phone_external'])) {
				echo '<li>External: '.$member['phone_external'].'</li>';
				} ?>
				<?php if (!empty($member['phone_mobile'])) {
				echo '<li>Mobile: '.$member['phone_mobile'].'</li>';
				} ?>
			</ul>
			<?php
			}
			?>
		</p>
</div>
<div class='Memberbox'>
	<h2><?php echo $member['name']; ?></h2>
	<h3><?php echo $member['title']; ?></h3>
	<p><?php echo $member['blurb']; ?></p>
</div>
<div class="clear">&nbsp;</div>
<?php
}
?>