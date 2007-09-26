<?php
function yorkermail($to, $subject, $message, $from) {
	require_once('Mail.php');
	$headers = array(
		'From' => $from,
		'To' => $to,
		'Subject' => $subject
	);
	$smtp = Mail::factory(
		'smtp',
		array(
			'host' => 'smtp.ntlworld.com',
			'auth' => false
		)
	);
	$mail = $smtp->send($to, $headers, $message);
	if (PEAR::isError($mail)) {
		throw new Exception('Email send failed: '.$mail->getMessage());
	}
}
?>
