<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<?php
		$comments->Load();
	?>
	<a href="/office/reviewlist/<?php echo($context_type); ?>">Back to the attention list</a>
</div>