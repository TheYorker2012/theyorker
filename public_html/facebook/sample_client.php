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
include_once 'include/config.php';
include_once 'include/facebookapi_php5_restlib.inc.php';

session_start();

if (!array_key_exists('auth_token',$_GET)) {
	header('Location: '.$config['login_url']);
	exit;
} else {
	$auth_token = $_REQUEST['auth_token'];
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

} catch (FacebookRestClientException $ex) {
	if (!isset($uid) && $ex->getCode() == 100/*API_EC_PARAM*/) {
		// This will happen if auth_getSession fails, which generally means you
		// just hit "reload" on the page with an already-used up auth_token
		// parameter.	Bounce back to facebook to get a fresh auth_token.
		header('Location: '.$config['login_url']);
		exit;
	} else {
		// Developers should probably handle other exceptions in a better way than this.
		throw $ex;
	}
}

?>
<html><head><title>Example PHP5 REST Client</title></head>
<body>
<form method="post" action="http://www.facebook.com/logout.php">
<input type="hidden" name="confirm" value="1"/>
<a href=# onclick="this.parentNode.submit(); return false;">Log out of Facebook</a></form>
<?php
print "<h2>Hello $user_name!</h2>";

print '<P>You have '. $notifications['messages']['unread'] .' new ' . 
			(($notifications['messages']['unread'] == 1) ? 'message' : 'messages');
print '<p>' . $friend_profiles[0]['name'] . ' and ' . $friend_profiles[1]['name'] . 
			($friend_info[0]['are_friends'] ? ' are friends.' : ' are not friends.');
			
print '<h3>This is your profile:</h3>';
print_profiles($user_profile);
print "<HR>";
print '<h3>Here are some of your friends profiles</h3>';
print_profiles($friend_profiles);

print "<hr><p><h2>Your upcoming events </h2><P>";
if (is_array($events)) {
	foreach ($events as $id => $event) {
		print "<P><h4>" . $event['name'] . '</h4>';
		print "<P>Starts at: " . date('l dS \of F Y h:i:s A' , (int) ($event['start_time']));
		print "<P>Ends at: " . date('l dS \of F Y h:i:s A' , (int) ($event['end_time']));
		print "<P>The event venue is: " . $event['location'];
		if ($event['eid'] == $first_event_eid)
		{
			print "<P>There " . (($event_count == 1) ? 'is 1 person' : 
				"are $event_count people") . " attending this event.";
		}
		print "<hr>";
	}
}

if (isset($groups)) {
	print "<h2>Some groups you are in</h2>";
	foreach($groups as $group) {
		$pic_src = htmlspecialchars_decode($group['pic']);
		print '<img border=0 src="'.$pic_src.'"><br/>'.$group['name'].'</a><p>';
	}
}

if (is_array($photos)) {
	print "<h2>Photos of " . $user_profile[0]['name'] . "</h2>";
	print "<hr>";
	foreach($photos as $photo) {
		$photo_url = htmlspecialchars_decode($photo['src']);
		print '<img style="margin: 2px;" src="'. $photo_url . '">';
	}
}
if (isset($album_photos)) {
	print "<hr>";
	print "<h2>Photos from your album: $album[name]</h2>";
	print '<div style="clear: both;">';
	foreach($album_photos as $photo) {
		$photo_src = htmlspecialchars_decode($photo['src']);
		$photo_link = htmlspecialchars_decode($photo['link']);
		print '<div style="float: left; width: 150px;"><a href="'.$photo_link.'">';
		print '<img border=0 src="'.$photo_src.'"><br/>'.$photo['caption'].'</a></div>';
	}
	print '</div>';
}


print "</body></html>";

function print_profiles($profiles)
{
	foreach ($profiles as $id => $profile) {
		print "<h4>$profile[name] (id " . $profile['uid'] . ")</h4>";
		$pic = htmlspecialchars_decode($profile['pic']);
		if ($pic) {
			print "<img src=\"$pic\">\n";
		}
	}
}

?>
