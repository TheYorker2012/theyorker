<div class="BlueBox" id="photo_search">
	<div class="search_options">
		<a href="<?php echo(site_url('office/gallery/upload')); ?>">
			<img src="/images/icons/upload_photo.png" alt="Upload Photo" />
		</a>
		<a href="<?php echo(site_url('office/gallery/mass_upload')); ?>">
			<img src="/images/icons/button_massupload.png" alt="Mass Upload Photos" />
		</a>
	</div>
	<h2 style="float:left">search by...</h2>
	<form method="post" action="<?php echo(site_url('office/gallery')); ?>">
		<fieldset>
			<input type="text" name="search" id="search" value="<?php if (isset($_SESSION['gallery_search']['term'])) echo($_SESSION['gallery_search']['term']); ?>" size="35" />
			<input type="submit" name="search_button" value="Go" />
		</fieldset>
	</form>
	<div class="clear"></div>
</div>

<div class="BlueBox">
	<div class="search_options">
		<a href="/office/gallery/change_view/icons">
			<img src="/images/icons/view_icons.png" alt="Icon View" title="Icon View" />
		</a>
		<a href="/office/gallery/change_view/thumbs">
			<img src="/images/icons/view_medium.png" alt="Thumbnail View" title="Thumbnail View" />
		</a>
		<a href="/office/gallery/change_view/details">
			<img src="/images/icons/view_detail.png" alt="Detail View" title="Detail View" />
		</a>
	</div>

	<h2><?php echo($title); ?></h2>
	<?php
		// Load a subview.
		$content[0]->Load();
	?>
	<div class="clear"></div>
</div>
<?php echo($pageNumbers); ?>
<div>Viewing <?php echo((($total == 0) ? '0' : ($offset + 1)) . ' - ' . ((($offset + $perpage) <= $total) ? ($offset + $perpage) : $total) . ' of ' . $total . ' photos'); ?></div>

<script type="text/javascript">
onLoadFunctions.push(function () { document.getElementById('search').focus(); });
</script>