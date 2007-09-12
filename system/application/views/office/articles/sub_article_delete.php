<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
	<h2>confirm delete</h2>
		<p>Are you sure you want to delete this sub article type? This cannot be undone!</p>
		<p>
		<b>Name</b> : <?php echo $article_type['name']; ?><br>
		<b>Parent</b> : <?php echo $parent_article_type['name']; ?><br>
		<b>Children</b> : None<br>
		<b>Articles</b> : None<br>
		</p>
		<p><a href='/office/articletypes/delete/<?php echo $article_type['id']; ?>/confirm'>Confirm Delete</a>&nbsp;&nbsp;<a href='/office/articletypes'>Go Back</a></p>
	</div>
</div>