<?php

/**
 * @file views/comments/add.php
 * @brief View for comment adder.
 *
 * @param $Thread array_thread Thread information with:
 *	- 'allow_anonymous_comments' bool Whether to allow comment to be anonymous.
 * @param $LoggedIn bool Whether a user is logged in.
 * @param $SmileyTable
 * @param $FormTarget string Target of form.
 * @param $Identities array[int=>string] Array of identities the user can use
 *	to post the comment.
 * @param $Preview comment_array Preview array.
 * @param $DefaultIdentity int Index of default identity.
 * @param $DefaultAnonymous bool Whether anonymous by default.
 * @param $DefaultContent string Default wikitext.
 */

?>
<div class="BlueBox" id="SectionCommentAdd">
	<?php
	if (!$LoggedIn) {
		echo('<h2>Add Comment</h2>');
		echo('<p>You must <a href="'.$LoginUrl.'">log in</a> to submit a comment</p>');
	} else {
		?>
		<script type="text/javascript">
			function insert_smiley(smiley)
			{
				<?php
				/**
				* @todo Add smileys intelligently. e.g. if blank and add, has space at
				*	the beginning which is interpretted as preformatting in wikitext.
				*/ 
				?>
				document.CommentAdd.CommentAddContent.value += " " + smiley;
			}
		</script>
		<?php
		// Show the preview
		if (NULL !== $Preview) {
			echo '<h2>Comment Preview:</h2>';
			$CI = &get_instance();
			echo '<div id="CommentPreview">';
			$CI->load->view('comments/comment', array(
				'Comment' => $Preview,
				'ListNumber' => 14,
			));
			echo '</div>';
		}
		?>
		<h2>Add Comment</h2>
		<form class="form" name="CommentAdd" method="post" action="<?php echo $FormTarget; ?>">
			<fieldset>
				<?php /*
				<label for="CommentAddIdentity">Identity</label>
				<select name="CommentAddIdentity">
				<?php
				foreach ($Identities as $id => $identity) {
					echo('<option value="'.$id.'" '.($DefaultIdentity==$id ? 'selected="selected"' : '').'>'.
						$identity.'</option>');
				}
				?>
				</select> */ ?>
				<?php if ($Thread['allow_anonymous_comments']) { ?>
					<label for="CommentAddAnonymous">Anonymous</label>
					<input type="checkbox" name="CommentAddAnonymous"<?php if ($DefaultAnonymous) echo ' checked="checked"'; ?> />
				<?php } ?>
				<label for="CommentAddContent">Content (wikitext)</label>
				<textarea name="CommentAddContent" cols="40" rows="4"><?php echo $DefaultContent; ?></textarea>
			</fieldset>
				<?php echo $SmileyTable; ?>
			<fieldset>
				<input type="submit" class="button" name="CommentAddPreview" value="Preview" />
				<input type="submit" class="button" name="CommentAddSubmit" value="Submit" />
			</fieldset>
		</form>
	<?php } ?>
</div>