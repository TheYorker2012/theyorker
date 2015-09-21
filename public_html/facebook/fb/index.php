<?php  
   //get the library
   include_once "src/facebook.php";
   //Set app details  
   $app_id = '318695444817002';  
   $application_secret = '278ec1ba4a3242c09b2a54aa96c87dfa';  
   
   $facebook = new Facebook(array(  
  'appId'  => $app_id,  
  'secret' => $application_secret,  
  'cookie' => true, // enable optional cookie support  
));  
    //Get info about user
    if ($facebook->getUser()) {  
    $user = $facebook->getUser();  
    $uid = $facebook->getUser();  
    $me = $facebook->api('/me/friends');  
?>
<?PHP
// Use cURL to get the RSS feed into a PHP string variable.
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,
        'http://www.dpreview.com/feeds/news.xml');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$xml = curl_exec($ch);
curl_close($ch);

// Include the handy XML data extraction functions.
include 'xml_regex.php';
// An RSS 2.0 feed must have a channel title, and it will
// come before the news items. So it's safe to grab the
// first title element and assume that it's the channel
// title.
$channel_title = value_in('title', $xml);
// An RSS 2.0 feed must also have a link element that
// points to the site that the feed came from.
$channel_link = value_in('link', $xml);
// Create an array of item elements from the XML feed.
$news_items = element_set('item', $xml);
foreach($news_items as $item) {
    $title = value_in('title', $item);
    $url = value_in('link', $item);
    $description = value_in('description', $item);
    $timestamp = strtotime(value_in('pubDate', $item));
    $item_array[] = array(
            'title' => $title,
            'url' => $url,
            'description' => $description,
            'timestamp' => $timestamp
    );
}

if (sizeof($item_array) > 0) {
    // First create a div element as a container for the whole
    // thing. This makes CSS styling easier.
    $html = '<div class="rss_feed_headlines">';
    // Markup the title of the channel as a hyperlink.
    $html .= '<h2 class="channel_title">'.
            '<a href="'.make_safe($channel_link).'">'.
            make_safe($channel_title).'</a></h2><dl>';
    // Now iterate through the data array, building HTML for
    // each news item.
    $count = 0;
    foreach ($item_array as $item) {
        $html .= '<dt><a href="'.make_safe($item['url']).'">'.
                make_safe($item['title']).'</a></dt>';
        $html .= '<dd>'.make_safe($item['description']);
        if ($item['timestamp'] != false) {
		    $html .= '<br />' .
                    '<span class="news_date">['.
                    gmdate('H:i, jS F T', $item['timestamp']).
                    ']</span>';
        }
        echo '</dd>';
        // Limit the output to five news items.
        if (++$count == 5) {
            break;
        }
    }
    $html .= '</dl></div>';
    echo $html;
}

    //Protect the app from The Yorker going horribly wrong.
function make_safe($string) {
    $string = preg_replace('#<!\[CDATA\[.*?\]\]>#s', '', $string);
    $string = strip_tags($string);
    // The next line requires PHP 5.2.3, unfortunately.
    //$string = htmlentities($string, ENT_QUOTES, 'UTF-8', false);
    // Instead, use this set of replacements in older versions of PHP.
    $string = str_replace('<', '&lt;', $string);
    $string = str_replace('>', '&gt;', $string);
    $string = str_replace('(', '&#40;', $string);
    $string = str_replace(')', '&#41;', $string);
    $string = str_replace('"', '&quot;', $string);
    $string = str_replace('\'', '&#039;', $string);
    return $string;
}
  
    }  
    else { 
    // Ask user to login 
    $canvas_page = "http://apps.facebook.com/theyorkeruk/";
    $auth_url = "http://facebook.com/dialog/oauth?client_id=" 
            . $app_id . "&redirect_uri=" . urlencode($canvas_page);
  
    echo("<script> top.location.href='" . $auth_url . "'</script>"); 
}  
  
?>  