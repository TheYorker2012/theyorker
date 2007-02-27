<ul id="<?php echo($style); ?>">
<?php
foreach ($items as $key => $item) {
?>
	<li<?php if ($key === $selected) { echo(' class="current"'); } ?>>
		<a href="<?php echo($item['link']); ?>"><?php echo $item['title']; ?></a>
	</li>
<?php
}
?>
</ul>

<!-- Hack before page content is fixed to not overlap the navbar -->
&nbsp;
