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
    print '<pre style="overflow: hidden;">';
    print_r($profile);
    print '</pre>';
  }
}

?>