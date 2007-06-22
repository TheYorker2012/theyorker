<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<div class="Entry">
		<?php
			echo($maintext);
		?>
	</div>
</div>

<div id="MainColumn">
	<?php
		$culprit->Load();
	?>

	<div class="BlueBox" style="border:0">
		<form class="form" method="post" action="<?php echo($target); ?>" name="comment_report">
			<fieldset>
				<input class="button" type="submit" name="comment_report_cancel" value="Cancel" />
				<input class="button" type="submit" name="comment_report_confirm" value="Confirm Abuse" />
			</fieldset>
		</form>
	</div>
</div>