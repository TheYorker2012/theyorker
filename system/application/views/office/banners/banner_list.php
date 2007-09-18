<div class='RightToolbar'>
	<h4 class="first">Information</h4>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
	<h4>Actions</h4>
	<div class="Entry">
		<ul>
			<li><a href="/office/banners/upload">Upload new banner(s)</a></li>
		</ul>
	</div>
</div>

<div class='blue_box'>
	<h2>homepage banners</h2>
	<table>
	<tr>
		<th>Banner</th>
		<th>Schedule</th>
		<th></th>
	</tr>
		<?php foreach($banners as $banner) { ?>
	<tr>
	<td><?php echo $this->image->getImage($banner['banner_id'], 'banner', array('width' => '294', 'height' => '75')); ?></td>
	<td><?php echo (isset($banner['banner_last_displayed_timestamp']) ? $banner['banner_last_displayed_timestamp'] : 'In Pool'); ?></td>
	<td><a href="/office/banners/edit/<?php echo $banner['banner_id']; ?>" title="Edit this banner">Edit</a></td>
	</tr>
	<?php } ?>
	</table>

</div>