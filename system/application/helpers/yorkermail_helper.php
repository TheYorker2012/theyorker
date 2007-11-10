<?php
function yorkermail($to, $subject, $message, $from, $cc = array(), $bcc = array()) {
	require_once('Mail.php');

	$CI = &get_instance();
	$CI->load->config('mail');
	$config = $CI->config->Item('mail');

	if (!is_array($to)) {
		$to = array($to);
	}

	$headers = array(
		'From' => $from,
		'To' => implode(', ', $to),
		'Subject' => $subject,
		'Cc' => implode(', ', $cc)
	);
	$smtp = Mail::factory(
		'smtp',
		array(
			'host' => $config['smtp_server'],
			'auth' => false
		)
	);
	$mail = $smtp->send(array_merge($to, $cc, $bcc), $headers, $message);
	if (PEAR::isError($mail)) {
		throw new Exception('Email send failed: '.$mail->getMessage());
	}
}
?>
