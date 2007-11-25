<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
	<h2>confirm delete</h2>
		<p>Are you sure you want to delete this tag group? This cannot be undone!</p>
		<p>
		<b>Name</b> : <?php echo $tag_group['tag_group_name']; ?><br>
		<b>Tags</b> : Empty<br>
		<b>Section</b> : <?php echo $tag_group['content_type_name']; ?><br>
		</p>
		<p><a href='/office/reviewtags/deletegroup/<?php echo $tag_group['tag_group_id']; ?>/confirm'>Confirm Delete</a>&nbsp;&nbsp;<a href='/office/reviewtags'>Go Back</a></p>
	</div>
</div>
