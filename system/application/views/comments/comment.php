<?php
/**
 * @file views/comments/comment.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @author Chris Travis
 * @brief An individual comment
 *
 * @param $Comment array Comment.
 * @param $ListNumber int Comment number.
 * @param $EditUrlPrefix string Url for editing (prepended)
 * @param $EditUrlPostfix string Url for editing (appended)
 * @param $ReportUrlPrefix string Url for reporting (prepended)
 * @param $ReportUrlPostfix string Url for reporting (appended)
 * @param $DeleteUrlPrefix string Url for deleting (prepended)
 * @param $DeleteUrlPostfix string Url for deleting (appended)
 * @param $UndeleteUrlPrefix string Url for undeleting (prepended)
 * @param $UndeleteUrlPostfix string Url for undeleting (appended)
 * @param $GoodUrlPrefix string Url for gooding (prepended)
 * @param $GoodUrlPostfix string Url for gooding (appended)
 * @param $UngoodUrlPrefix string Url for ungooding (prepended)
 * @param $UngoodUrlPostfix string Url for ungooding (appended)
 * @param $Mode 'mod','debug' Indicates moderator and debug modes
 */

if (!function_exists('star_rating')) {
	function star_rating ($rating) {
		$xhtml = '';
		$star_count = 0;
		$rating_left = $rating;
	
		while ($rating_left >= 2) {
			$xhtml .= '<img src="/images/icons/duck.gif" alt="User Rating: '.$rating.'" title="User Rating: '.$rating.'" />';
			$star_count++;
			$rating_left -= 2;
		}
		if ($rating_left == 1) {
			$xhtml .= '<img src="/images/icons/thumb_half.png" alt="User Rating: '.$rating.'" title="User Rating: '.$rating.'" />';
			$star_count++;
			$rating_left--;
		}
		while ($star_count < 5) {
			$xhtml .= '<img src="/images/icons/empty_duck.gif" alt="User Rating: '.$rating.'" title="User Rating: '.$rating.'" />';
			$star_count++;
		}
		return $xhtml;
	}
}

$show_as_deleted = $Comment['deleted'] && (!isset($Mode) || ($Mode != 'mod' && $Mode != 'debug'));
$anonymous = ($Comment['author'] == 'Anonymous');

if ($show_as_deleted) {
	$Comment['author'] = '<em>comment removed</em>';
	$Comment['xhtml'] = '';
	$Comment['edits'] = array();
} else {
	$Comment['author'] = '<b>'.$Comment['author'].'</b>';
}
if ($Comment['deleted']) {
	$Comment['edits'][] = array(
		'action' => 'del',
		'edit_time' => $Comment['deleted_time'],
		'by_author' => $Comment['deleted_by_owner'],
		'name'      => $Comment['deleted_name'],
	);
}

?>

<div id="CommentItem<?php echo($Comment['comment_id']); ?>" class="BlueBox"<?php if ($anonymous) { echo(' style="border-color:#999;"'); } ?>>
	<div style="float:right;margin:0.2em 0.5em;text-align:right">
		<?php
		if (!$show_as_deleted) {
			if ($anonymous) {
				?><img src="/images/prototype/directory/members/anon.png" alt="Anonymous" title="Anonymous Comment" /><?php
			} else {
				?><img src="/images/prototype/directory/members/no_image.png" alt="User Comment" title="User Comment" /><?php
			}
		}
		?>
		<?php if (!$show_as_deleted && NULL !== $Comment['rating']) {
			echo('<br />' . star_rating($Comment['rating']));
		} ?>
	</div>
	<div style="background-color:<?php echo ($anonymous) ? '#999' : '#20c1f0' ; ?>;color:#fff;padding:0.2em;margin:0">
		#<?php echo((isset($Comment['comment_order_num']) ? $Comment['comment_order_num'] : '') . ' ' . $Comment['author']); ?> - <?php echo($Comment['post_time']); ?>
	</div>
