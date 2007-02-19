	<script type='text/javascript'>
	var articleRevision = 0;
	var timers = new Array();
	timers['Headline'] = 0;
	timers['Subheadline'] = 0;
	timers['Subtext'] = 0;
	timers['Blurb'] = 0;
	timers['Content'] = 0;
	var pages = new Array('request','article','comments','revisions','sidebar');
	var preview = 0;

	function tabs (selected) {
		for (i=0; i < pages.length; i++) {
			document.getElementById('content_' + pages[i]).className = 'hide';
			document.getElementById('navbar_' + pages[i]).className = '';
		}
		document.getElementById('content_' + selected).className = 'show';
		document.getElementById('navbar_' + selected).className = 'current';
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
		xajax__updateHeadlines(articleRevision,headline,subheadline,subtext,blurb,wiki,preview);
	}

	function headlinesUpdates (revision, date) {
		articleRevision = revision;
		document.getElementById('revision_status').innerHTML = "You are editing revision <b>" + revision + "</b> which was last saved @ <b>" + date + "</b>";
		document.getElementById('save_load').className = 'ajax_loading hide';
		document.getElementById('save_load2').className = 'ajax_loading hide';
		if (preview) {
			document.getElementById('preview_load').className = 'ajax_loading show';
			window.location = '/office/news/preview/<?php echo $article['id']; ?>/<?php echo $article['box_codename']; ?>/' + articleRevision;
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

	// pre-load ajax images
	imageObj = new Image();
	imageObj.src = '/images/prototype/prefs/loading.gif';
	imageObj.src = '/images/prototype/prefs/yorker-bg.png';
	</script>

	<div class="RightToolbar">
		<h4>Article Status</h4>
		<div id="revision_status"></div><br />
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
		</div>
		<br />
	</div>

	<form action="/office/news/" method="post" class="form">
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
					<textarea name="content" id="content" class="full" rows="18" onkeyup="articleContentUpdate('content');"><?php echo $revision['text']; ?></textarea>
					<br />
				</fieldset>
			</div>
		</div>

		<div id="content_comments" class="hide">
			<div class="blue_box">
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
			</div>
		</div>

		<div id="content_revisions" class="hide">
			<div class="blue_box">
				<h2>revisions...</h2>
				<div id="revision_container">
				<?php foreach ($revisions as $revision) { ?>
					<div class="revision">
						<span class="right">
							<?php echo (date('D jS F Y @ H:i', $revision['updated'])); ?>
						</span>
						<?php echo ('<b>#' . $revision['id'] . '</b> - ' . $revision['username']); ?>
					</div>
				<?php } ?>
				</div>
			</div>
		</div>

		<div id="content_sidebar" class="hide">
			<div class="blue_box">
				<h2>sidebar...</h2>
				@TODO: SIDEBAR
			</div>
		</div>
	</form>
