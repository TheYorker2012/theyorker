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
		<li><a href='/office/photos/view/flagged'>Send to editor for review</a></li>
		<li><a href='#'>Cancel Request</a></li>
		</ul>
	</div>

	<form id='edit_request' action='/office/photos/view/ID' method='post' class='form'>
		<div class='blue_box'>
			<h2>details</h2>
			<fieldset>
				<label for='r_title'>Title:</label>
				<div id='r_title' style='float: left; margin: 5px 10px;'>Monkey in a Tree</div>
				<br />
				<label for='r_brief'>Description:</label>
				<div id='r_brief' style='float: left; margin: 5px 10px;'>
					I need a photo of a really old tree, preferably with a monkey up it. If you can get a photo with it eating a banana then that would be fantastic.
				</div>
			    <br />
				<label for='r_date'>Date Requested:</label>
				<div id='r_date' style='float: left; margin: 5px 10px;'>25th March 2007 @ 22:40</div>
				<br />
				<label for='r_user'>Requested By:</label>
				<div id='r_user' style='float: left; margin: 5px 10px;'>Chris Travis</div>
				<br />
				<label for="r_article">For Article:</label>
				<div id="r_article" style="float: left; margin: 5px 10px;">
					<a href="/office/news/78" target="_blank">
						Computer Scientists make Pong in MCP
					</a>
				</div>
				<br />
				<label for='r_assigned'>Assigned to:</label>
				<div id='r_assigned' style='float: left; margin: 5px 10px;'>Chris Travis</div>
				<input type='button' name='r_assign' value='Unassign from me' class='button' />
				<br />
			</fieldset>
		</div>

		<div class="blue_box">
			<h2>photos</h2>
			<div id="proposed_photos">
				<div class="photo_item">
					<a href="/images/prototype/news/default_photo_large.jpg">
						<img src="/images/prototype/news/default_photo.jpg" alt="Proposed Photo" title="Proposed Photo" />
					</a>
					<a href="">
						<img src="/images/prototype/news/delete.gif" alt="Delete" title="Delete" class="delete_icon" />
					</a>
					Mickey Mouse<br />
					25/03/07 22:56
                </div>
				<div class="photo_item">
					<a href="/images/prototype/news/default_photo_large.jpg">
						<img src="/images/prototype/news/default_photo.jpg" alt="Proposed Photo" title="Proposed Photo" />
					</a>
					<a href="">
						<img src="/images/prototype/news/delete.gif" alt="Delete" title="Delete" class="delete_icon" />
					</a>
					Chris Travis<br />
					25/03/07 22:56
                </div>
                <div class="photo_item">
					<a href="/images/prototype/news/default_photo_large.jpg">
						<img src="/images/prototype/news/default_photo.jpg" alt="Proposed Photo" title="Proposed Photo" />
					</a>
					<a href="">
						<img src="/images/prototype/news/delete.gif" alt="Delete" title="Delete" class="delete_icon" />
					</a>
					Pluto<br />
					25/03/07 22:56
                </div>
                <div class="photo_item">
					<a href="/images/prototype/news/default_photo_large.jpg">
						<img src="/images/prototype/news/default_photo.jpg" alt="Proposed Photo" title="Proposed Photo" />
					</a>
					<a href="">
						<img src="/images/prototype/news/delete.gif" alt="Delete" title="Delete" class="delete_icon" />
					</a>
					Donald Duck<br />
					25/03/07 22:56
                </div>
                <div class="photo_item">
					<a href="/images/prototype/news/default_photo_large.jpg">
						<img src="/images/prototype/news/default_photo.jpg" alt="Proposed Photo" title="Proposed Photo" />
					</a>
					<a href="">
						<img src="/images/prototype/news/delete.gif" alt="Delete" title="Delete" class="delete_icon" />
					</a>
					Minnie Mouse<br />
					25/03/07 22:56
                </div>
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