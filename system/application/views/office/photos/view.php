<?php
function printInput ($title, $name,$type,$value,$section,$access,$user_level)
{
	$name = xml_escape($name);
	$title = xml_escape($title);
	$value = xml_escape($value);
	if ($type != 'submit') {
	   echo('<label for="'.$name.'">'.$title.':</label>');
	}
	if ($access[$section][$user_level]) {
		switch ($type) {
			case 'textarea':
				echo('<textarea name="'.$name.'" id="'.$name.'" cols="25" rows="5">'.$value.'</textarea>');
				break;
			case 'submit':
				echo('<input type="'.$type.'" name="'.$name.'" id="'.$name.'" value="'.$value.'" class="button" />');
				break;
			default:
				echo('<input type="'.$type.'" name="'.$name.'" id="'.$name.'" value="'.$value.'" size="30" />');
				break;
		}
	} else {
		if ($type != 'submit') {
			echo('<div id="'.$name.'" style="float:left;margin:5px 10px;">'.$value.'</div>');
		}
	}
	echo('<br />');
}
?>
<style type='text/css'>
#proposed_photos .photo_item {
	float: left;
	font-size: x-small;
	text-align: center;
	margin-bottom: 5px;
}
#proposed_photos .photo_item .delete_icon {
	float: right;
}
</style>

<div id="RightColumn">
	<h2 class="first">Quick Links</h2>
	<ul>
		<?php if ($request_editable) { ?>
			<li><a href='/office/gallery/'>Select photo from gallery</a></li>
		<?php } ?>
		<?php if ($access['ready'][$user_level]) {
			if ($request_editable) { ?>
				<li><a href='/office/photos/view/<?php echo($id); ?>/ready'>Flag as ready</a></li>
			<?php } elseif (!$request_finished) { ?>
				<li><a href='/office/photos/view/<?php echo($id); ?>/unready'>Remove ready flag</a></li>
			<?php } ?>
		<?php } ?>
		<?php if (($access['cancel'][$user_level]) && (!$request_finished)) { ?>
			<li><a href='/office/photos/view/<?php echo($id); ?>/cancel'>Cancel Request</a></li>
		<?php } ?>
	</ul>

	<h2>What now?</h2>
	<?php echo($help_text); ?>
</div>

