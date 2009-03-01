<?php

// Ensure smiley helper is loaded
$CI = &get_instance();
$CI->load->helper('smiley');

/**
 * Parse Smileys
 *
 * Takes a string as input and swaps any contained smileys for wikitext
 *
 * @access	public
 * @param	string	the text to be parsed
 * @param	string	the URL to the folder containing the smiley images
 * @return	string
 */	
function wikitext_parse_smileys($str = '', $image_url = 'images/smileys/', $smileys = NULL)
{
	if ($image_url == '')
	{
		return $str;
	}

	if ( ! is_array($smileys))
	{
		if (FALSE === ($smileys = _get_smiley_array()))
		{
			return $str;
		}
	}
	
	// Add a trailing slash to the file path if needed
	$image_url = preg_replace("/(.+?)\/*$/", "\\1/",  $image_url);

	foreach ($smileys as $key => $val)
	{
		$str = str_replace($key, '[[image:'.$image_url.$smileys[$key][0].'|'.$smileys[$key][3]."]]", $str);
	}
	
	return $str;
}

?>