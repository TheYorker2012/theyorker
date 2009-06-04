<?php

/**
 *	Flickr API Access
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 */

class FlickrAPI {

	private $api_key = '117e4fb7e8f54e5425b7dc4e28dec883';
	private $secret = '829bd64f0de1291d';
	private $format = 'php_serial';
	private $token = '72157619247322984-f4fde18e59ea802f';

	function __construct($api_key = NULL, $secret = NULL, $format = NULL, $token = NULL)
	{
		if (!empty($api_key)) $this->api_key = $api_key;
		if (!empty($secret)) $this->secret = $secret;
		if (!empty($format)) $this->format = $format;
		if (!empty($token)) $this->token = $token;
	}

	private function _request ($params, $sign = false)
	{
		if (empty($params['api_key'])) $params['api_key'] = $this->api_key;
		if (empty($params['format'])) $params['format'] = $this->format;

		if ($sign) {
			$sig = $this->secret;
			ksort($params);
			foreach ($params as $key => $value) {
				$sig .= $key . $value;
			}
			$params['api_sig'] = md5($sig);
		}

		$encoded_params = array();
		foreach ($params as $key => $value) {
			$encoded_params[] = urlencode($key) . '=' . urlencode($value);
		}
		$url = 'http://api.flickr.com/services/rest/?' . implode('&', $encoded_params);
		$response = file_get_contents($url);
		$response = unserialize($response);

		return $response;
	}

	function getChangesSince ($since, $page = 1)
	{
		$params = array(
			'method'		=>	'flickr.photos.recentlyUpdated',
			'min_date'		=>	$since,
			'extras'		=>	'license,owner_name,date_upload,last_update,tags',
			'per_page'		=>	'500',
			'page'			=>	$page,
			'auth_token'	=>	$this->token
		);
		return $this->_request($params, true);
	}

	function getPhotoInfo ($photo_id)
	{
		$params = array(
			'method'		=>	'flickr.photos.getInfo',
			'photo_id'		=>	$photo_id
		);
		return $this->_request($params);
	}

}

class Flickr {}

?>