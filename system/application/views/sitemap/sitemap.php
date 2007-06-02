<?php
echo '<?xml version="1.0" encoding="UTF-8" ?>
	<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	';

foreach ($url_items as $item) {
	echo '
	<url>
		<loc>'.$item['loc'].'</loc>
		<lastmod>'.$item['lastmod'].'</lastmod>
		<changefreq>'.$item['changefreq'].'</changefreq>
		<priority>'.$item['priority'].'</priority>
	</url>';
}
echo '
</urlset>
';

?>