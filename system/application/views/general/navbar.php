<ul id="<?php echo($style); ?>">
<?php
foreach (array_reverse($items) as $key => $item) {
?>
	<li<?php if ($key === $selected) { echo(' class="current"'); } ?>>
		<a href="<?php echo($item['link']); ?>"><?php echo $item['title']; ?></a>
	</li>
<?php
}
?>
</ul>
