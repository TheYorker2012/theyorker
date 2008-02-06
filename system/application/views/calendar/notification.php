<?php
/**
 * @file views/calendar/notification.php
 * @brief A single calendar notificaiton view.
 * @param $Type
 *   'id' - the type identifier
 *   'class' - the class to view with {error,warning,information,facebook}
 *   'summary' - basically the name of the notification type such as "cancelled event"
 *   'description' - a general brief description of the notification
 *   'actions' - a list of actions that the user can perform to dismiss the event
 * @param $Keys - data that identifies a notification so that an action can be applied to the right notification
 * @param $Custom - arbitrary html extra info after description, before actions
 */

static $notification_id = 0;
$CI = & get_instance();

// Produce the html to go inside the message
$html = "<h3>$Type[summary]</h3>\n";
$html .= "<p><em>$Type[description]</em></p>\n$Custom\n";
$html .= '<form class="form" method="post" action="'.
		site_url($Paths->NotificationAction())
	."\">\n";
$html .= '<fieldset>';
$html .= '<input type="hidden" name="refer" value="'.$CI->uri->uri_string()."\" />\n";
$html .= "<input type=\"hidden\" name=\"calnot[type]\" value=\"$Type[id]\" />\n";
foreach ($Keys as $key => $value) {
	$value = xml_escape($value);
	$html .= "<input type=\"hidden\" name=\"calnot[keys][$key]\" value=\"$value\" />\n";
}
foreach ($Type['actions'] as $action => $description) {
	$html .= '<input class="button" type="submit"'.
		" name=\"calnot[action]\" value=\"$action\" />\n";
}
$html .= '</fieldset>';
$html .= "</form>\n";

// Divify and stuff
echo("<div id=\"calnot_${key}_div\" style=\"display:block;\">");
get_instance()->load->view('general/message', array(
	'class' => $Type['class'],
	'text' => $html,
));
echo("</div>");

++$notification_id;

?>
