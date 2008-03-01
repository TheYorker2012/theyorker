<?php

echo('<?xml version="1.0" ?>
	<rss version="2.0" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
		xmlns:cc="http://web.resource.org/cc/" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">
	<channel>
	<title>The Yorker - ' . xml_escape($rss_title) . '</title>
	<link>' . xml_escape($rss_link) . '</link>
	<atom:link href="' . xml_escape($rss_link) . '" rel="self" type="application/rss+xml" />
	<description>' . xml_escape($rss_desc) . '</description>
	<itunes:summary>' . xml_escape($rss_itunes_summary) . '</itunes:summary>');

foreach ($rss_itunes_categories as $category)
{
	echo('<itunes:category text="'.xml_escape($category).'" />');
}
	echo('<language>en-gb</language>
	<copyright>Copyright 2006-'.date('Y').', The Yorker</copyright>
	<category>' . xml_escape($rss_category) . '</category>
	<pubDate>' . date('r',$rss_pubdate) . '</pubDate>
	<lastBuildDate>' . xml_escape($rss_lastbuild) . '</lastBuildDate>
	<docs>http://www.rssboard.org/rss-specification</docs>
	<itunes:image ref="'.xml_escape($itunes_image).'"/>
	<image>
		<url>' . xml_escape($rss_image) . '</url>
		<width>' . $rss_width . '</width>
		<height>' . $rss_height . '</height>
		<title>The Yorker - ' . xml_escape($rss_title) . '</title>
		<link>' . xml_escape($rss_link) . '</link>
	</image>
	<managingEditor>' . xml_escape($rss_email_ed) . '</managingEditor>
	<webMaster>' . xml_escape($rss_email_web) . '</webMaster>
		<itunes:author>'.xml_escape($itunes_author).'</itunes:author>
		<itunes:owner>
			<itunes:name>'.xml_escape($itunes_owner).'</itunes:name>
			<itunes:email>'.xml_escape($itunes_owner_email).'</itunes:email>
		</itunes:owner>');
		
foreach ($rss_items as $item) {
	$url = $this->config->item('static_web_address').'/media/podcasts/'.$item['filename'];
	echo('<item>
		<title>' . xml_escape($item['title']) . '</title>
		<description><![CDATA[' . xml_escape($item['description']) . ']]></description>
		<pubDate>' . date('r',$item['date']) . '</pubDate>
		<itunes:author>'.xml_escape($itunes_author).'</itunes:author>
		<itunes:explicit>No</itunes:explicit>
		<itunes:subtitle>'. xml_escape($item['description']) . '</itunes:subtitle>
		<itunes:summary>'. xml_escape($item['description']) . '</itunes:sumary>
		<enclosure url="'.$url.'" length="'.$item['length'].'" type="'.xml_escape($item['type']).'" />
		<guid isPermaLink="true">'.$url.'</guid>
		<link>'.$url.'</link>
		</item>');
}
echo('</channel></rss>');

?>
