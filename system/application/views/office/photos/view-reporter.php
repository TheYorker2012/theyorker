	<style type='text/css'>
	#proposed_photos .photo_item {
		float: left;
		width: 33%;
		font-size: small;
		text-align: center;
		margin-bottom: 5px;
	}
	#proposed_photos .photo_item .delete_icon {
		float: right;
	}
	</style>

	<div class='RightToolbar'>
	    <h4>Operations</h4>
	   	<ul>
		<li><a href='#'>Cancel Request</a></li>
		</ul>
	</div>

	<form name='edit_request' id='edit_request' action='/office/photos/view/<?=$photoRequest->photo_request_id?>/reporter' method='post' class='form'>
		<div class='blue_box'>
			<h2>details</h2>
			<fieldset>
				<label for='r_title'>Title:</label>
				<input type='text' name='r_title' id='r_title' value='<?=$photoRequest->photo_request_title?>' size='30' />
				<br />
				<label for='r_brief'>Description:</label>
				<textarea name='r_brief' id='r_brief' cols='25' rows='5'><?=$photoRequest->photo_request_description?></textarea>
			    <br />
				<label for='r_date'>Date Requested:</label>
				<div id='r_date' style='float: left; margin: 5px 10px;'><?=$photoRequest->photo_request_timestamp?></div>
				<br />
				<label for='r_user'>Requested By:</label>
				<div id='r_user' style='float: left; margin: 5px 10px;'><?=fullname($photoRequest->photo_request_user_entity_id)?></div>
				<br />
				<label for="r_article">For Article:</label>
				<div id="r_article" style="float: left; margin: 5px 10px;">
					<a href="/office/news/<?=$photoRequest->photo_request_article_id?>" target="_blank"><?=$article['request_title']?></a>
				</div>
				<br /><!--
				<label for='r_assigned'>Assigned to:</label>
				<div id='r_assigned' style='float: left; margin: 5px 10px;'>Unassigned</div>
				<input type='button' name='r_assign' value='Assign to me' class='button' />
				<br />-->
			</fieldset>
		</div>

<?php if(isset($suggestion)) {?>
		<div class="blue_box">
			<h2>Your Suggestion</h2>
			<fieldset>
				<?php for($i=0; $i < count($suggestion); $i++) {?>
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
<?php }?>
				<input type="hidden" name="imgid_number" value="<?=count($suggestion)?>" />
				<input type='submit' name='r_assign' value='Suggest' class='button' />
				<br />
			</fieldset>
		</div>
<?php }?>

		<div class="blue_box">
			<h2>photos</h2>
			<div id="proposed_photos">
<?php if($photoRequest->photo_count != 0) foreach ($photos->result() as $preview) {?>
				<div class="photo_item">
					<?=imageLocTag($preview->photo_request_photo_photo_id, 'medium', true, 'Proposed Photo')?>
					<?=fullname($preview->photo_request_photo_user_id)?><br />
					<?=$preview->photo_request_photo_date?><br />
				</div>
<?php }?>
			</div>
			<div style="clear:both;">&nbsp;</div>
			<input type="button" name="r_gallery" id="r_gallery" value="Select from Gallery" class="button" onclick="window.location='/office/gallery/';" />
			<input type="button" name="r_upload" id="r_upload" value="Upload Photo" class="button" onclick="window.location='/office/gallery/upload/';" />
			<div style="clear:both;">&nbsp;</div>
		</div>

<!--
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
-->
	</form>