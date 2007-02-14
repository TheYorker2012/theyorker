<?php

echo '<?xml version=\'1.0\' ?>
	<rss version=\'2.0\'>
	<channel>
	<title>The Yorker - ' . $rss_title . '</title>
	<link>' . $rss_link . '</link>
	<description>' . $rss_desc . '</description>
	<language>en-gb</language>
	<copyright>Copyright 2006-07, The Yorker</copyright>
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
		<title>' . $item['heading'] . '</title>
		<author>';
	$temp_reporters = '';
	foreach ($item['authors'] as $reporter) {
		$temp_reporters .= $reporter['name'] . ', ';
	}
	echo substr($temp_reporters, 0, -2) . '</author>
		<link>http://www.theyorker.co.uk/news/' . $item['id'] . '</link>
		<description>' . $item['blurb'] . '</description>
		<pubDate>' . $item['date'] . '</pubDate>
		<guid isPermaLink=\'true\'>http://www.theyorker.co.uk/news/' . $item['id'] . '</guid>
		</item>';
}
echo '</channel></rss>';

?>