<?php if (count($articles) == 0) { ?>
		<div>
			You do not appear to have written any articles for The Yorker using the bylines
			that you have linked with your Facebook account. To link additional bylines
			click <a href="http://apps.facebook.com/theyorker/myarticles/bylines/">here</a>.
		</div>
<?php }
foreach ($articles as $a) {
	$reporters = array();
	foreach ($a['reporters'] as $r) $reporters[] = $r['name'];
	$reporters = implode(', ', $reporters); ?>
		<div style="clear:both; border-bottom:1px solid #bbb; padding-bottom: 5px; margin-bottom: 10px;">
			<a href="http://www.theyorker.co.uk/news/<?php echo($a['type_codename']); ?>/<?php echo($a['id']); ?>">
				<img src="http://www.theyorker.co.uk/photos/small/<?php echo($a['photo_id']); ?>" alt="<?php echo($a['photo_title']); ?>" style="float:left; margin-bottom:5px" />
			</a>
			<div style="margin-left:75px">
				<span style="float:right">
					<fb:share-button class="url" href="http://www.theyorker.co.uk/news/<?php echo($a['type_codename']); ?>/<?php echo($a['id']); ?>" />
<?php if (isset($extra_ops)) { ?>
					<a href="http://apps.facebook.com/theyorker/myarticles/article/feedpost/<?php echo($a['id']); ?>/">
						Post on my Feed
					</a>
<?php } ?>
				</span>
				<a href="http://www.theyorker.co.uk/news/<?php echo($a['type_codename']); ?>/<?php echo($a['id']); ?>"><b><?php echo($a['heading']); ?></b></a>
				<br /><?php echo($a['blurb']); ?><br />
				<i>by <?php echo($reporters); ?></i>
			</div>
			<div style="clear:both"></div>
		</div>
<?php } ?>