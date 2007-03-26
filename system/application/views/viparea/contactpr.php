<?php

/**
 * @file view/viparea/contactpr.php
 * @brief Form for contacting PR rep.
 */

?>

<div class="blue_box">
	<H2>PR Rep Contact information</H2>
	<P>
		Your pr rep contact details:<br />
		Photo<br />
		Name<br />
		Phone number + email address
	</P>
</div>

<div class="grey_box">
	<H2>Send PR Rep a Message</H2>
	
	<P>You can use this form to send your PR Rep an email.</P>
	
	<?php echo form_open($message_pr_target, array('class' => 'form')); ?>
	<fieldset>
		<label for="message_pr_subject">Subject:</label>
		<?php echo form_input(array('name' => 'message_pr_subject')); ?><br />
		<label for="message_pr_message">Message:</label>
		<?php echo form_textarea(array('name' => 'message_pr_message')); ?><br />
		<?php echo form_submit(array(
			'class' => 'button',
			'name'  => 'send_message',
			'value' => 'Send Message')); ?>
	</fieldset>
	<?php echo form_close(); ?>
</div>
