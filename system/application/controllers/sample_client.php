<?php

//
// +---------------------------------------------------------------------------+
// | Facebook Development Platform PHP5 client                                 |   
// +---------------------------------------------------------------------------+
// | Copyright (c) 2007 Facebook, Inc.                                         | 
// | All rights reserved.                                                      |
// |                                                                           |
// | Redistribution and use in source and binary forms, with or without        |
// | modification, are permitted provided that the following conditions        |
// | are met:                                                                  |
// |                                                                           |
// | 1. Redistributions of source code must retain the above copyright         |
// |    notice, this list of conditions and the following disclaimer.          |
// | 2. Redistributions in binary form must reproduce the above copyright      |
// |    notice, this list of conditions and the following disclaimer in the    |
// |    documentation and/or other materials provided with the distribution.   |
// |                                                                           |
// | THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR      |
// | IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES |
// | OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.   |
// | IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,          |
// | INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT  |
// | NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY     |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT       |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF  |
// | THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.         |
// +---------------------------------------------------------------------------+
// | For help with this library, contact developers-help@facebook.com          |
// +---------------------------------------------------------------------------+
//
//include_once 'facebook_conf.php';
//include_once 'facebookapi_php5_restlib.php';

class Sample_client extends controller
{
	function __construct()
	{
		parent::controller();
		
		$this->config->load('facebook');
		$this->load->helper('facebookapi');
	}
	
	function index()
	{
		if (!CheckPermissions('student')) return;
		
		$config = array(
			'api_server_base_url' => $this->config->item('api_server_base_url'),
			'login_server_base_url' => $this->config->item('login_server_base_url'),
			'rest_server_addr' => $this->config->item('rest_server_addr'),
			'api_key' => $this->config->item('api_key'),
			'secret' => $this->config->item('secret'),
			'next' => $this->config->item('next'),
			'login_url' => $this->config->item('login_url'),
			'debug' => $this->config->item('debug'),
		);
		
		$auth_token = $_REQUEST['auth_token'];
		if (!$auth_token) {
			header('Location: '.$config['login_url']);
			exit;
		}
		
		try {
			// Create our client object.  
			// This is a container for all of our static information.
			$client = new FacebookRestClient($config['rest_server_addr'], $config['api_key'], $config['secret'], null, false);
			
			// The required call: Establish session 
			// The session key is saved in the client lib for the whole PHP instance.
			$session_info = $client->auth_getSession($auth_token);
			$uid = $session_info['uid'];
			
			// Get the entire user profile.
			$user_profile = $client->users_getInfo($uid, $profile_field_array);
			$user_name = $user_profile[0]['name'];
			
			// Get five of the user's friends.
			$friends_array = array_slice($client->friends_get(), 0, 5);
			// See if these two friends know each other.
			if (isset($friends_array[0]) && isset($friends_array[1]))
			{
				$friend_info = $client->friends_areFriends($friends_array[0], $friends_array[1]);
			}
			
			// Get all of this user's photo albums.
			$albums = $client->photos_getAlbums($uid, null);
			if (!empty($albums)) {
				$album = $albums[0];
				// Get all photos from this album.
				$album_photos = $client->photos_get(null, $album['aid'], null);
			}
			
			// Get the profiles of users' five friends.
			$friend_profiles = $client->users_getInfo($friends_array, $profile_field_array);
			
			
			// Get events for the next few weeks.
			$events = $client->events_get($uid, null, time(), time() + (86400 * 21), null);
			
			if (isset($events[0]))
			{
				$first_event_eid = $events[0]['eid'];
				$event_members = $client->events_getMembers($events[0]['eid']);
				$event_count = count($event_members['attending']);
			}
								
			// Get all photos of the user, trim to 10.
			$photos = array_slice($client->photos_get($uid, null, null), 0, 10);
			
			// Get all notifications for the current user.
			$notifications = $client->notifications_get();
			
			// Get the user's groups, and save a few
			$groups = array_slice($client->groups_get($uid, null), 0, 5);
			
			$data = array(
				'user_profile' => $user_profile,
				'user_name' => $user_name,
				'friends_array' => $friends_array,
				'albums' => $albums,
				'friend_profiles' => $friend_profiles,
				'events' => $events,
				'photos' => $photos,
				'notifications' => $notifications,
				'groups' => $groups,
			);
			$this->main_frame->SetContent('calendar/facebooksample',$data);
			
		} catch (FacebookRestClientException $ex) {
			if (!isset($uid) && $ex->getCode() == API_EC_PARAM) {
				// This will happen if auth_getSession fails, which generally means you
				// just hit "reload" on the page with an already-used up auth_token
				// parameter.  Bounce back to facebook to get a fresh auth_token.
				header('Location: '.$config['login_url']);
				exit;
			} else {
				// Developers should probably handle other exceptions in a better way than this.
				$this->messages->AddMessage('error',$ex->getMessage());
			}
		}
		
		$this->main_frame->Load();
	}
}

?>
