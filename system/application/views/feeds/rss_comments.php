<?php

echo('<?xml version="1.0" ?>
	<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
	<title>The Yorker - ' . $rss_title . '</title>
	<link>' . $rss_link . '</link>
	<atom:link href="' . $rss_link . '" rel="self" type="application/rss+xml" />
	<description>' . $rss_desc . '</description>
	<language>en-gb</language>
	<copyright>Copyright 2006-'.date('Y').', The Yorker</copyright>
	<category>' . $rss_category . '</category>
	<pubDate>' . $rss_pubdate . '</pubDate>
	<lastBuildDate>' . $rss_lastbuild . '</lastBuildDate>
	<docs>http://www.rssboard.org/rss-specification</docs>
	<image>
		<url>' . $rss_image . '</url>
		<width>' . $rss_width . '</width>
		<height>' . $rss_height . '</height>
		<title>The Yorker - ' . $rss_title . '</title>
		<link>' . $rss_link . '</link>
	</image>
	<managingEditor>' . $rss_email_ed . '</managingEditor>
	<webMaster>' . $rss_email_web . '</webMaster>');

foreach ($rss_items as $item) {
	if ($item['comment_anonymous']) {
		$author = 'Anonymous';
	} elseif (($item['user_firstname'] == '') && ($item['user_surname'] == '')) {
		$author = 'no name';
	} else {
		$author = $item['user_firstname'] . ' ' . $item['user_surname'];
	}
	$page = (floor(($item['article_comment_count'] - 1) / $comments_per_page) * $comments_per_page) + 1;
	$url = 'http://' . $_SERVER['SERVER_NAME'] . '/comments/thread/' . $item['comment_id'];
	echo('<item>
		<title>' . $author . ' on ' . $item['heading'] . '</title>
		<author>' . $rss_email_no . ' (' . $author . ')</author>
		<link>' . $url . '</link>
		<description><![CDATA[' . $item['comment_content_wikitext'] . ']]></description>
		<pubDate>' . date('r',$item['comment_post_time']) . '</pubDate>
		<guid isPermaLink="true">' . $url . '</guid>
		</item>');
}
echo('</channel></rss>');

?>
