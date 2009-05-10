<?php

echo('<?xml version="1.0" ?>
	<rss version="2.0" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:atom="http://www.w3.org/2005/Atom"
		xmlns:cc="http://web.resource.org/cc/" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">
	<channel>
	<title>The Yorker - ' . $rss_title . '</title>
	<link>' . $rss_link . '</link>
	<atom:link href="' . $rss_link . '" rel="self" type="application/rss+xml" />
	<description>' . $rss_desc . '</description>
	<itunes:summary>' . $rss_itunes_summary . '</itunes:summary>');

foreach ($rss_itunes_categories as $category)
{
	echo('<itunes:category text="'.$category.'" />');
}
	echo('<language>en-gb</language>
	<copyright>Copyright 2006-'.date('Y').', The Yorker</copyright>
	<category>' . $rss_category . '</category>
	<pubDate>' . date('r',$rss_pubdate) . '</pubDate>
	<lastBuildDate>' . $rss_lastbuild . '</lastBuildDate>
	<docs>http://www.rssboard.org/rss-specification</docs>
	<itunes:image href="'.$itunes_image.'"/>
	<image>
		<url>' . $rss_image . '</url>
		<width>' . $rss_width . '</width>
		<height>' . $rss_height . '</height>
		<title>The Yorker - ' . $rss_title . '</title>
		<link>' . $rss_link . '</link>
	</image>
	<managingEditor>' . $rss_email_ed . '</managingEditor>
	<webMaster>' . $rss_email_web . '</webMaster>
		<itunes:author>'.$itunes_author.'</itunes:author>
		<itunes:owner>
			<itunes:name>'.$itunes_owner.'</itunes:name>
			<itunes:email>'.$itunes_owner_email.'</itunes:email>
		</itunes:owner>');
		
foreach ($rss_items as $item) {
	$url = $this->config->item('static_web_address').'/media/podcasts/'.$item['filename'];
	echo('<item>
		<title>' . $item['title'] . '</title>
		<description><![CDATA[' . $item['description'] . ']]></description>
		<pubDate>' . date('r',$item['date']) . '</pubDate>
		<itunes:author>'.$itunes_author.'</itunes:author>
		<itunes:explicit>No</itunes:explicit>
		<itunes:subtitle>'. $item['description'] . '</itunes:subtitle>
		<itunes:summary>'. $item['description'] . '</itunes:summary>
		<enclosure url="'.$url.'" length="'.$item['length'].'" type="'.$item['type'].'" />
		<guid isPermaLink="true">'.$url.'</guid>
		<link>'.$url.'</link>
		</item>');
}
echo('</channel></rss>');

?>
