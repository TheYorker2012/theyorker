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
 * @param $ThreadUrlPrefix string Url for thread (prepended)
 * @param $ThreadUrlPostfix string Url for thread (appended)
 * @param $Threaded bool Whether we're part of a thread or just a list of comments.
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
$author_xml = '<b>' . xml_escape($Comment['author']) . '</b>';

// Only show 'report abuse' link if 'no_report' index isn't set
// Don't provide a working 'report abuse' link if only showing a comment preview
if (!$Comment['owned'] && !isset($Comment['preview']) && (!array_key_exists('no_report', $Comment) || !$Comment['no_report'])) {
	$report_link = ' href="'.$ReportUrlPrefix.$Comment['thread_id'].'/'.$Comment['comment_id'].$ReportUrlPostfix.'"';
}

?>

<?php if ($show_as_deleted) { ?>
	<div id="CommentItem<?php echo($Comment['comment_id']); ?>" class="CommentBox CommentDeleted">
		<img src="/images/icons/delete.png" alt="Comment Deleted" title="Comment Deleted" />
		comment deleted by <?php echo($Comment['deleted_by_owner'] ? 'the author' : 'a moderator'); ?>
	</div>
<?php } else { ?>

<div id="CommentItem<?php echo($Comment['comment_id']); ?>" class="CommentBox<?php if ($anonymous) echo(' CommentAnonymous'); ?>">
	<div class="CommentInfo">
		#<?php echo((isset($Comment['comment_order_num']) ? $Comment['comment_order_num'] : '') . ' ' . $author_xml); ?>
		<div class="CommentInfoSmall">
			<?php echo($Comment['post_time']); ?>
		</div>
		<?php if (!empty($report_link)) { ?>
			<div class="CommentInfoSmall">
				<a <?php echo($report_link); ?>>
					<img src="/images/icons/comments_delete.png" alt="Report Abuse" title="Report Abuse" /> Report
				</a>
			</div>
		<?php } ?>
	</div>

	<div class="CommentBody">
		<?php if (!$show_as_deleted && NULL !== $Comment['rating']) { ?>
			<div class="CommentRating">
				<?php echo(star_rating($Comment['rating'])); ?>
			</div>
		<?php } ?>

<?php
	if (!empty($Comment['edits'])) {
		// Only show the full list of edits when link is clicked
		$compressHistory = count($Comment['edits']) > 1;
		?><ul id="CommentItem<?php echo($Comment['comment_id']); ?>History" class="comment_edit"><?php
		$messageXml = null;
		foreach ($Comment['edits'] as $key => $edit) {
			?><li><?php
			$messageXml = '';
			if (NULL !== $edit['edit_time']) {
				$messageXml .= $edit['edit_time'].' - ';
			}
			if (isset($edit['action'])) {
				if ($edit['action'] == 'del') {
					$messageXml .= 'Deleted';
				} else {
					$messageXml .= 'Edited';
				}
			} else {
				$messageXml .= 'Edited';
			}
			$messageXml .= ' by '.($edit['by_author'] ? 'the author' : 'a moderator');
			if (!$edit['by_author'] && isset($Mode) && ($Mode === 'mod' || $Mode === 'debug') && isset($edit['name']) && NULL !== $edit['name']) {
				$messageXml .= ' ('.xml_escape($edit['name']).')';
			}
			echo($messageXml);
			if ($compressHistory && $key == count($Comment['edits'])-1) {
				?> (<a onclick="document.getElementById('CommentItem<?php echo($Comment['comment_id']); ?>History').style.display='none'; document.getElementById('CommentItem<?php echo($Comment['comment_id']); ?>ShortHistory').style.display='';">less</a>)<?php
			}
			?></li><?php
		}
		?></ul><?php
		if ($compressHistory) {
			?>
			<ul id="CommentItem<?php echo($Comment['comment_id']); ?>ShortHistory" class="comment_edit" style="display:none;">
				<li><?php echo($messageXml); ?>
				(<a onclick="document.getElementById('CommentItem<?php echo($Comment['comment_id']); ?>History').style.display=''; document.getElementById('CommentItem<?php echo($Comment['comment_id']); ?>ShortHistory').style.display='none';"><?php echo(count($Comment['edits'])-1); ?> more</a>)
			</li></ul>
			<script type="text/javascript">
			// <![CDATA[
				document.getElementById('CommentItem<?php echo($Comment['comment_id']); ?>History').style.display='none';
				document.getElementById('CommentItem<?php echo($Comment['comment_id']); ?>ShortHistory').style.display='';
			// ]]>
			</script>
			<?php
		}
	}
	echo($Comment['xhtml']);
	if (isset($Mode) && ($Mode === 'mod' || $Mode === 'debug') && is_numeric($Comment['comment_id'])) {
		$abuse_links = array();
		if ($Comment['deleted']) {
			$abuse_links[] = '<a href="'.$UndeleteUrlPrefix.$Comment['comment_id'].$UndeleteUrlPostfix.'"><img src="/images/icons/note_go.png" alt="" title="Un-Delete Comment" /> Un-Delete Comment</a>';
		} else {
			$abuse_links[] = '<a href="'.$EditUrlPrefix.$Comment['comment_id'].$EditUrlPostfix.'"><img src="/images/icons/note_edit.png" alt="" title="Delete Comment" /> Edit Comment</a>';
			$abuse_links[] = '<a href="'.$DeleteUrlPrefix.$Comment['comment_id'].$DeleteUrlPostfix.'"><img src="/images/icons/note_delete.png" alt="" title="Delete Comment" /> Delete Comment</a>';
		}
		if ($Comment['good']) {
			$abuse_links[] = '<a href="'.$UngoodUrlPrefix.$Comment['comment_id'].$UngoodUrlPostfix.'"><img src="/images/icons/flag_red.png" alt="" title="Un-Flag as Good" /> Un-Flag as Good</a>';
		} else {
			$abuse_links[] = '<a href="'.$GoodUrlPrefix.$Comment['comment_id'].$GoodUrlPostfix.'"><img src="/images/icons/flag_green.png" alt="" title="Flag as Good" /> Flag as Good</a>';
		}
		if (!$Threaded) {
			$abuse_links[] = '<a href="'.$ThreadUrlPrefix.$Comment['comment_id'].$ThreadUrlPostfix.'"><img src="/images/icons/note_go.png" alt="" title="Show this thread" /> Show thread</a>';
		}
		
		echo('<ul><li style="font-size:x-small;">');
		if ($Comment['reported_count'] != 0) {
			echo('This comment has been reported as abusive '.$Comment['reported_count'].' time'.($Comment['reported_count'] != 1 ? 's' : '').'.');
		} else {
			echo('This comment has not been reported as abusive.');
		}
		echo('</li></ul>');
		echo('<p style="font-size:x-small;">'.implode('&nbsp;&nbsp;&nbsp;&nbsp;',$abuse_links).'</p>');
		if ($Mode === 'debug') { ?>
			<div style="background-color:#20c1f0;color:#fff;padding:0.2em;margin:0">
				<b>DEBUG: Comment Source</b>
			</div>
			<pre><?php echo(xml_escape($Comment['wikitext'])); ?></pre>
<?php	}
	} else if (!$show_as_deleted) {
		$links = array();
		if ($Comment['owned']) {
			$links[] = '<a href="'.$EditUrlPrefix.$Comment['comment_id'].$EditUrlPostfix.'"><img src="/images/icons/note_edit.png" alt="Edit Comment" title="Edit Comment" /> Edit Comment</a>';
			$links[] = '<a href="'.$DeleteUrlPrefix.$Comment['comment_id'].$DeleteUrlPostfix.'"><img src="/images/icons/note_delete.png" alt="Delete Comment" title="Delete Comment" /> Delete Comment</a>';
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
</div>

<?php } ?>