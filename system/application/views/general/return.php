<?php
/**
 * @file views/general/return.php
 * @brief Simple view for displaying an error and back button.
 */
?>
<div class="BlueBox">
	<img align="left" src="/images/prototype/homepage/error.png" alt="error" width="30" height="30" />
	<h2><?php echo($Title); ?></h2>
	<?php echo($Description); ?>
	<?php echo(HtmlButtonLink($Target,$Caption)); ?>
</div>
