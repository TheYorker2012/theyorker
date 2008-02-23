	<style type='text/css'>
	#proposed_photos .photo_item {
		font-size: small;
		text-align: left;
		margin-bottom: 5px;
	}

	#proposed_photos .photo_item img {
		float: left;
		margin-right: 5px;
	}

	#proposed_photos .photo_item .selection {
		float: right;
	}
	</style>

	<div class='RightToolbar'>
	    <h4>Operations</h4>
	   	<ul>
		<li><a href='/office/photos/view'>Send back for additional proposed photos</a></li>
		</ul>
	</div>

	<form id='edit_request' action='/office/photos/view/ID' method='post' class='form'>
		<div class='blue_box'>
			<h2>details</h2>
			<fieldset>
				<label for='r_title'>Title:</label>
				<input type='text' name='r_title' id='r_title' value='Monkey in a Tree' size='30' />
				<br />
				<label for='r_brief'>Description:</label>
				<textarea name='r_brief' id='r_brief' cols='25' rows='5'>I need a photo of a really old tree, preferably with a monkey up it. If you can get a photo with it eating a banana then that would be fantastic.</textarea>
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
					<input type="radio" name="photo_option" value="1" class="selection" />
					Chris Travis<br />
					25/03/07 22:56
					<br style="clear:both;" />
                </div>
				<div class="photo_item">
					<a href="/images/prototype/news/default_photo_large.jpg">
						<img src="/images/prototype/news/default_photo.jpg" alt="Proposed Photo" title="Proposed Photo" />
					</a>
					<input type="radio" name="photo_option" value="2" class="selection" />
					Chris Travis<br />
					25/03/07 22:56
					<br style="clear:both;" />
                </div>
                <div class="photo_item">
					<a href="/images/prototype/news/default_photo_large.jpg">
						<img src="/images/prototype/news/default_photo.jpg" alt="Proposed Photo" title="Proposed Photo" />
					</a>
					<input type="radio" name="photo_option" value="3" class="selection" />
					Chris Travis<br />
					25/03/07 22:56
					<br style="clear:both;" />
                </div>
                <div class="photo_item">
					<a href="/images/prototype/news/default_photo_large.jpg">
						<img src="/images/prototype/news/default_photo.jpg" alt="Proposed Photo" title="Proposed Photo" />
					</a>
					<input type="radio" name="photo_option" value="4" class="selection" />
					Chris Travis<br />
					25/03/07 22:56
					<br style="clear:both;" />
                </div>
                <div class="photo_item">
					<a href="/images/prototype/news/default_photo_large.jpg">
						<img src="/images/prototype/news/default_photo.jpg" alt="Proposed Photo" title="Proposed Photo" />
					</a>
					<input type="radio" name="photo_option" value="5" class="selection" />
					Chris Travis<br />
					25/03/07 22:56
					<br style="clear:both;" />
                </div>
			</div>
			<div style="clear:both;">&nbsp;</div>
			<input type="button" name="r_choose" id="r_choose" value="Choose Photo" class="button" />
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