	<div class="BlueBox">
		<div style="float:right;margin:0.2em 0.5em;text-align:right">
			<img src="<?php echo(($business_card_image_href != '') ? $business_card_image_href : site_url('images/prototype/directory/members/no_image.png')); ?>" alt="<?php echo(xml_escape($business_card_name)); ?>" title="<?php echo(xml_escape($business_card_name)); ?>" />
		</div>

		<div style="background-color:#20c1f0;color:#fff;padding:0.2em;margin:0;font-weight:bold;font-size:medium;">
			<?php echo(xml_escape($business_card_name)); ?>
		</div>

		<div style="color:#999;font-size:medium;font-weight:bold;margin:0;padding:0.2em;">
			<?php echo(xml_escape($business_card_title)); ?>
		</div>

		<?php if ($business_card_blurb !== NULL) { ?>
			<div>
				<?php echo(xml_escape($business_card_blurb)); ?>
			</div>
		<?php } ?>

		<div>
			<?php if ($business_card_course !== NULL) { ?>
				<img src="<?php echo site_url('images/icons/script.png'); ?>" alt="Course" title="Course" />
				<b>Course:</b>
				<?php echo(xml_escape($business_card_course)); ?>
				<br />
			<?php } ?>
			<?php if ($business_card_email !== NULL) { ?>
				<img src="<?php echo site_url('images/icons/email.png'); ?>" alt="E-Mail Address" title="E-Mail Address" />
				<b>E-Mail:</b>
				<?php echo(xml_escape($business_card_email)); ?>
				<br />
			<?php } ?>
			<?php if ($business_card_phone_internal !== NULL) { ?>
				<img src="<?php echo site_url('images/icons/phone.png'); ?>" alt="Internal Phone" title="Internal Phone" />
				<b>Phone (Int):</b>
				<?php echo(xml_escape($business_card_phone_internal)); ?>
				<br />
			<?php } ?>
			<?php if ($business_card_phone_external !== NULL) { ?>
				<img src="<?php echo site_url('images/icons/phone.png'); ?>" alt="External Phone" title="External Phone" />
				<b>Phone (Ext):</b>
				<?php echo(xml_escape($business_card_phone_external)); ?>
				<br />
			<?php } ?>
			<?php if ($business_card_mobile !== NULL) { ?>
				<img src="<?php echo site_url('images/icons/phone.png'); ?>" alt="Mobile" title="Mobile" />
				<b>Mobile:</b>
				<?php echo(xml_escape($business_card_mobile)); ?>
				<br />
			<?php } ?>
			<?php if ($business_card_postal_address !== NULL) { ?>
				<img src="<?php echo site_url('images/icons/map.png'); ?>" alt="Address" title="Address" />
				<b>Address:</b>
				<?php echo(xml_escape($business_card_postal_address)); ?>
				<br />
			<?php } ?>
			<?php if ((isset($archive_link)) && ($archive_link)) { ?>
				<a href="<?php echo site_url('news/archive/reporter/' . $business_card_id . '/'); ?>">
					<img src="<?php echo site_url('images/icons/book_open.png'); ?>" alt="Archive" title="Archive" />
					View articles I have written
				</a>
			<?php } ?>
		</div>

	</div>