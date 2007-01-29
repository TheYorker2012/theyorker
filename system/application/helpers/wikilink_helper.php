<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file wikilink_helper.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Simple helper functions for linking to wikis such as wikipedia and yorkipedia
 */

/**
 * @param $Wiki    array  Wiki information. Must contain:
 *	- ['base'] (Base URI of wiki)
 *	- ['type'] (Wiki type: 'mediawiki', 'mediawiki_no_urlrw')
 * @param $Article string Article title in wiki.
 */
function WikiLink($Wiki, $Article)
{
	$result = $Wiki['base'];
	switch ($Wiki['type']) {
		case 'local':
			// Local web address
			$result .= $Article;
			break;
			
		case 'mediawiki':
			// MediaWiki with url rewrite
			$result .= 'wiki/'.str_replace(' ','_',$Article);
			break;
			
		case 'mediawiki_no_urlrw':
			// MediaWiki without url rewrite
			$result .= 'index.php?title='.str_replace(' ','_',$Article);
			break;
			
		default:
			// Unknown wiki type
			throw new Exception('Unknown wiki type in WikiLink helper');
			break;
	}
	return $result;
}

?>