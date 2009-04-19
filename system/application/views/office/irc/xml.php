<?php
/**
 * @file views/office/irc/xml.php
 * @author James Hogan (jh559)
 *
 * @param $Errors    Error messages, with code attribute.
 *	This is for errors relating to ajax and connection, not irc.
 * @param $Messages  Messages recieved.
 */
header('content-type: text/xml');
$this->load->helper('xml');

// Simple text fields, field => tag
$simple_fields = array(
	'sender',
	'to',
	'content',
	'names',
	'topic',
	'channel',
);

?><<?php ?>?xml version="1.0" encoding="UTF-8"?><?php
?><irc><?php
	if (isset($Errors) && is_array($Errors)) {
		foreach ($Errors as $error) {
			write_xml($error, 'error');
		}
	}
	if (isset($Messages) && is_array($Messages)) {
		foreach ($Messages as $message) {
			echo('<msg type="'.xml_escape($message['type']).'"');
			if (isset($message['highlight'])) {
				echo(' highlight="1"');
			}
			echo('>');
			if (isset($message['received'])) {
				echo('<time>'.date('H:i', $message['received']).'</time>');
			}
			foreach ($simple_fields as $field) {
				if (isset($message[$field])) {
					write_xml($message[$field], $field);
				}
			}
			?></msg><?php
		}
	}
?></irc>
