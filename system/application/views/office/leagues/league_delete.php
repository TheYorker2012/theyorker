<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
	<h2>confirm delete</h2>
		<p>Are you sure you want to delete this league? This cannot be undone!</p>
		<p>
		<b>Name</b> : <?php echo $league['name']; ?><br>
		<b>League Type</b> : <?php echo $league['section_name']; ?><br>
		</p>
		<p><a href='/office/leagues/delete/<?php echo $league['id']; ?>/confirm'>Confirm Delete</a>&nbsp;&nbsp;<a href='/office/leagues'>Go Back</a></p>
	</div>
</div>