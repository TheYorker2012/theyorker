<script type='text/javascript' src='/javascript/calendar_select.js'></script>
<script type='text/javascript' src='/javascript/calendar_select-en.js'></script>
<script type='text/javascript' src='/javascript/calendar_select-setup.js'></script>
<script type="text/javascript">
setStartPage('<?php echo($start_page); ?>');
setData(<?php echo($articleJS); ?>);
onLoadFunctions.push(loadPage);
window.onbeforeunload = leavePage;
</script>

<div id="office_container">
	<div id="office_sidebar">
		<ul id="office_nav">
			<li id="nav_brief"><a href="#" onclick="return switchPage('brief');">Brief</a></li>
			<li id="nav_article"><a href="#" onclick="return switchPage('article');">Content</a></li>
			<li id="nav_photos"><a href="#" onclick="return switchPage('photos');">Photos</a></li>
			<li id="nav_bylines"><a href="#" onclick="return switchPage('bylines');">Bylines</a></li>
			<li id="nav_related"><a href="#" onclick="return switchPage('related');">Related Articles</a></li>
			<li id="nav_comments"><a href="#" onclick="return switchPage('comments');">Comments</a></li>
			<li id="nav_revisions"><a href="#" onclick="return switchPage('revisions');">Revisions</a></li>
			<li id="nav_publish"><a href="#" onclick="return switchPage('publish');">Publish</a></li>
		</ul>

		<div id="office_sidebar_save">
			<input type="button" value="Save" onclick="detectChanges();" /><br />
			<input type="button" value="Preview" onclick="window.location='/news/<?php echo($article['id']); ?>';" />
		</div>
		<div id="office_sidebar_wait" class="hide">
			<img src="/images/version2/office/ajax-loader.gif" alt="Please wait" /> <span id="wait_msg">Saving...</span>
		</div>
	</div>

	<div id="office_canvas">
		<div id="office_pages">

			<div class="office_page" id="page_brief">
				<img src="/images/version2/office/icon_announcements.png" alt="Article Brief" class="title" />
				<h2>Article Brief</h2>

				<label for="article_request_title">Title:</label>
				<input type="text" name="article_request_title" id="article_request_title" value="" size="40" class="heading" />
				<br />
				<label for="article_request_description">Brief:</label>
				<textarea name="article_request_description" id="article_request_description" rows="5" cols="50"></textarea>
				<br />
				<label for="article_type_id">Type:</label>
				<select name="article_type_id" id="article_type_id">
					<option value="-1">-</option>
					<?php foreach ($types as $type) { ?>
					<option value="<?php echo($type['id']); ?>"><?php echo(ucfirst($type['section']) . ' - ' . $type['name']); ?></option>
					<?php } ?>
				</select>
				<br />
				<label for="article_date_created_full">Created:</label>
				<div id="article_date_created_full" class="input"></div>
				<div id="article_date_created_academic" class="input"></div>
				<br />
				<label for="article_date_deadline_full">Deadline:</label>
				<div id="article_date_deadline_full" class="input"></div>
				<!--<div id="article_date_deadline_academic" class="input"></div>-->
				<input type="hidden" id="article_date_deadline" value="<?php echo($article['date_deadline']); ?>" />
				<input type="button" id="deadline_trigger" value="Change" />
				<br />

				<input type="hidden" id="article_thumbnail_photo_id" value="" />
				<input type="hidden" id="article_main_photo_id" value="" />
			</div>

			<div class="office_page" id="page_article">
				<img src="/images/version2/office/icon_article.png" alt="Article Contents" class="title" />
				<h2>Article Content</h2>

				<label for="article_content_heading">Headline:</label>
				<input type="text" name="article_content_heading" id="article_content_heading" value="" size="40" class="heading" />
				<br />
				<label for="article_content_blurb">Blurb:</label>
				<textarea name="article_content_blurb" id="article_content_blurb" rows="4" cols="50"></textarea>
				<br />
				<label for="article_content_subtext">Introduction:</label>
				<textarea name="article_content_subtext" id="article_content_subtext" rows="4" cols="50"></textarea>
				<br />
				<label>&nbsp;</label>
				<div id="toolbar" class="input"></div>
				<br />
				<label for="article_content_wikitext">Body:</label>
				<textarea name="article_content_wikitext" id="article_content_wikitext" rows="20" cols="50"></textarea>
				<br />
			</div>

			<div class="office_page" id="page_photos">
				<div class="actions">
					<input type="button" value="Gallery" onclick="window.location='/office/article/photo/<?php echo($article['id']); ?>';" />
					<input type="button" value="Upload" onclick="window.location='/office/article/photo/<?php echo($article['id']); ?>/upload';" />
				</div>
				<img src="/images/version2/office/icon_article.png" alt="Your Articles" class="title" />
				<h2>Photos</h2>
				
				<div id="photo_container"></div>
			</div>

			<div class="office_page" id="page_bylines">
				<img src="/images/version2/office/icon_article.png" alt="Bylines" class="title" />
				<h2>Bylines</h2>

				<label for="article_creator_name">Creator:</label>
				<div id="article_creator_name" class="input"></div>
				<br />
				<label for="article_editor_name">Editor:</label>
				<div id="article_editor_name" class="input"></div>
				<input type="button" id="changeEditor" value="Change" onclick="changeEditor();" />
				<select id="article_editor_user_id" class="hide" onchange="changedEditor();">
					<option value="<?php echo($article['editor_user_id']); ?>"><?php echo($article['editor_name']); ?></option>
					<?php foreach ($editors as $ed) { ?>
					<option value="<?php echo($ed['id']); ?>"><?php echo($ed['fullname']); ?></option>
					<?php } ?>
				</select>
				<br />

				<div class="clear"><br /><br /></div>

				<div id="reporter_container"></div>
				<div id="reporter_prompt">
					<label>&nbsp;</label>
					<input type="button" value="Add Reporter" onclick="$('reporter_prompt').className='hide';$('reporter_control').className='show';" />
				</div>
				<div id="reporter_control" class="hide">
					<label for="newReporter">Add Reporter:</label>
					<select id="newReporter" onchange="addReporter(this);">
						<option value="-1">-</option>
						<?php foreach ($all_reporters as $r) { ?>
						<option value="<?php echo($r['id']); ?>"><?php echo($r['fullname']); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="office_page" id="page_related">
				<div id="custom_tags"></div>
				<img src="/images/version2/office/icon_article.png" alt="Tags" class="title" />
				<h2>Tags</h2>
				<div id="all_tags"></div>
			</div>

			<div class="office_page" id="page_comments">
				<img src="/images/version2/office/icon_article.png" alt="Comments" class="title" />
				<h2>Comments</h2>
			</div>

			<div class="office_page" id="page_revisions">
				<img src="/images/version2/office/icon_article.png" alt="Revisions" class="title" />
				<h2>Revisions</h2>
			</div>

			<div class="office_page" id="page_publish">
				<img src="/images/version2/office/icon_article.png" alt="Publish" class="title" />
				<h2>Article Status</h2>

				<label for="article_status">Status:</label>
				<div id="article_status" class="input bold heading"></div>
				<br />
				<label for="article_hits">Article Views:</label>
				<div id="article_hits" class="input"></div>
				<br />

				<div class="clear"></div>
				<img src="/images/version2/office/icon_article.png" alt="Checklist" class="title" />
				<h2>Publish</h2>

				<label for="article_date_published_full">Publish Date:</label>
				<div id="article_date_published_full" class="input"></div>
				<!--<div id="article_date_published_academic" class="input"></div>-->
				<input type="hidden" id="publish" value="<?php echo($article['date_published']); ?>" />
				<input type="button" id="publish_trigger" value="Change" />
				<br />

				<label>&nbsp;</label>
				<input type="button" value="PUBLISH ARTICLE" onclick="publishArticle();" />
				<br />
			</div>

		</div>
		<div class="clear"></div>
	</div>
</div>

<script type="text/javascript">
//<![CDATA[
mwSetupToolbar('toolbar','article_content_wikitext', false);

Calendar.setup(
	{
		inputField	: 'publish',
		ifFormat	: '%s',
		displayArea	: 'article_date_published_full',
		daFormat	: '%a %e %b, %Y @ %H:%M',
		button		: 'publish_trigger',
		singleClick	: false,
		firstDay	: 1,
		date		: <?php echo(js_literalise($article['date_published'])); ?>,
		weekNumbers	: false,
		showsTime	: true,
		timeFormat	: '24'
	}
);

Calendar.setup(
	{
		inputField	: 'article_date_deadline',
		ifFormat	: '%s',
		displayArea	: 'article_date_deadline_full',
		daFormat	: '%a %e %b, %Y @ %H:%M',
		button		: 'deadline_trigger',
		singleClick	: false,
		firstDay	: 1,
		date		: <?php echo(js_literalise($article['date_deadline'])); ?>,
		weekNumbers	: false,
		showsTime	: true,
		timeFormat	: '24'
	}
);

//]]>
</script>