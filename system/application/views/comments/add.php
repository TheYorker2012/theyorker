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
 * @param $ShowCancelButton bool Whether to show a cancelation option.
 * @param $AlreadyExists bool Whether the comment already exists.
 * @param $WarningMessageXml string Warning message.
 */

?>
<a id="comment_preview"></a>
<div class="BlueBox" id="SectionCommentAdd">
	<?php
	if (!$LoggedIn) {
		echo('<h2>Add Comment</h2>');
		echo('<p>You must <a href="'.$LoginUrl.'">log in</a> to submit a comment</p>');
	} else { ?>
		<script type="text/javascript" src="/javascript/wikitoolbar.js"></script>
		<script type="text/javascript">
		// <![CDATA[
		function getObject(obj) {
			if (document.getElementById) {
				obj = document.getElementById(obj);
			} else if (document.all) {
				obj = document.all.item(obj);
			} else {
				obj = null;
			}
			return obj;
		}
		
		function moveObject(obj,e,offset_x,offset_y) {
			// step 1
			var tempX = 0;
			var tempY = 0;
			var objHolder = obj;
		
			// step 2
			obj = getObject(obj);
			if (obj==null) return;
		
			// step 3
			if (document.all) {
				tempX = event.clientX + document.body.scrollLeft;
				tempY = event.clientY + document.body.scrollTop;
			} else {
				tempX = e.pageX;
				tempY = e.pageY;
			}
		
			// step 4
			if (tempX < 0){ tempX = 0 }
			if (tempY < 0){ tempY = 0 }
		
			// step 5
			obj.style.top  = (tempY - offset_y) + 'px';
			obj.style.left = (tempX - offset_x) + 'px';
		
			// step 6
			displayObject(objHolder, true);
		
			// step 7
			return false;
		}
		
		function displayObject(obj,show) {
			obj = getObject(obj);
			if (obj==null) return;
			obj.style.display = show ? 'block' : 'none';
			obj.style.visibility = show ? 'visible' : 'hidden';
			return false;
		}

		function insert_smiley(smiley) {
			insertTags(smiley, '', '', 'CommentAddContent');
		}
		// ]]>
		</script>
		<?php
		// Show the preview
		if (NULL !== $Preview) {
			echo('<h2>Comment Preview:</h2>');
			$CI = &get_instance();
			echo('<div id="CommentPreview">');
			$CI->load->view('comments/comment', array(
				'Comment' => $Preview,
				'ListNumber' => 14,
			));
			echo '</div>';
		}
		?>
		<h2><?php echo($AlreadyExists?'Edit':'Add'); ?> Comment</h2>
		<form class="form" id="CommentAdd" method="post" action="<?php echo($FormTarget); ?>">
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

				<div id="SmileySelect" style="position:absolute;width:225px;z-index:0;display:none;border:1px #999 solid;background-color:#fff;top:0px;left:0px;">
					<?php echo($SmileyTable); ?>
					<input type="button" class="button" name="Close" value="Close" onclick="return displayObject('SmileySelect',false);" />
					<div style="clear:both"></div>
				</div>

				<?php if ($Thread['allow_anonymous_comments']) { ?>
					<label for="CommentAddAnonymous" style="width:35%;">Post Anonymously:</label>
					<input type="checkbox" name="CommentAddAnonymous" id="CommentAddAnonymous"<?php if ($DefaultAnonymous) echo(' checked="checked"'); ?> />
				<?php } ?>

				<textarea name="CommentAddContent" id="CommentAddContent" cols="40" rows="4"><?php echo(xml_escape($DefaultContent)); ?></textarea>

				<label style="text-align:center">
					<a href="#" onclick="return moveObject('SmileySelect',event,10,10);">Insert Smiley</a>
					<?php echo($WarningMessageXml); ?>
				</label>

				<?php if ($ShowCancelButton) { ?>
				<input type="submit" class="button" name="CommentAddCancel"  value="Cancel" />
				<?php } ?>
				<input type="submit" class="button" name="CommentAddPreview" value="Preview" />
				<input type="submit" class="button" name="CommentAddSubmit" value="Submit" />
			</fieldset>
		</form>
	<?php } ?>
</div>