<div class="ClearFlexiBox Box13<?php if (!empty($last)) echo(' FlexiBoxLast'); ?>"<?php if (!empty($position)) { echo(' style="float:' . $position . '"'); } ?>>
<!--
	<script type="text/javascript">
		google_ad_client = "pub-8676956632365960";
		/* 234x60, created 02/06/09 */
		google_ad_slot = "4255960768";
		google_ad_width = 234;
		google_ad_height = 60;
	</script>
	<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
-->
	<?php 
		/*
		$img = '/image/adthird/' . $advert['image_id'];
		
		echo '<a href="' . $img . '" title="' . $advert['alt'] . '" />';
		echo '<img src="' . $img . '" alt="' . $advert['alt'] . '" />';
		echo '</a>';
		*/
		
		$img = 'http://www.theyorker.co.uk/photos/full/4426';
		
		echo '<a href="http://graduates.teachfirst.org.uk" title="Teach First" />';
		echo '<img src="' . $img . '" style="width: 234px; height: 60px;" alt="Teach First" />';
		echo '</a>';	
	?>
	
</div>