<?php
	if (!empty($Comment['edits'])) {
		echo('<ul class="comment_edit">');
		foreach ($Comment['edits'] as $edit) {
			echo('<li>');
			if (NULL !== $edit['edit_time']) {
				echo($edit['edit_time'].' - ');
			}
			if (isset($edit['action'])) {
				if ($edit['action'] == 'del') {
					echo('Deleted');
				} else {
					echo('Edited');
				}
			} else {
				echo('Edited');
			}
			echo(' by '.($edit['by_author'] ? 'the author' : 'a moderator'));
			if (!$edit['by_author'] && isset($Mode) && ($Mode === 'mod' || $Mode === 'debug') && isset($edit['name']) && NULL !== $edit['name']) {
				echo(' ('.$edit['name'].')');
			}
			echo('</li>');
		}
		echo('</ul>');
	}
	echo($Comment['xhtml']);
	if (isset($Mode) && ($Mode === 'mod' || $Mode === 'debug') && is_numeric($Comment['comment_id'])) {
		$abuse_links = array();
		if ($Comment['deleted']) {
			$abuse_links[] = '<a href="'.$UndeleteUrlPrefix.$Comment['comment_id'].$UndeleteUrlPostfix.'"><img src="/images/icons/note_go.png" alt="Un-Delete Comment" title="Un-Delete Comment" /> Un-Delete Comment</a>';
		} else {
			$abuse_links[] = '<a href="'.$DeleteUrlPrefix.$Comment['comment_id'].$DeleteUrlPostfix.'"><img src="/images/icons/note_delete.png" alt="Delete Comment" title="Delete Comment" /> Delete Comment</a>';
		}
		if ($Comment['good']) {
			$abuse_links[] = '<a href="'.$UngoodUrlPrefix.$Comment['comment_id'].$UngoodUrlPostfix.'"><img src="/images/icons/flag_red.png" alt="Un-Flag as Good" title="Un-Flag as Good" /> Un-Flag as Good</a>';
		} else {
			$abuse_links[] = '<a href="'.$GoodUrlPrefix.$Comment['comment_id'].$GoodUrlPostfix.'"><img src="/images/icons/flag_green.png" alt="Flag as Good" title="Flag as Good" /> Flag as Good</a>';
		}
		echo('<ul>');
		echo('<li>This comment has been reported for abuse '.$Comment['reported_count'].' time(s).</li>');
		echo('<li>'.implode('&nbsp;&nbsp;&nbsp;&nbsp;',$abuse_links).'</li>');
		echo('</ul>');
		if ($Mode === 'debug') { ?>
			<div style="background-color:#20c1f0;color:#fff;padding:0.2em;margin:0">
				<b>DEBUG: Comment Source</b>
			</div>
			<pre><?php echo(htmlentities($Comment['wikitext'])); ?></pre>
<?php	}
	} else if (!$show_as_deleted) {
		$links = array();
		if ($Comment['owned']) {
			$links[] = '<a href="'.$EditUrlPrefix.$Comment['comment_id'].$EditUrlPostfix.'"><img src="/images/icons/note_edit.png" alt="Edit Comment" title="Delete Comment" /> Edit Comment</a>';
			$links[] = '<a href="'.$DeleteUrlPrefix.$Comment['comment_id'].$DeleteUrlPostfix.'"><img src="/images/icons/note_delete.png" alt="Delete Comment" title="Delete Comment" /> Delete Comment</a>';
		}
		// Only show 'report abuse' link if 'no_report' index isn't set
		elseif (!array_key_exists('no_report', $Comment) || !$Comment['no_report']) {
			// Don't provide a working 'report abuse' link if only showing a comment preview
			if (!isset($Comment['preview'])) {
				$report_link = ' href="'.$ReportUrlPrefix.$Comment['thread_id'].'/'.$Comment['comment_id'].$ReportUrlPostfix.'"';
			} else {
				$report_link = '';
			}
			$links[] = '<a'.$report_link.'><img src="/images/icons/comments_delete.png" alt="Report Abuse" title="Report Abuse" /> Report Abuse</a>';
		}
		if (!empty($links) && !isset($Comment['no_links'])) {
			echo('<p style="font-size:x-small;">');
			echo(implode('&nbsp;&nbsp;&nbsp;&nbsp;', $links));
			echo('</p>');
		}
	}
	if (!$show_as_deleted) {
		?><div style="clear:both"></div><?php
	}
	?>
</div>
