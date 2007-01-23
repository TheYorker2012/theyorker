<?php
foreach ($aboutdata as $about) {
?>
<h2><?php echo $about['title']; ?></h2>
<div class='columnPhoto'>
	<a href='<?php echo $about['image']; ?>'><img src='<?php echo $about['image']; ?>' alt='<?php echo $about['image_description']; ?>'/></a>
	<a href='<?php echo $about['image']; ?>'><h5><?php echo $about['image_description']; ?></h5></a>
</div>
<div class='columnText'>
	<p><?php echo $about['blurb']; ?></p>
</div>
<div class="clear">&nbsp;</div>
<?php
}
?>
<div align='center' id='related_pages'>
Related pages : <a href='/about/'>About Us</a> | <a href='/policy/'>Our Policy</a> | <a href='/charity/'>Sponsored Charity</a>
</div>