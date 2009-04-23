<?php

/**
 * @file init_helper.php
 * @brief Simple autoloaded helper to do some initialisation.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * - Start a session
 * - Warn if Magic quotes are enabled
 */

// Set the timezone
date_default_timezone_set('Europe/London');

// Warn if Magic quotes are enabled
SpecialInitAssert(!get_magic_quotes_gpc(), 'Magic Quotes are Enabled! They\'re evil and cause problems so please disable them in the PHP config.');


/// Assert to a bold error message.
/**
 * @param $Assertion   bool   Assertion condition that should be true.
 * @param $FailMessage string Failure message.
 * @return @a $Assertion.
 */
function SpecialInitAssert($Assertion, $FailMessage)
{
	if (!$Assertion) {
		$CI = &get_instance();
		$CI->load->library('messages');
		$CI->messages->AddMessage('error', '<h4>'.$FailMessage.'</h4>');
	}
	return $Assertion;
}

/// Destroy the current session if it contains no data
function CloseSessionIfEmpty()
{
	if (0 == strlen(session_encode())) {
		session_destroy();
	}
}
// If session is empty, we may as well delete it, so that googlebot doesn't clutter session directory
register_shutdown_function('CloseSessionIfEmpty');

?>
