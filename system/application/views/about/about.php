<?php
foreach ($textblocks as $textblock) {
?>
<a name='<?php echo $textblock['shorttitle']; ?>'></a>
<div class='columnPhoto' style='padding: 25px 0px 0px 0px;'>
	<?php echo $textblock['image']; ?>
</div>
<div class='columnText'>
	<p><?php echo $textblock['blurb']; ?></p>
</div>
<div class="clear">&nbsp;</div>
<?php
}
?>
<div align='center' id='related_pages'>
Related pages : <a href='/about/'>About Us</a> | <a href='/policy/'>Our Policy</a> | <a href='/charity/'>Sponsored Charity</a>
</div>