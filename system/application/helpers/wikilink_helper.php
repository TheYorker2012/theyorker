<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file wikilink_helper.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Simple helper functions for linking to wikis such as wikipedia and yorkipedia
 */

/// Get the preset wikis.
/**
 * @param $key string Key to wiki information.
 * @return array Wiki information structure.
 * @todo Consider where this might be better placed.
 */
function PresetWikis($key = FALSE)
{
	static $wiki_information = array(
			'local'  => array(
					'base' => '/',
					'type' => 'local',
				),
			'wikipedia'  => array(
					'base' => 'http://en.wikipedia.org/',
					'type' => 'mediawiki',
				),
			'yorkipedia' => array(
					'base' => 'http://yorkipedia.theyorker.co.uk/',
					'type' => 'mediawiki_no_urlrw',
				),
		);
	if (FALSE === $key)
		return $wiki_information;
	elseif (array_key_exists($key,$wiki_information))
		return $wiki_information[$key];
	else
		return FALSE;
}

/// Produce a uri for a wiki link.
/**
 * @param $Wikiname array/string Wiki information. If aray must contain:
 *	- ['base'] (Base URI of wiki)
 *	- ['type'] (Wiki type: 'mediawiki', 'mediawiki_no_urlrw')
 * @param $Article string Article title in wiki.
 */
function WikiLink($Wikiname, $Article)
{
	if (is_string($Wikiname)) {
		$Wiki = PresetWikis($Wikiname);
		if (FALSE === $Wiki)
			return '';
	} elseif (is_array($Wikiname)) {
		$Wiki = $Wikiname;
	} else {
		return '';
	}
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
			return '';
	}
	return $result;
}

?>