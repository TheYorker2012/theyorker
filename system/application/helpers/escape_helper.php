<?php

/**
 * @file escape_helper.php
 * @brief Simple functions for escaping.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 */
 
/// Htmlentities shortcut.
/**
 * @param $text string The text to escape.
 * @return string @a $text with xml bits escaped using htmlentities.
 */
function xml_escape($text)
{
	return htmlentities($text, ENT_QUOTES, 'utf-8');
}

/// Literalise a php value into javascript.
/**
 * @param $value string,int,bool,null The value to literalise.
 * @return string @a $value in javascript.
 */
function js_literalise($value)
{
	if (is_int($value) || is_float($value)) {
		return $value;
	}
	elseif (is_bool($value)) {
		return $value ? 'true' : 'false';
	}
	elseif (null === $value) {
		return 'null';
	}
	elseif (is_array($value)) {
		// represent arrays as hashes
		$result = '{';
		$comma = '';
		foreach ($value as $key => $item) {
			$result .= $comma.js_literalise($key).':'.js_literalise($item);
			$comma = ',';
		}
		$result .= '}';
		return $result;
	}
	else {
		return '\''.str_replace(
			array('\'',  '<?'),
			array('\\\'', '<"+"?'),
			$value
		).'\'';
	}
}

?>
