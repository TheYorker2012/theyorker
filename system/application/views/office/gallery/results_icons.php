<script type="text/javascript">
function enlarge (e, photo) {
	preview = document.getElementById('preview');
	preview_img = document.getElementById('preview_img');
	if (preview_img.src != photo.src.replace('small','medium')) {
		preview_img.src = photo.src.replace('small','medium');
		preview_img.alt = photo.alt;
		preview_img.title = photo.title;
	}
	var posx = 0;
	var posy = 0;
	var offset = 5;
	if (!e) var e = window.event;
	if (e.pageX || e.pageY) 	{
		posx = e.pageX;
		posy = e.pageY;
	}
	else if (e.clientX || e.clientY) 	{
		posx = e.clientX + document.body.scrollLeft
			+ document.documentElement.scrollLeft;
		posy = e.clientY + document.body.scrollTop
			+ document.documentElement.scrollTop;
	}
	posx += offset;
	posy += offset;
	preview.style.top = posy + 'px';
	preview.style.left = posx + 'px';
	preview.style.display = 'block';
}
function shrink () {
	preview = document.getElementById('preview');
	preview.style.display = 'none';
}
</script>

<div id="preview">
	<img id="preview_img" src="" alt="alt text" title="title text" />
</div>

<?php foreach ($photos as $thumbnail) { ?>
	<div class="gallery_thumb_view">
		<a href="/office/gallery/show/<?php echo($thumbnail->photo_id); ?>">
			<img src="/photos/small/<?php echo($thumbnail->photo_id); ?>" alt="<?php echo(xml_escape($thumbnail->photo_title)); ?>" title="<?php echo(xml_escape($thumbnail->photo_title)); ?>" onmousemove="enlarge(event, this);" onmouseout="shrink();" />
		</a>
	</div>
<?php } ?>