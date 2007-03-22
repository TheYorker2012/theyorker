<div id="<?php echo $style; ?>">
	<ul>
<?php
	/// @note Echo in reverse because right aligned.
	foreach (array_reverse($items) as $key => $item) {
		$link_attributes = 'href="'.$item['link'].'"';
		if ($key === $selected) {
			$link_attributes .= ' class="current"';
		}
?>
		<li><a id='navbar_<?php echo $key; ?>' <?php echo $link_attributes; ?>><?php echo $item['title']; ?></a></li>
		<li><div class="thin">&nbsp;</div></li>
<?php
		}
?>
	</UL>
</DIV>