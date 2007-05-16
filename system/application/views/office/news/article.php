	<script type='text/javascript'>
	var articleRevision = 0;
	var timers = new Array();
	timers['Headline'] = 0;
	timers['Subheadline'] = 0;
	timers['Subtext'] = 0;
	timers['Blurb'] = 0;
	timers['Content'] = 0;
	timers['Factbox'] = 0;
	var pages = new Array('request','article','comments','revisions');
	var preview = 0;
	var functionQueue = '';

	function tabs (selected) {
		for (i=0; i < pages.length; i++) {
			document.getElementById('content_' + pages[i]).className = 'hide';
			document.getElementById('Navbar_' + pages[i]).className = '';
		}
		document.getElementById('content_' + selected).className = 'show';
		document.getElementById('Navbar_' + selected).className = 'current';
	}

	function previewArticle() {
		preview = 1;
		updateHeadlinesManual();
	}

	function articleContentUpdate (field) {
		clearTimeout(timers[field]);
		timers[field] = setTimeout('updateHeadlinesAuto()',3000);
	}

	function updateHeadlinesAuto() {
		document.getElementById('save_load').className = 'ajax_loading show';
		updateHeadlines();
	}

	function updateHeadlinesManual() {
		document.getElementById('save_load2').className = 'ajax_loading show';
		updateHeadlines();
	}

	function updateHeadlines() {
		document.getElementById('save_form').className = 'form hide';
		var headline = document.getElementById('headline').value;
		var subheadline = document.getElementById('subheadline').value;
		var subtext = document.getElementById('subtext').value;
		var blurb = document.getElementById('blurb').value;
		var wiki = document.getElementById('content').value;
		var fact_heading = document.getElementById('factbox_heading').value;
		var fact_text = document.getElementById('factbox').value;
		xajax__updateHeadlines(articleRevision,headline,subheadline,subtext,blurb,wiki,preview,fact_heading,fact_text);
	}

	function updatePhoto(id,operation) {
		document.getElementById('photo_requests').innerHTML = '';
		xajax__updatePhoto(id,operation);
	}

	function createNewPhoto() {
		var title = document.getElementById('photo_title').value;
		var description = document.getElementById('photo_description').value;
		document.getElementById('photo_requests').innerHTML = '';
		xajax__newPhoto(title,description);
	}

	function photo_created(photo,id,title,datetime,number,main,thumb) {
		var container = document.getElementById('photo_requests');

		container.innerHTML = container.innerHTML + '<div style="margin-bottom:5px;">';
		container.innerHTML = container.innerHTML + '<a href="/office/photos/view/' + id + '"><img src="' + photo + '" alt="' + title + '" title="' + title + '" style="float: left; margin-right: 5px;" /></a>';
		container.innerHTML = container.innerHTML + '<b>Photo ' + number + '</b> ';
		if (main == 1) {
			container.innerHTML = container.innerHTML + '(M)';
		}
		if (thumb == 1) {
			container.innerHTML = container.innerHTML + '(T)';
		}
		container.innerHTML = container.innerHTML + '<br />';
		container.innerHTML = container.innerHTML + '<a href="/office/photos/view/' + id + '">' + title + '</a><br />';
		container.innerHTML = container.innerHTML + '<span style="font-size:x-small;">' + datetime + '<br />';
		container.innerHTML = container.innerHTML + '[ <a onclick="insertImageTag(\'content\', \'' + number + '\');return false;" href="#">Insert</a> ]';
		if (main != 1) {
			container.innerHTML = container.innerHTML + ' [ <a href="#" onclick="updatePhoto(\'' + number + '\',\'main\');return false;">Main</a> ]';
		}
		if (thumb != 1) {
			container.innerHTML = container.innerHTML + ' [ <a href="#" onclick="updatePhoto(\'' + number + '\',\'thumbnail\');return false;">Thumbnail</a> ]';
		}
		container.innerHTML = container.innerHTML + '</span><br class="clear" /></div>';

	}

	function headlinesUpdates (revision, date) {
		articleRevision = revision;
		document.getElementById('revision_status').innerHTML = "You are editing revision <b>" + revision + "</b> which was last saved @ <b>" + date + "</b>";
		document.getElementById('save_load').className = 'ajax_loading hide';
		document.getElementById('save_load2').className = 'ajax_loading hide';
		if (preview) {
			document.getElementById('preview_load').className = 'ajax_loading show';
			window.location = '/office/news/preview/<?php echo $article['id']; ?>/<?php echo $article['box_codename']; ?>/' + articleRevision;
		} else if (functionQueue != '') {
			eval(functionQueue + '();');
			functionQueue = '';
		} else {
			document.getElementById('save_form').className = 'form show';
		}
	}

	function addComment() {
		if (document.getElementById('new_comment').value == '') {
			alert('Please enter a comment to submit.');
		} else {
			document.getElementById('comment_form').className = 'hide';
			document.getElementById('comment_load').className = 'ajax_loading show';
			xajax__addComment(document.getElementById('new_comment').value);
		}
	}

	function commentAdded(date, name, text) {
		document.getElementById('new_comment').value = '';
		document.getElementById('comment_form').className = 'show';
		document.getElementById('comment_load').className = 'ajax_loading hide';
		if (text != '') {
			var container = document.getElementById('comment_container');
			container.innerHTML = '<div class="feedback"><div class="top"><span class="right">' + date + '</span>' + name + '</div><div class="main">' + text + '</div></div>' + container.innerHTML;
		}
	}

	function newFactbox() {
		document.getElementById('factbox_create').className = 'hide';
		document.getElementById('factbox_load').className = 'ajax_loading show';
		if (articleRevision == 0) {
			functionQueue = 'newFactbox';
			updateHeadlines();
		} else {
			var fact_heading = document.getElementById('factbox_heading').value;
			var fact_text = document.getElementById('factbox').value;
			xajax__newFactbox(articleRevision,fact_heading,fact_text);
		}
	}

	function factbox_created(success) {
		if (success == 0) {
			functionQueue = 'newFactbox';
			updateHeadlines();
		} else {
			document.getElementById('factbox_load').className = 'hide';
			document.getElementById('factbox_container').className = 'show';
			document.getElementById('save_form').className = 'form show';
		}
	}

	function deleteFactbox() {
		document.getElementById('factbox_container').className = 'hide';
		document.getElementById('factbox_load2').className = 'ajax_loading show';
		if (articleRevision == 0) {
			functionQueue = 'deleteFactbox';
			updateHeadlines();
		} else {
			xajax__removeFactBox(articleRevision);
		}
	}

	function factbox_deleted() {
		document.getElementById('factbox_load2').className = 'hide';
		document.getElementById('factbox_create').className = 'show';
	}

	function delete_article() {
		if (confirm('Are you sure you want to remove this article? This operation cannot be reverted.')) {

		}
	}

	// pre-load ajax images
	imageObj = new Image();
	imageObj.src = '/images/prototype/prefs/loading.gif';
	imageObj.src = '/images/prototype/prefs/yorker-bg.png';
	</script>

	<form action="/office/news/<?php echo $article['id']; ?>" method="post" class="form">
		<div class="RightToolbar">
			<h4>Photo Requests</h4>
			<div id="photo_requests">

