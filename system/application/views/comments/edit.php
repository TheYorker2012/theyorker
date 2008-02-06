<?php

/**
 * @file views/comments/edit.php
 * @brief View for comment editor.
 *
 * @param $SmileyTable
 * @param $FormTarget string Target of form.
 * @param $Preview comment_array Preview array.
 * @param $DefaultAnonymous bool Whether anonymous by default.
 * @param $DefaultContent string Default wikitext.
 */

?>
<a id="comment_preview"></a>
<div class="BlueBox" id="SectionCommentEdit">
	<script type="text/javascript" src="/javascript/wikitoolbar.js"></script>
	<script type="text/javascript">
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
		insertTags(smiley, '', '', 'CommentEditContent');
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
	<h2>Edit Comment</h2>
	<form class="form" id="CommentEdit" method="post" action="<?php echo($FormTarget); ?>">
		<fieldset>
			<div id="SmileySelect" style="position:absolute;width:225px;z-index:0;display:none;border:1px #999 solid;background-color:#fff;top:0px;left:0px;">
				<?php echo($SmileyTable); ?>
				<input type="button" class="button" name="Close" value="Close" onclick="return displayObject('SmileySelect',false);" />
				<div style="clear:both"></div>
			</div>

			<textarea name="CommentEditContent" id="CommentEditContent" cols="40" rows="4"><?php echo(xml_escape($DefaultContent)); ?></textarea>

			<label><a href="#" onclick="return moveObject('SmileySelect',event,10,10);">Insert Smiley</a></label>

			<input type="submit" class="button" name="CommentEditCancel"  value="Cancel" />
			<input type="submit" class="button" name="CommentEditPreview" value="Preview" />
			<input type="submit" class="button" name="CommentEditSubmit"  value="Submit" />
		</fieldset>
	</form>
</div>