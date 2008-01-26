<?php
/**
 * @file views/comments/confirm.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Confirmation form.
 * @param $MainText string Information xhtml.
 * @param $Culprit  FramesView preview of thing to confirm.
 * @param $Action   string Action to confirm.
 * @param $Target   string URL target of the form.
 */
?>
<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<div class="Entry">
		<?php
			echo($MainText);
		?>
	</div>
</div>

<div id="MainColumn">
	<?php
		$Culprit->Load();
	?>
	<div class="BlueBox">
		<form class="form" method="post" action="<?php echo($Target); ?>" name="comment_confirm">
			<fieldset>
				<input class="button" type="submit" name="comment_confirm_cancel" value="Cancel" />
				<input class="button" type="submit" name="comment_confirm_confirm" value="<?php echo($Action); ?>" />
			</fieldset>
		</form>
	</div>
</div>
