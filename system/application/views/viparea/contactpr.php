<?php

/**
 * @file view/viparea/contactpr.php
 * @brief Form for contacting PR rep.
 */

?>

<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<div class="Entry">
		<?php echo $main_text; ?>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>your pr rep</h2>
<?php 
	if ($rep['has_rep'] == true) {
?>
		<p>
			Your rep is: <?php echo($rep['firstname'].' '.$rep['surname']); ?><br />
		</p>
<?php 
	}
	else {
?>
		<p>
			You have no dedicated rep, so our pr officers <?php echo(xml_escape($rep['name'])); ?> are looking after you.
		</p>
<?php 
	}
?>
	</div>
	<div class="BlueBox">
		<h2>email your pr rep</h2>
<?php
	echo(	'<form class="form" action="'.$message_pr_target.'" method="post">'."\n");
?>
			<fieldset>
				<label for="a_subject">Subject: </label>
<?php
	echo('				<input type="text" id="a_subject" name="a_subject" style="width: 357px" value="'.$subject.'" />'."\n");
?>
				<label for="a_content">Message:</label>
<?php
	echo('				<textarea name="a_content" id="a_content" rows="10" cols="42">'.$content.'</textarea>'."\n");
?>
			</fieldset>
			<fieldset>
				<input type="submit" name="submit_save_advert" value="Send Email" style="float: right;" />
			</fieldset>
		</form>
	</div>
</div>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>