<?php if (count($photo_requests) > 0) {
	foreach ($photo_requests as $request) { ?>
				<div style="margin-bottom:5px;">
					<a href="/office/photos/view/<?php echo($request['id']); ?>"><img src="<?php echo(imageLocation($request['chosen_photo'], 'small')); ?>" alt="<?php echo($request['title']); ?>" title="<?php echo($request['title']); ?>" style="float: left; margin-right: 5px;" /></a>
					<b>Photo <?php echo($request['photo_number']); ?></b>
					<?php if ($article['photo_main'] == $request['photo_number']) { echo('(M)'); } ?>
					<?php if ($article['photo_thumbnail'] == $request['photo_number']) { echo('(T)'); } ?>
					<br />
					<a href="/office/photos/view/<?php echo($request['id']); ?>"><?php echo($request['title']); ?></a><br />
					<span style="font-size:x-small;">
						<?php echo(date('d/m/y @ H:i', $request['time'])); ?><br />
						[ <a onclick="insertImageTag('content', '<?php echo($request['photo_number']); ?>');return false;" href="#">Insert</a> ]
						<?php if ($article['photo_main'] != $request['photo_number']) { ?> [ <a href="#" onclick="updatePhoto('<?php echo($request['photo_number']); ?>','main');return false;">Main</a> ]<?php } ?>
						<?php if ($article['photo_thumbnail'] != $request['photo_number']) { ?> [ <a href="#" onclick="updatePhoto('<?php echo($request['photo_number']); ?>','thumbnail');return false;">Thumbnail</a> ]<?php } ?>
					</span>
					<br class="clear" />
				</div>
<?php	}
	} ?>
			</div>
			<div>
				<input type="text" name="photo_title" id="photo_title" value="Photo Title" />
				<textarea name="photo_description" id="photo_description" rows="3">Description of Photo required</textarea>
				<input type="button" name="new_photo" id="new_photo" value="Request Photo" class="button" onclick="createNewPhoto();" />
				<br class="clear" />
			</div>

			<h4>Article Status</h4>
			<div id="revision_status">
				You are editing revision <b><?php echo $revision['id']; ?></b> which was last saved <b><?php echo date('D jS F Y @ H:i:s',$revision['last_edit']); ?></b>
			</div><br />
			<div id="save_load" class="ajax_loading hide">
				<img src="/images/prototype/prefs/loading.gif" alt="Saving" title="Saving" /> Auto Saving...
			</div>
			<div id="save_load2" class="ajax_loading hide">
				<img src="/images/prototype/prefs/loading.gif" alt="Saving" title="Saving" /> Saving...
			</div>
			<div id="preview_load" class="ajax_loading hide">
				<img src="/images/prototype/prefs/loading.gif" alt="Loading" title="Loading" /> Loading Preview...
			</div>
			<div id="save_form" class="form">
				<input type="button" name="save_changes" id="save_changes" value="Save Article" class="button" onclick="updateHeadlinesManual();" />
				<br />
				<input type="button" name="preview" id="preview" value="Preview Article" class="button" onclick="previewArticle();" />
				<br />
				<?php if ($user_level == 'editor') { ?>
					<input type="button" name="delete" id="delete" value="Delete Article" class="button" onclick="delete_article();" />
				<?php } ?>
			</div>
			<br style="clear:both;" /><br style="clear:both;" />
			<?php if ($user_level == 'editor') { ?>
				<h4>Publish Article</h4>
				<div id="publish_form" class="form">
					<input type="submit" name="publish" id="publish" value="Publish Article" class="button" />
				</div>
			<?php } ?>
		</div>

		<div id="content_request">
			<div class="blue_box">
				<h2><?php echo $request_heading; ?></h2>
				<fieldset>
					<label for="title">Title:</label>
					<div id="title" class="info"><?php echo $article['request_title']; ?></div>
					<br />
					<label for="description">Description:</label>
					<div id="description" class="info"><?php echo nl2br($article['request_description']); ?></div>
					<br />
					<label for="box">Box:</label>
					<div id="box" class="info"><?php echo $article['box_name']; ?></div>
					<br />
					<label for="created">Created:</label>
					<div id="created" class="info"><?php echo(date('D jS F Y @ H:i',$article['date_created'])); ?></div>
					<br />
					<label for="suggest">Suggested by:</label>
					<div id="suggest" class="info"><?php echo $article['suggest_name']; ?></div>
					<br />
					<label for="deadline">Deadline:</label>
					<div id="deadline" class="info"><?php if ($article['date_deadline'] == 0) { echo 'None'; } else { echo(date('D jS F Y @ H:i',$article['date_deadline'])); } ?></div>
					<br />
					<label for="editor">Editor:</label>
					<div id="editor" class="info"><?php echo $article['editor_name']; ?></div>
					<br />
					<label for="reporters">Reporters:</label>
					<div id="reporters" class="info">
					<?php foreach ($article['reporters'] as $reporter) {
						echo ($reporter['name'] . ' (' . $reporter['status'] . ')<br />');
					} ?>
					</div>
					<br />
				</fieldset>
			</div>
			<div style='width: 422px;'>
				<?php if ($user_requested) { ?>
			 	<input type='submit' name='decline' id='submit2' value='Decline Request' class='button' />
			 	<input type='submit' name='accept' id='submit' value='Accept Request' class='button' />
				<?php } ?>
			</div>
		</div>

		<div id="content_article" class="hide">
			<div class="blue_box">
				<h2>main article body...</h2>
				<fieldset>
					<label for="headline">Headline:</label>
					<input type="text" name="headline" id="headline" value="<?php echo $revision['headline']; ?>" size="30" onkeyup="articleContentUpdate('Headline');" />
					<br />
					<label for="subheadline">Sub-Headline:</label>
					<input type="text" name="subheadline" id="subheadline" value="<?php echo $revision['subheadline']; ?>" size="30" onkeyup="articleContentUpdate('Subheadline');" />
					<br />
					<label for="subtext" class="full">Introduction Paragraph</label>
					<textarea name="subtext" id="subtext" class="full" rows="2" onkeyup="articleContentUpdate('Subtext');"><?php echo $revision['subtext']; ?></textarea>
					<br />
					<label for="blurb" class="full" onkeyup="articleContentUpdate('Blurb');">Blurb</label>
					<textarea name="blurb" id="blurb" class="full" rows="2"><?php echo $revision['blurb']; ?></textarea>
					<br />
					<label for="content" class="full">Main Article Content</label>
					<div id="toolbar"></div>
					<textarea name="content" id="content" class="full" rows="18" onkeyup="articleContentUpdate('content');"><?php echo $revision['text']; ?></textarea>
					<br />
				</fieldset>
			</div>
		</div>

		<div id="content_comments" class="hide" style="width:422px;">
			<?php /*<div class="blue_box">
				<h2>comments...</h2>
				<div id="comment_load" class="ajax_loading hide">
					<img src="/images/prototype/prefs/loading.gif" alt="Loading" title="Loading" /> Saving new comment...
				</div>
				<fieldset id="comment_form">
					<label for="new_comment" class="full">Add New Comment</label>
					<textarea name="new_comment" id="new_comment" class="full"></textarea>
					<br />
				 	<input type="button" name="add_comment" id="add_comment" value="Add Comment" class="button" onclick="addComment();" />
				</fieldset>
				<br />
				<div id="comment_container">
				<?php foreach ($comments as $comment) { ?>
					<div class="feedback">
						<div class="top">
							<span class="right">
								<?php echo (date('D jS F Y @ H:i', $comment['time'])); ?>
							</span>
							<?php echo $comment['name']; ?>
						</div>
						<div class="main">
							<?php echo nl2br($comment['text']); ?>
						</div>
					</div>
				<?php } ?>
				</div>
			</div> */ ?>
			<?php
			// Comments if they're included
			if (isset($comments) && NULL !== $comments) {
				$comments->Load();
			}
			?>
		</div>

		<div id="content_revisions" class="hide">
			<div class="blue_box">
				<h2>revisions...</h2>
				<div id="revision_container">
				<?php foreach ($revisions as $rev) { ?>
					<div class="revision">
						<span class="right">
							<?php echo (date('D jS F Y @ H:i', $rev['updated'])); ?>
						</span>
						<?php echo ('<b>#' . $rev['id'] . '</b> - ' . $rev['username']); ?>
					</div>
				<?php } ?>
				</div>
			</div>
		</div>

		<div id="content_sidebar" class="hide">
			<div class="blue_box">
				<h2>fact box...</h2>
				<div id="factbox_load" class="ajax_loading hide">
					<img src="/images/prototype/prefs/loading.gif" alt="Creating" title="Creating" /> Creating new fact box...
				</div>
				<div id="factbox_load2" class="ajax_loading hide">
					<img src="/images/prototype/prefs/loading.gif" alt="Deleting" title="Deleting" /> Deleting fact box...
				</div>
				<div id="factbox_create"<?php if (isset($revision['fact_box'])) { echo ' class="hide"'; } ?>>
					There is currently no fact box for this article.
					<br />
					<input type="button" name="make_factbox" id="make_factbox" class="button" style="float: none;" value="Create Fact Box" onclick="newFactbox();" />
				</div>
				<div id="factbox_container"<?php if (!isset($revision['fact_box'])) { echo ' class="hide"'; } ?>>
					<fieldset>
						<label for="factbox_heading">Factbox Heading:</label>
						<input type="text" name="factbox_heading" id="factbox_heading" value="<?php if (isset($revision['fact_box'])) { echo $revision['fact_box']['title']; } ?>" size="30"  onkeyup="articleContentUpdate('Factbox');" />
						<br />
						<label for="factbox" class="full">Factbox Content</label>
						<textarea name="factbox" id="factbox" class="full" rows="18"  onkeyup="articleContentUpdate('Factbox');"><?php if (isset($revision['fact_box'])) { echo $revision['fact_box']['text']; } ?></textarea>
						<br />
						<input type="button" name="remove_factbox" id="remove_factbox" class="button" value="Delete Fact Box" onclick="deleteFactbox();" />
					</fieldset>
				</div>
			</div>
		</div>
	</form>

	<script type="text/javascript">
		mwSetupToolbar('toolbar','content', false);
	</script>
