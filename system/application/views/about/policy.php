<?php
foreach ($policydata as $policy) {
?>
<a name='<?php echo $policy['shorttitle']; ?>'></a>
<h2><?php echo $policy['title']; ?></h2>
<div class='columnPhoto'>
	<a href='<?php echo $policy['image']; ?>'><img src='<?php echo $policy['image']; ?>' alt='<?php echo $policy['image_description']; ?>'/></a>
	<a href='<?php echo $policy['image']; ?>'><small><?php echo $policy['image_description']; ?></small></a>
</div>
<div class='columnText'>
	<p><?php echo $policy['blurb']; ?></p>
</div>
<div class="clear">&nbsp;</div>
<?php
}
?>
<div align='center' id='related_pages'>
Related pages : <a href='/policy/'>About Us</a> | <a href='/policy/'>Our Policy</a> | <a href='/charity/'>Sponsored Charity</a>
</div>