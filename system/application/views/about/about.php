<?php
foreach ($textblocks as $textblock) {
?>
<a name='<?php echo $textblock['shorttitle']; ?>'></a>
<div class='columnPhoto' style='padding: 25px 0px 0px 0px;'>
	<a href='<?php echo $textblock['image']; ?>'><img src='<?php echo $textblock['image']; ?>' width='220px'/></a>
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