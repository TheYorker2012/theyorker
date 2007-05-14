<?php
function printInput ($title, $name,$type,$value,$section,$access,$user_level)
{
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
		echo('<div id="'.$name.'" style="float:left;margin:5px 10px;">'.$value.'</div>');
	}
	echo('<br />');
}
?>

	<style type='text/css'>
	#proposed_photos .photo_item {
		float: left;
		width: 20%;
		font-size: x-small;
		text-align: center;
		margin-bottom: 5px;
	}
	#proposed_photos .photo_item .delete_icon {
		float: right;
	}
	</style>

	<div class='RightToolbar'>
	    <h4>Reporter</h4>
	   	<ul>
				<li><a href='#'>Upload new photo</a></li>
				<li><a href='#'>Select photo from gallery</a></li>
				<li><a href='#'>Cancel Request</a></li>
			</ul>
			<h4>Photographer</h4>
			<ul>
				<li><a href='#'>Upload new photo</a></li>
				<li><a href='#'>Select photo from gallery</a></li>
				<li><a href='#'>Flag for review</a></li>
			</ul>
			<h4>Editor</h4>
			<ul>
				<li><a href='#'>Flag for review</a></li>
				<li><a href='#'>Cancel Request</a></li>
			</ul>
	</div>

	<form name='edit_request' id='edit_request' action='/office/photos/view/<?php echo($id); ?>' method='post' class='form'>
		<div class='blue_box'>
			<h2>details</h2>
			<fieldset>

				<?php printInput('Title','r_title','text',$title,'details',$access,$user_level); ?>
				<?php printInput('Description','r_brief','textarea',$description,'details',$access,$user_level); ?>
				<?php printInput('','r_details','submit','Edit','details',$access,$user_level); ?>

				<label for="r_article">For Article:</label>
				<div id="r_article" style="float: left; margin: 5px 10px;">
					<a href="/office/news/<?php echo($article_id); ?>" target="_blank">
						<?php echo($article_title); ?>
					</a>
				</div>
				<br />
				<label for='r_date'>Date Requested:</label>
				<div id='r_date' style='float: left; margin: 5px 10px;'><?php echo(date('d/m/y @ H:i',$time)); ?></div>
				<br />
				<label for='r_user'>Requested By:</label>
				<div id='r_user' style='float: left; margin: 5px 10px;'><?php echo($reporter_name); ?></div>
				<br />
				<label for='r_assigned'>Assigned to:</label>
				<div id='r_assigned' style='float: left; margin: 5px 10px;'>
<?php if ($assigned_status == 'unassigned') {
	echo('Unassigned');
} else {
	echo($assigned_name . ' (' . $assigned_status . ')');
} ?>
				</div>
<?php
$assign_text = '';
$other_input = '';

$select_users = '<select name="r_assignuser" id="r_assignuser" size="">';
foreach ($photographers as $user) {
	$select_users .= '	<option value='.$user['id'].'>'.$user['name'].'</option>';
}
$select_users .= '	</select>';

if ($status == 'unassigned') {
	if ($user_level == 'editor') {
		$assign_text = 'Assign';
		$other_input = $select_users;
	} else {
		$assign_text = 'Assign Me';
	}
} elseif ($status == 'assigned') {
	if ($user_level == 'editor') {
		$assign_text = 'Unassign';
	} elseif ($user_level == 'photographer') {
		$assign_text = 'Unassign Me';
		if ($assigned_status == 'requested') {
			$assign_text = 'Accept';
			$other_input = '<input type="submit" name="r_decline" value="Decline" class="button" />';
		}
	}
}
?>
				<?php echo($other_input); ?>
				<?php if ($assign_text != '') { ?><input type="submit" name="r_assign" value="<?php echo($assign_text); ?>" class="button" /><?php } ?>
				<br />
<?php if ($editor_id !== NULL) { ?>
				<label for="r_reviewed">Reviewed by:</label>
				<div id='r_reviewed' style='float: left; margin: 5px 10px;'><?php echo($editor_name); ?></div>
				<br />
<?php } ?>
			</fieldset>
		</div>

<?php if (isset($suggestion)) { ?>

		<div class="grey_box">
			<h2>Your Suggestions</h2>
			<fieldset>
				<?php for($i=0; $i < count($suggestion); $i++) { ?>
					<h3><?=$i+1?>:</h3>
					<label for="imgid_<?=$i?>_img">Photo</label>
					<?=imageLocTag($suggestion[$i], 'medium', true, 'Suggested Photo')?>
					<br />
					<label for="imgid_<?=$i?>_comment">Comment:</label>
					<textarea name="imgid_<?=$i?>_comment"></textarea>
					<br />
					<label for="imgid_<?=$i?>_allow">Suggest:</label>
					<input name="imgid_<?=$i?>_allow" type="checkbox" value="y" />
					<input type="hidden" name="imgid_<?=$i?>_number" value="<?=$suggestion[$i]?>" />
				<?php } ?>
				<input type="hidden" name="imgid_number" value="<?=count($suggestion)?>" />
				<input type="submit" name="r_suggest" value="Suggest" class="button" />
				<br />
			</fieldset>
		</div>

<?php } else { ?>
	</form>
	<form class="form">
		<div class="blue_box">
			<h2>photos</h2>
			<div id="proposed_photos">
<?php foreach ($photos as $photo) {
	echo('				<div class="photo_item">');
	echo('					<a href="/office/gallery/show/' . $photo['id'] . '"><img src="' . $photo['url'] . '" alt="' . $photo['comment'] . '" title="' . $photo['comment'] . '" /></a>');
	echo('					<a href=""><img src="/images/prototype/news/delete.gif" alt="Delete" title="Delete" class="delete_icon" /></a>');
	echo('					' . $photo['user_name'] . '<br />');
	echo('					' . date('d/m/y @ H:i',$photo['time']) . '');
	echo('            </div>');
} ?>
			</div>
			<div style="clear:both;">&nbsp;</div>
			<input type="button" name="r_gallery" id="r_gallery" value="Select from Gallery" class="button" onclick="window.location='/office/gallery/';" />
			<input type="button" name="r_upload" id="r_upload" value="Upload Photo" class="button" onclick="window.location='/office/gallery/upload/';" />
			<div style="clear:both;">&nbsp;</div>
		</div>

		<div class="grey_box">
			<h2>comments</h2>
			<div id="comment_container">
				<div class="feedback">
					<div class="top">
						<span class="right">25th March 2007 @ 22:49</span>
 						Chris Travis
					</div>
					<div class="main">
						This is the first comment for this photo request. blah blah blah.
					</div>
				</div>
				<div class="feedback">
					<div class="top">
						<span class="right">25th March 2007 @ 22:50</span>
 						Random Reporter
					</div>
					<div class="main">
						Hey, ive heard that there is a monkey in the tree outside costcutter in Market Square.
						Apparently you can tempt it down from the tree if you have some chocolate.
					</div>
				</div>
			</div>
			<br />
			<fieldset id="comment_form">
				<label for="new_comment" class="full">Add New Comment</label>
				<textarea name="new_comment" id="new_comment" class="full"></textarea>
				<br />
			 	<input type="button" name="add_comment" id="add_comment" value="Add Comment" class="button" />
			</fieldset>
		</div>

	</form>

	<div style="width:422px;">
	<?php // Display comments if thread exists
	if ((isset($comments)) && (NULL !== $comments)) {
		$comments->Load();
	} ?>
	</div>

<?php } ?>
