<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class TwitterXML
{
	private $username;
	private $password;
	private $http_status;
	private $error_string;

	function __construct ($username, $password) {
		$this->username = $username;
		$this->password = $password;
	}

	// Status Updates
	function updateStatus ($status) {
		$status = urlencode(stripslashes(urldecode($status)));
		$api_path = 'http://twitter.com/statuses/update.xml?status=' . $status;
		return $this->_APICall($api_path, TRUE, array(), TRUE);
	}

	// Direct Messages
	function getDirectMessages ($since_date = NULL, $since_msg_id = 0, $page_number = 1) {
		$api_path = 'http://twitter.com/direct_messages.xml';
		$params = array();
		if ($since_date !== NULL) $params['since'] = $since_date;
		if ($since_msg_id != 0) $params['since_id'] = $since_msg_id;
		if ($page_number != 1) $params['page'] = $page_number;
		return $this->_APICall($api_path, TRUE, $params);
	}

	function sendDirectMessage ($user_id, $message) {
		$api_path = 'http://twitter.com/direct_messages/new.xml';
		$params = array(
			'user'	=>	$user_id,
			'text'	=>	$message
		);
		return $this->_APICall($api_path, TRUE, $params, TRUE);
	}

	function deleteDirectMessage ($msg_id) {
		$api_path = 'http://twitter.com/direct_messages/destroy/' . $msg_id . '.xml';
		return $this->_APICall($api_path, TRUE);
	}

	// Friendships
	function addFriend ($id) {
		$api_path = 'http://twitter.com/friendships/create/' . $id . '.xml';
		return $this->_APICall($api_path, TRUE);
	}

	function removeFriend ($id) {
		$api_path = 'http://twitter.com/friendships/destroy/' . $id . '.xml';
		return $this->_APICall($api_path, TRUE);
	}

	// Users
	function getUserInfo ($id, $email = NULL) {
		$params = array();
		if ($email == NULL) {
			$api_path = 'http://twitter.com/users/show/' . $id . '.xml';
		} else {
			$api_path = 'http://twitter.com/users/show.xml';
			$params['email'] = $email;
		}
		return $this->_APICall($api_path, TRUE, $params);
	}

	// Misc
	function verifyCredentials () {
		$api_path = 'http://twitter.com/account/verify_credentials.xml';
		return $this->_APICall($api_path, TRUE);
	}

	function getError () {
		return $this->error_string;
	}

	function _APICall ($url, $login_required = FALSE, $params = array(), $post = FALSE) {
		$first = TRUE;
		foreach ($params as $key => $value) {
			$url .= ($first ? '?' : '&') . $key . '=' . urlencode($value);
			$first = FALSE;
		}
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
		if ($login_required) {
			curl_setopt($curl_handle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		}
		if ($post) {
			curl_setopt($curl_handle, CURLOPT_POST, TRUE);
		}
		$twitter_data = curl_exec($curl_handle);
		$this->http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		//print_r($twitter_data);
		//print_r($this->http_status);
		curl_close($curl_handle);
		if ($this->http_status == 200) {
			return new SimpleXMLElement($twitter_data);
		} else {
			$this->error_string = $twitter_data;
			return false;
		}
	}
}

/// Empty dummy class
class Twitter {}
?>