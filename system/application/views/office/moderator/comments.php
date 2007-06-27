<?php
/**
 * @file views/office/moderator/comments.php
 * @brief View for lists of comments in moderator section of office.
 */
?>

<div class="RightToolbar">
	<?php
	if (NULL !== $Description) {
		?>
		<h4>What's this?</h4>
		<?php
		echo($Description);
	}
	?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo($Title); ?></h2>
		<?php
		// Show message that no comments were found.
		if ($Comments->EmptyComments()) {
			?>
			<div>
				<h3>No <?php echo($Title); ?></h3>
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