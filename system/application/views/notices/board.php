<?php

/**
 * @file views/notices/board.php
 * @brief Notice board view.
 *
 * Lists notices on a notice board.
 *
 * @see
 *	http://real.theyorker.co.uk/wiki/Functional:Notices
 *		Functional Specification sections "Directory Notices" and "VIP Notices"
 *
 * @version 21/03/2007 James Hogan (jh559)
 *	- Created.
 *
 * @pre CI string helper must be loaded for alternator(..).
 *
 * @param $Title string Filter description.
 * @param $Notices array Notice array, each notice with the following:
 *	- 'from_name' string (name of poster)
 *	- 'from_link' string (url or mailto link of the poster)
 *	- 'subject' string (subject string)
 *	- 'post_time' string (post time)
 *	- 'body' string (xhtml)
 *	- 'delete_link' string|NULL (link url or NULL if no permission)
 * @param $Menu array Top level menu items, each menu item with the following:
 *	- 'name' string (caption of the link)
 *	- 'link' string (URL to link to)
 *	- 'quantity' integer (Number of active notices on this board)
 *	- 'children' array (array of more menu items, possibly empty)
 *	- [potentially more style information later]
 * @todo archived notices.
 */

assert('function_exists("alternator") && "string helper required"');

/// Render a menu and its submenus.
/**
 * Echos the html for the specified menu array.
 * @param $DrawMenu array[menu_item] In the format of @a $Menu.
 */
function RenderMenu($DrawMenu)
{
	echo '<ul>';
	foreach ($DrawMenu as $child) {
		echo '<li>';
		echo '<a href="'.xml_escape($child['link']).'">'.xml_escape($child['name']).' ('.$child['quantity'].')</a>';
		if (isset($child['children']) && !empty($child['children'])) {
			echo RenderMenu($child['children']);
		}
		echo '</li>';
	}
	echo '</ul>';
}

?>

<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php RenderMenu($Menu); ?>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
	<?php
		echo '<h2>'.xml_escape($Title).'</h2>';
		
		// Reset the alternator
		alternator();
		
		// Render notices
		foreach ($Notices as $notice) {
			echo '
		<h3>'.xml_escape($notice['subject']).'</h3>
		<p>Posted <b>'.$notice['post_time'].'</b> by <a href="'.xml_escape($notice['from_link']).'">'.xml_escape($notice['from_name']).'</a></p>
		'.$notice['body'];
			if ($notice['delete_link'] !== NULL) {
				echo '<p><a href="'.$notice['delete_link'].'">delete notice</a></p>';
			}
		}
	?>
	</div>
</div>
