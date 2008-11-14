<?php
/**
 * @file views/office/moderator/comments.php
 * @brief View for lists of comments in moderator section of office.
 *
 * @param $Title string Title for page.
 */
?>

<div id="RightColumn">
	<?php
	if (NULL !== $Description) {
		?>
		<h2 class="first">Page Information</h2>
		<div class="Entry">
			<?php echo($Description); ?>
		</div>
		<?php
	}
	?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo(xml_escape($Title)); ?></h2>
		<?php
		// Show message that no comments were found.
		if ($Comments->EmptyComments()) {
			?>
			<div>
				<h3>No <?php echo(xml_escape($Title)); ?></h3>
				<p>No comments match the search criteria.</p>
			</div>
			<?php
		}
		?>
		<div>
			<a href="<?php echo(site_url('office/moderator')); ?>">Return to Moderator Control Panel</a>
		</div>
	</div>
	
	<?php
	$Comments->Load();
	?>
</div>