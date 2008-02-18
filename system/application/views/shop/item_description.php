<?php if($item['event_date'] > 0) { ?>
<div class="Date"><?php echo(xml_escape($item['event_date_string'])); ?></div>
<?php } ?>
<div style="overflow: hidden">
	<a href="/shop/item/<?php echo(xml_escape($item['id'])); ?>"><img class="Left" src="http://ecx.images-amazon.com/images/I/31qa-xPHWNL._AA115_.jpg" /></a>
	<p style="margin-top: 0px"><?php echo(xml_escape($item['blurb'])); ?></p>
	<strong>Price: </strong> <?php echo(xml_escape($item['price_string'])); ?><br /><strong>Availability: </strong> Limited<br /><a href="/shop/item/1">[more info]</a>
</div>

