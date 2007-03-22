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
		echo '<a href="'.$child['link'].'">'.$child['name'].' ('.$child['quantity'].')</a>';
		if (isset($child['children']) && !empty($child['children'])) {
			echo RenderMenu($child['children']);
		}
		echo '</li>';
	}
	echo '</ul>';
}

?>

<div class='RightToolbar'>
<?php
	RenderMenu($Menu);
?>
</div>

<div>
<?php
	echo '<b>'.$Title.'</b>';
	
	// Reset the alternator
	alternator();
	
	// Render notices
	foreach ($Notices as $notice) {
		echo '
<div class="'.alternator('blue_box','grey_box').'">
	<h3>'.$notice['subject'].'</h3>
	<p>Posted <b>'.$notice['post_time'].'</b> by <a href="'.$notice['from_link'].'">'.$notice['from_name'].'</a></p>
	'.$notice['body'];
		if ($notice['delete_link'] !== NULL) {
			echo '<a href="'.$notice['delete_link'].'">delete notice</a>';
		}
		echo '
</div>';
	}
?>
</div>