<div id="MainColumn">
	<form id='edit_request' action='/office/photos/view/<?php echo($id); ?>' method='post' class='form'>
		<div class='BlueBox'>
			<h2>details</h2>
			<fieldset>
				<?php
				if ($status == 'completed') {
					printInput('Caption','r_title','text',$title,'details',$access,$user_level);
					printInput('ALT Text','r_brief','text',$description,'details',$access,$user_level);
				} else {
					printInput('Title','r_title','text',$title,'details',$access,$user_level);
					printInput('Description','r_brief','textarea',$description,'details',$access,$user_level);
				} ?>

				<?php printInput('','r_details','submit','Update','details',$access,$user_level); ?>

				<label for="r_article">For Article:</label>
				<div id="r_article" style="float: left; margin: 5px 10px;">
					<a href="/office/news/<?php echo($article_id); ?>">
						<?php echo(xml_escape($article_title)); ?>
					</a>
				</div>
				<br />
				<label for='r_date'>Date Requested:</label>
				<div id='r_date' style='float: left; margin: 5px 10px;'><?php echo(date('d/m/y @ H:i',$time)); ?></div>
				<br />
				<label for='r_user'>Requested By:</label>
				<div id='r_user' style='float: left; margin: 5px 10px;'><?php echo(xml_escape($reporter_name)); ?></div>
				<br />
				<label for='r_assigned'>Assigned to:</label>
				<div id='r_assigned' style='float: left; margin: 5px 10px;'>
					<?php 
					if ($assigned_status == 'unassigned') {
						echo('Unassigned');
					} else {
						echo(xml_escape($assigned_name) . ' (' . xml_escape($assigned_status) . ')');
					}
					?>
				</div>
				<?php
				$assign_text = '';
				$other_input_xml = '';

				$select_users_xml = '<br /><label for="r_assignuser">&nbsp;</label>
				<select name="r_assignuser" id="r_assignuser" size="">';
				foreach ($photographers as $user) {
					$select_users_xml .= '	<option value="'.$user['id'].'">'.xml_escape($user['name']).'</option>';
				}
				$select_users_xml .= '	</select>';

				if ($status == 'unassigned') {
					if ($user_level == 'editor') {
						$assign_text = 'Assign';
						$other_input_xml = $select_users_xml;
					} else {
						$assign_text = 'Assign Me';
					}
				} elseif ($status == 'assigned') {
					if ($user_level == 'photographer') {
						$assign_text = 'Unassign Me';
						if ($assigned_status == 'requested') {
							$assign_text = 'Accept';
							$other_input_xml = '<br /><input type="submit" name="r_decline" value="Decline" class="button" />';
						}
					} elseif ($user_level == 'editor') {
						$assign_text = 'Unassign';
					}
				}
				echo($other_input_xml);
				if ($assign_text != '') { 
				?>
				<input type="submit" name="r_assign" value="<?php echo(xml_escape($assign_text)); ?>" class="button" />
				<?php
				}
				?>
				<br />
				<?php if ($editor_id !== NULL) { ?>
					<label for="r_reviewed">Reviewed by:</label>
					<div id='r_reviewed' style='float: left; margin: 5px 10px;'><?php echo(xml_escape($editor_name)); ?></div>
					<br />
				<?php } ?>
			</fieldset>
		</div>

		<?php if (isset($suggestion)) { ?>

		<div class="BlueBox">
			<h2>Your Suggestions</h2>
			<fieldset>
				<?php for($i=0; $i < count($suggestion); $i++) { ?>
					<h3><?php echo($i+1); ?>:</h3>
					<label for="imgid_<?php echo($i); ?>_img">Photo</label>
					<?php echo($this->image->getThumb($suggestion[$i], 'medium')); ?>
					<br />
					<label for="imgid_<?php echo($i); ?>_comment">Comment:</label>
					<textarea name="imgid_<?php echo($i); ?>_comment"></textarea>
					<br />
					<label for="imgid_<?php echo($i); ?>_allow">Suggest:</label>
					<input name="imgid_<?php echo($i); ?>_allow" type="checkbox" value="y" checked="checked" />
					<input type="hidden" name="imgid_<?php echo $i?>_number" value="<?php echo($suggestion[$i]); ?>" />
				<?php } ?>
				<input type="hidden" name="imgid_number" value="<?php echo(count($suggestion))?>" />
				<input type="submit" name="r_suggest" value="Suggest" class="button" />
				<br />
			</fieldset>
		</div>
		<?php } else { ?>
	</form>
	
	<form id='edit_photos' action='/office/photos/view/<?php echo($id); ?>' method='post' class='form'>
		<?php if ($status == 'completed') { ?>
		<div class="BlueBox">
			<h2>chosen photo</h2>
			<a href="/office/gallery/show/<?php echo($chosen_photo); ?>">
				<?php echo($this->image->getThumb($chosen_photo, 'medium')); ?>
			</a><br />
			<?php echo(xml_escape($title)); ?>
		</div>
		<?php } ?>
		<div class="BlueBox">
			<h2>suggested photos</h2>
			<div id="proposed_photos">
				<?php
				$photo_width = '20';
				$photo_size = 'small';
				if ($status == 'ready') {
					$photo_width = '50';
					$photo_size = 'medium';
				}
				foreach ($photos as $photo) {
					$photo['tag'] = $this->image->getThumb($photo['id'], $photo_size);

					echo('				<div class="photo_item" style="width: '.$photo_width.'%;">');
					echo('					<a href="/office/gallery/show/' . $photo['id'] . '">'.$photo['tag'].'</a><br />');
					if (($request_editable) && (($user_level == 'editor') || ($photo['user_id'] == $this->user_auth->entityId))) {
						echo('					<a href=""><img src="/images/prototype/news/delete.gif" alt="Delete" title="Delete" class="delete_icon" /></a>');
					}
					echo('					' . xml_escape($photo['user_name']) . '<br />');
					echo('					' . date('d/m/y @ H:i',$photo['time']) . '');
					if ($status == 'ready') {
						echo('					<br /><a href="/office/photos/view/'.$id.'/select/'.$photo['id'].'">Select this Photo</a>');
					}
					echo('            </div>');
				} ?>
			</div>
			<div style="clear:both;">&nbsp;</div>
		</div>
	</form>
	<div>
		<?php // Display comments if thread exists
		if ((isset($comments)) && (NULL !== $comments)) {
			$comments->Load();
		} ?>
	</div>
<?php } ?>
