<ul id="<?php echo($style); ?>">
<?php
foreach (array_reverse($items) as $key => $item) {
?>
	<li id="Navbar_<?php echo($key); ?>"<?php if ($key === $selected) { echo(' class="current"'); } ?>>
		<a href="<?php echo(xml_escape($item['link'])); ?>"><?php echo(xml_escape($item['title'])); ?></a>
	</li>
<?php
}
?>
</ul>
