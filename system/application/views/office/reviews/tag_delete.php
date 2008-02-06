<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
	<h2>confirm delete</h2>
		<p>Are you sure you want to delete this tag? This cannot be undone!</p>
		<p>
		<b>Name</b> : <?php echo(xml_escape($tag['tag_name'])); ?><br>
		<b>Tag Group</b> : <?php echo(xml_escape($tag['tag_group_name'])); ?><br>
		<b>Section</b> : <?php echo(xml_escape($tag['content_type_name'])); ?><br>
		</p>
		<p><a href='/office/reviewtags/delete/<?php echo($tag['tag_id']); ?>/confirm'>Confirm Delete</a>&nbsp;&nbsp;<a href='/office/reviewtags'>Go Back</a></p>
	</div>
</div>
