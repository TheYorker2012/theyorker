<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file uri_tail_helper.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Simple helper function for using uri tails for redirects.
 */

/// Get the uri tail, all uri segments after the first @a $PreSegment segments.
/**
 * @param @$PreSegments int Number of uri segments to skip.
 */
function GetUriTail($PreSegments)
{
	$CI = & get_instance();
	return implode('/', array_slice($CI->uri->rsegment_array(), $PreSegments));
}

/// Redirec to the uri tail, all uri segments after the first @a $PreSegment segments.
/**
 * @param @$PreSegments int Number of uri segments to skip.
 */
function RedirectUriTail($PreSegments)
{
	redirect(GetUriTail($PreSegments));
}

?>