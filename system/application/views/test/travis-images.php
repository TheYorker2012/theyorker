<?php foreach ($images as $img) { ?>
	<img src="/test/travis/image_test/<?php echo($img['url']); ?>/<?php echo($img['style']); ?>/<?php echo($img['position']); ?>" alt="Test Image" title="Test Image" />
	<br />
<?php } ?>