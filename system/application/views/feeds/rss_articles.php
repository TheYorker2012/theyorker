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
	echo('<item>
		<title>(' . xml_escape($item['type_name']) . ') ' . xml_escape($item['heading']) . '</title>
		<author>' . xml_escape($rss_email_no) . ' (');
	$temp_reporters = '';
	foreach ($item['reporters'] as $reporter) {
		$temp_reporters .= $reporter['name'] . ', ';
	}
	echo(xml_escape(substr($temp_reporters, 0, -2)) . ')</author>
		<link>' . site_url('/news/' . $item['type_codename'] . '/' . $item['id']) . '</link>
		<description><![CDATA[' . xml_escape($item['blurb']) . ']]></description>
		<pubDate>' . date('r',$item['date']) . '</pubDate>
		<guid isPermaLink=\'true\'>' . site_url('/news/' . $item['type_codename'] . '/' . $item['id']) . '</guid>
		</item>');
}
echo('</channel></rss>');

?>