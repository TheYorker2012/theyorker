<?php
/*
if ($class === 'error')
	echo 'warning_box"><img src="/images/prototype/homepage/error.png" alt="Error" title="Error"';
elseif ($class === 'warning')
	echo 'warning_box"><img src="/images/prototype/homepage/warning.png" alt="Warning" title="Warning"';
elseif ($class === 'information')
	echo 'information_box"><img src="/images/prototype/homepage/information.png" alt="Information" title="Information"';
elseif ($class === 'success')
	echo 'information_box"><img src="/images/prototype/homepage/sucess.png" alt="Success" title="Success"';
else
	echo 'information_box"><img src="/images/prototype/homepage/questionmark.png" alt="Question" title="Question"';
*/

/// @todo FIXME call text xml
?>

<?php
if ('fbml' === OutputMode()) {
	// Facebook markup language message
	if ('error' !== $class) {
		$class = 'explanation';
	}
	echo('<fb:'.$class.'><fb:message>');
	echo($text);
	echo('</fb:message></fb:'.$class.'>');
	
} else {
	// Normal html message
	// This uses a table so that the icon appears in its own column.
	?>
	<div class="message_<?php echo($class) ?>">
		<table border="0"><tr>
			<td><img src="/images/prototype/homepage/<?php echo($class) ?>.png" alt="<?php echo($class) ?>" width="30" height="30" /></td>
			<td><?php echo($text); ?></td>
		</tr></table>
	</div>
	<?php
}
?>
