<DIV id="<?php echo $style; ?>">
	<UL>
<?php
	foreach ($items as $key => $item) {
		$link_attributes = 'href="'.$item['link'].'"';
		if ($key === $selected) {
			$link_attributes .= ' class="current"';
		}
?>
		<LI><A <?php echo $link_attributes; ?>><?php echo $item['title']; ?></A></LI>
		<LI><DIV class="thin">&nbsp;</DIV></LI>
<?php
		}
?>
	</UL>
</DIV>