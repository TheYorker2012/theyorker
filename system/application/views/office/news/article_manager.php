<script type="text/javascript">
onLoadFunctions.push(loadPage);
onLoadFunctions.push(disablePages);
</script>

<div id="am_container">
	<div id="am_sidebar">
		<ul id="am_nav">
			<li id="am_nav_request"><a href="#" onclick="return switchPage('request');">Request</a></li>
			<li id="am_nav_article"><a href="#" onclick="return switchPage('article');">Article</a></li>
			<li id="am_nav_photos"><a href="#" onclick="return switchPage('photos');">Photos</a></li>
			<li id="am_nav_bylines"><a href="#" onclick="return switchPage('bylines');">Bylines</a></li>
			<li id="am_nav_related"><a href="#" onclick="return switchPage('related');">Related Articles</a></li>
			<li id="am_nav_comments"><a href="#" onclick="return switchPage('comments');">Comments</a></li>
			<li id="am_nav_publish"><a href="#" onclick="return switchPage('publish');">Publish</a></li>
		</ul>
		<input type="button" name="save_button" id="save_button" value="Save" />
	</div>

	<div id="am_canvas">
		<div id="am_blackout"></div>
		<div id="am_popup_container">
			<div id="am_popup_test">
				<img src="/images/prototype/homepage/error.png" alt="Error" />
				Message will go here! :)<br /><br />
				<div class="am_popup_buttons">
					<input type="button" value="Make Article" onclick="enablePages();hidePopup('test');" />
					<input type="button" value="Dismiss" onclick="hidePopup('test');" />
				</div>
				<div class="clear"></div>
			</div>
		</div>

		<div id="am_pages">
			<div class="am_page" id="am_page_request">
				The main article content and controls will go here somewhere!<br /><br />
				<b>REQUEST</b><br />
				<input type="button" value="Show Popup!" onclick="showPopup('test');" />
			</div>
			<div class="am_page" id="am_page_article"><br /><br /><b>ARTICLE</b></div>
			<div class="am_page" id="am_page_photos"><br /><br /><b>PHOTOS</b></div>
			<div class="am_page" id="am_page_bylines"><br /><br /><b>BYLINES</b></div>
			<div class="am_page" id="am_page_related"><br /><br /><b>RELATED</b></div>
			<div class="am_page" id="am_page_comments"><br /><br /><b>COMMENTS</b></div>
			<div class="am_page" id="am_page_publish"><br /><br /><b>PUBLISH</b></div>
		</div>

		<div class="clear_right"></div>
	</div>
</div>