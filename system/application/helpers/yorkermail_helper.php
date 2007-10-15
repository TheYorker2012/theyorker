<?php
function yorkermail($to, $subject, $message, $from) {
	require_once('Mail.php');

	$CI = &get_instance();
	$CI->load->config('mail');
	$config = $CI->config->Item('mail');

	$headers = array(
		'From' => $from,
		'To' => $to,
		'Subject' => $subject
	);
	$smtp = Mail::factory(
		'smtp',
		array(
			'host' => $config['smtp_server'],
			'auth' => false
		)
	);
	$mail = $smtp->send($to, $headers, $message);
	if (PEAR::isError($mail)) {
		throw new Exception('Email send failed: '.$mail->getMessage());
	}
}
?>
