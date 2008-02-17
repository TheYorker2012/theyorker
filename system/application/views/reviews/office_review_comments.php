<div class='RightToolbar'>
	<h4 class="first">Page Information</h4>
	<?php echo($page_information); ?>
</div>
<div class="MainColumn">
	<div class="blue_box">
	<?php
		$comments->Load();
	?>
	<a href="/office/reviewlist/<?php echo($context_type); ?>">Back to the attention list</a>
	</div>
</div>