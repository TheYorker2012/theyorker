<?php
/**
 * @param $Comment array Comment.
 * @param $ListNumber int Comment number.
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
?>

<?php
echo('<div class="'.($Comment['owned'] ? 'Blue':'Grey').'Box" id="CommentItem'.$Comment['comment_id'].'">');
echo('<h2>'.(isset($ListNumber)?$ListNumber.': ':'').$Comment['author'].'</h2>');
echo('<p><small>posted '.date(DATE_RFC822,(int)$Comment['post_time']).'</small></p>');
if (NULL !== $Comment['rating']) {
	echo('<p>rated: '.$Comment['rating'].'</p>');
}
echo($Comment['xhtml']);
if (isset($Mode) && ($Mode === 'mod' || $Mode === 'debug') && is_numeric($Comment['comment_id'])) {
	echo('<ul>');
	$abuse_links = array();
	if ($Comment['deleted']) {
		$abuse_links[] = '<a href="'.$UndeleteUrlPrefix.$Comment['comment_id'].$UndeleteUrlPostfix.'">undelete</a>';
	} else {
		$abuse_links[] = '<a href="'.$DeleteUrlPrefix.$Comment['comment_id'].$DeleteUrlPostfix.'">delete</a>';
	}
	if ($Comment['good']) {
		$abuse_links[] = '<a href="'.$UngoodUrlPrefix.$Comment['comment_id'].$UngoodUrlPostfix.'">unflag as good</a>';
	} else {
		$abuse_links[] = '<a href="'.$GoodUrlPrefix.$Comment['comment_id'].$GoodUrlPostfix.'">flag as good</a>';
	}
	
	echo('<li>There have been '.$Comment['reported_count'].' report(s) of abuse ('.
			implode(', ',$abuse_links).
		')</li>');
	echo('</ul>');
	
	// Show wikitext if in debug mode
	if ($Mode === 'debug') {
		echo('<div><div class="GreyBox">'.
				'<h2>source</h2>'.
				'<pre>'.htmlentities($Comment['wikitext']).'</pre>'.
			'</div></div>');
	}
} else {
	// Don't link to report properly if only a preview.
	if (is_numeric($Comment['comment_id'])) {
		$report_link = ' href="'.$ReportUrlPrefix.$Comment['thread_id'].'/'.$Comment['comment_id'].$ReportUrlPostfix.'"';
	} else {
		$report_link = '';
	}
	echo('<p><a'.$report_link.'>report abuse</a></p>');
}
echo('</div>');
?>