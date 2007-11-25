<?php

echo '<?xml version=\'1.0\' ?>
	<rss version=\'2.0\'>
	<channel>
	<title>The Yorker - ' . $rss_title . '</title>
	<link>' . $rss_link . '</link>
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
	<webMaster>' . $rss_email_web . '</webMaster>';

foreach ($rss_items as $item) {
	echo '<item>
		<title>(' . $item['type_name'] . ') ' . html_entity_decode($item['heading'],ENT_QUOTES,'UTF-8') . '</title>
		<author>' . $rss_email_ed . ' (';
	$temp_reporters = '';
	foreach ($item['reporters'] as $reporter) {
		$temp_reporters .= $reporter['name'] . ', ';
	}
	echo substr($temp_reporters, 0, -2) . ')</author>
		<link>http://www.theyorker.co.uk/news/' . $item['type_codename'] . '/' . $item['id'] . '</link>
		<description><![CDATA[' . html_entity_decode($item['blurb'],ENT_QUOTES,'UTF-8') . ']]></description>
		<pubDate>' . date('r',$item['date']) . '</pubDate>
		<guid isPermaLink=\'true\'>http://www.theyorker.co.uk/news/' . $item['type_codename'] . '/' . $item['id'] . '</guid>
		</item>';
}
echo '</channel></rss>';

?>