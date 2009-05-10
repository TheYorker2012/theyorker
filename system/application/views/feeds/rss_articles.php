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
	echo('<item>
		<title>' . $item['heading'] . '</title>
		<author>' . $rss_email_no . ' (');
	$temp_reporters = '';
	foreach ($item['reporters'] as $reporter) {
		$temp_reporters .= $reporter['name'] . ', ';
	}
	echo(xml_escape(substr($temp_reporters, 0, -2)) . ')</author>
		<link>http://' . $_SERVER['SERVER_NAME']);
		if(!empty($item['organisation_codename'])){
			//If the article has an org name, it is a review.
			echo('/reviews/' . $item['type_codename'] . '/' . $item['organisation_codename'] . '/');
		}else{
			echo('/news/' . $item['type_codename'] . '/');
		}
		echo($item['id'] . '</link>
		<description><![CDATA[' . $item['blurb'] . ']]></description>
		<pubDate>' . date('r',$item['date']) . '</pubDate>
		<guid isPermaLink=\'true\'>http://' . $_SERVER['SERVER_NAME']);
		if(!empty($item['organisation_codename'])){
			//If the article has an org name, it is a review.
			echo('/reviews/' . $item['type_codename'] . '/' . $item['organisation_codename'] . '/');
		}else{
			echo('/news/' . $item['type_codename'] . '/');
		}
		echo($item['id'] . '</guid>
		</item>');
}
echo('</channel></rss>');

?>
