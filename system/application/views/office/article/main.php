<script type="text/javascript">
setStartPage('<?php echo($start_page); ?>');
setData(<?php echo($articleJS); ?>);
onLoadFunctions.push(loadPage);
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
			<li id="nav_publish"><a href="#" onclick="return switchPage('publish');">Publish</a></li>
		</ul>
	</div>

	<div id="office_canvas">
		<div id="office_pages">

			<div class="office_page" id="page_brief">
				<div class="actions">
					<a href="/office/organisation">
						<img src="/images/version2/office/button_organisation.png" alt="Organisation Chart" />
					</a>
				</div>

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
				<div id="article_date_deadline_academic" class="input"></div>
				<br />
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
				<label for="article_content_wikitext">Body:</label>
				<textarea name="article_content_wikitext" id="article_content_wikitext" rows="20" cols="50"></textarea>
				<br />
			</div>

			<div class="office_page" id="page_photos">
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
				<label for="">Editor:</label>
				<div id="article_editor_name" class="input"></div>
				<br />
				<label for="">Reporter:</label>
				<br />
			</div>

			<div class="office_page" id="page_related">
				<img src="/images/version2/office/icon_article.png" alt="Related Articles" class="title" />
				<h2>Related</h2>
			</div>

			<div class="office_page" id="page_comments">
				<img src="/images/version2/office/icon_article.png" alt="Comments" class="title" />
				<h2>Comments</h2>
			</div>

			<div class="office_page" id="page_publish">
				<img src="/images/version2/office/icon_article.png" alt="Publish" class="title" />
				<h2>Publish</h2>

				<label for="article_status">Status:</label>
				<div id="article_status" class="input bold heading"></div>
				<br />
				<label for="article_date_published_full">Publish Date:</label>
				<div id="article_date_published_full" class="input"></div>
				<div id="article_date_published_academic" class="input"></div>
				<br />
				<label for="article_hits">Article Views:</label>
				<div id="article_hits" class="input"></div>
				<br />

				<div class="clear"></div>
				<img src="/images/version2/office/icon_article.png" alt="Checklist" class="title" />
				<h2>Checklist</h2>
			</div>

		</div>
		<div class="clear"></div>
	</div>
</div>

<pre style="clear:both">
<?php
print_r($article);
?>
</pre>