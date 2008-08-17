<?php

echo('<?xml version="1.0" ?>
	<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
	<title>The Yorker - ' . xml_escape($rss_title) . '</title>
	<link>' . xml_escape($rss_link) . '</link>
	<atom:link href="' . xml_escape($rss_link) . '" rel="self" type="application/rss+xml" />
	<description>' . xml_escape($rss_desc) . '</description>
	<language>en-gb</language>
	<copyright>Copyright 2006-'.date('Y').', The Yorker</copyright>
	<category>' . xml_escape($rss_category) . '</category>
	<pubDate>' . xml_escape($rss_pubdate) . '</pubDate>
	<lastBuildDate>' . xml_escape($rss_lastbuild) . '</lastBuildDate>
	<docs>http://www.rssboard.org/rss-specification</docs>
	<image>
		<url>' . xml_escape($rss_image) . '</url>
		<width>' . $rss_width . '</width>
		<height>' . $rss_height . '</height>
		<title>The Yorker - ' . xml_escape($rss_title) . '</title>
		<link>' . xml_escape($rss_link) . '</link>
	</image>
	<managingEditor>' . xml_escape($rss_email_ed) . '</managingEditor>
	<webMaster>' . xml_escape($rss_email_web) . '</webMaster>');

foreach ($rss_items as $item) {
	if ($item['comment_anonymous']) {
		$author = 'Anonymous';
	} elseif (($item['user_firstname'] == '') && ($item['user_surname'] == '')) {
		$author = 'no name';
	} else {
		$author = $item['user_firstname'] . ' ' . $item['user_surname'];
	}
	$page = (floor(($item['article_comment_count'] - 1) / $comments_per_page) * $comments_per_page) + 1;
	$url = 'http://' . $_SERVER['SERVER_NAME'] . '/news/' . $item['content_type_codename'] . '/' . $item['article_id']. '/' . $page . '/#CommentItem' . $item['comment_id'];
	echo('<item>
		<title>' . xml_escape($author) . ' on ' . xml_escape($item['article_content_heading']) . '</title>
		<author>' . xml_escape($rss_email_no) . ' (' . xml_escape($author) . ')</author>
		<link>' . $url . '</link>
		<description><![CDATA[' . xml_escape(substr($item['comment_content_wikitext'], 0, 150) . '...') . ']]></description>
		<pubDate>' . date('r',$item['comment_post_time']) . '</pubDate>
		<guid isPermaLink="true">' . $url . '</guid>
		</item>');
}
echo('</channel></rss>');

?>