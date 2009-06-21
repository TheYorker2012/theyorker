<style type="text/css">
div.activity p {
	display: inline;
}
</style>

<div class="BlueBox Box23b">
	<h2>recent activity</h2>
	
	<div class="activity">
<?php
foreach ($activity as $a) {
	echo('<div>');
	switch ($a->type) {
		case 'byline':
		case 'byline-teams':
			echo($a->user_name . ' ' . $a->wikitext);
			break;
		case 'announcement':
			echo($a->user_name . ' posted an announcement titled "<a href="/office/announcements">' . $a->subject . '</a>".');
			break;
		default:
			echo('ERROR: Unknown activity type (' . $a->type . ')');
	}
	echo('</div>');
}
?>
	</div>

	<pre>
	<?php print_r($activity); ?>
	</pre>
</div>

<div class="BlueBox Box13b FlexiBoxLast">
	<h2>links</h2>
	<p><a href="/office/bylines">Bylines</a></p>
	<p><a href="/office/irc">IRC Chat</a></p>
	<h2>notifications</h2>
	<?php
	foreach ($notifications as $notification) {
		if (!empty($notification['count'])) {
			echo('<div><a href="' . $notification['link'] . '">' . $notification['count'] . ' ' . $notification['title'] . ($notification['count'] > 1 ? 's' : '') . '</a></div>');
		}
	}
	?>
</div>



<?php /*
<script type="text/javascript">
onLoadFunctions.push(loadPage);

// Announcements
var current_announcement = 0;
function showAnnouncement(id, status) {
	if ((current_announcement != 0) && (current_announcement != id)) {
		// Slide away
		document.getElementById('announcement_' + current_announcement).style.display = 'none';
	}
	if (current_announcement == id) {
		// Slide away
		document.getElementById('announcement_' + id).style.display = 'none';
		current_announcement = 0;
	} else {
		// Slide in
		document.getElementById('announcement_' + id).style.display = 'block';
		current_announcement = id;
		// Mark it as read
		if (status == 'unread') {
			xajax__readAnnouncement(id);
		}
	}
	return false;
}
</script>

<?php
switch (count($my_requests)) {
	case 0:
		$article_text = 'Articles';
		break;
	case 1:
		$article_text = '1 Article';
		break;
	default:
		$article_text = count($my_requests) . ' Articles';
}
?>

<div id="office_container">
	<div id="office_sidebar">
		<ul id="office_nav">
			<li id="nav_announcements"><a href="#" onclick="return switchPage('announcements');">Announcements</a></li>
			<li id="nav_article"><a href="#" onclick="return switchPage('article');"><?php echo($article_text); ?></a></li>
			<!--
			<li id="nav_photos"><a href="#" onclick="return switchPage('photos');">Photos</a></li>
			<li id="nav_bylines"><a href="#" onclick="return switchPage('bylines');">Bylines</a></li>
			<li id="nav_related"><a href="#" onclick="return switchPage('related');">Related Articles</a></li>
			<li id="nav_comments"><a href="#" onclick="return switchPage('comments');">Comments</a></li>
			<li id="nav_publish"><a href="#" onclick="return switchPage('publish');">Publish</a></li>
			-->
		</ul>
	</div>

	<div id="office_canvas">
		<div id="office_pages">
			<div class="office_page" id="page_announcements">
				<div class="actions">
					<a href="/office/organisation">
						<img src="/images/version2/office/button_organisation.png" alt="Organisation Chart" />
					</a>
				</div>

				<img src="/images/version2/office/icon_announcements.png" alt="Announcements" class="title" />
				<h2>Announcements</h2>

				<?php
				foreach ($announcements as $announce) {
					$status = ($announce->opened) ? 'read' : 'unread';
				?>
				<div class="item">
					<div class="header <?php echo($status); ?>">
						<a href="#" onclick="return showAnnouncement(<?php echo(xml_escape($announce->id) . ',\'' . $status . '\''); ?>);">
							<img src="/images/version2/office/smallicon_announcements_<?php echo($status); ?>.png" alt="<?php echo($status); ?>" title="<?php echo($status); ?>" />
						</a>
						<div class="date">
							<?php echo(date('d/m/y H:i', $announce->time)); ?>
						</div>
						<a href="#" onclick="return showAnnouncement(<?php echo(xml_escape($announce->id) . ',\'' . $status . '\''); ?>);">
							<?php echo(xml_escape($announce->subject)); ?>
						</a>
					</div>
					<div id="announcement_<?php echo(xml_escape($announce->id)); ?>" class="content">
						<div class="author">
							<img src="<?php if ($announce->user_image !== NULL) { ?>/photos/userimage/<?php echo($announce->user_image); ?><?php } else { ?>/images/prototype/directory/members/anon.png<?php } ?>" alt="<?php echo(xml_escape($announce->user_name)); ?>" title="<?php echo(xml_escape($announce->user_name)); ?>" />
							<div><?php echo(xml_escape($announce->user_name)); ?></div>
							<div><?php echo(xml_escape($announce->user_title)); ?></div>
						</div>
						<?php echo($announce->content); ?>
						<div class="clear"></div>
					</div>
				</div>
				<?php } ?>
			</div>
			<div class="office_page" id="page_article">
				<div class="actions">
					<a href="/office/news/create">
						<img src="/images/version2/office/button_createarticle.png" alt="New Article" />
					</a>
				</div>
				<img src="/images/version2/office/icon_article.png" alt="Your Articles" class="title" />
				<h2>Your Articles</h2>

				<table>
					<thead>
						<tr>
							<th>Title</th>
							<th>Box</th>
							<th>Assignees</th>
							<th>Status</th>
							<th class="right">Deadline</th>
						</tr>
					</thead>
<?php if (empty($my_requests)) { ?>
					<tr>
						<td colspan="5" class="center">
							You do not currently have any articles.
						</td>
					</tr>
<?php } else {
	foreach($my_requests as $request) { ?>
					<tr>
						<td>
							<a href="/office/<?php echo(($request['type'] == 'photo') ? 'photos/view' : 'news'); ?>/<?php echo($request['id']); ?>">
								<img src="/images/prototype/news/<?php echo($request['type']); ?>-small.gif" alt="<?php echo($request['type']); ?> request" title="<?php echo($request['type']); ?> request" />
								<?php echo(xml_escape($request['title'])); ?>
							</a>
						</td>
						<td>
							<?php echo($request['box']); ?>
						</td>
						<td>
<?php	foreach ($request['reporters'] as $reporter) { ?>
							<img src="/images/prototype/news/person.gif" alt="Assignee" title="Assignee" />
							<?php echo(xml_escape($reporter['name'])); ?><br />
<?php	} ?>
						</td>
						<td>
<?php	foreach ($request['reporters'] as $reporter) { ?>
							<img src="/images/prototype/news/<?php echo($reporter['status']); ?>.gif" alt="<?php echo($reporter['status']); ?>" title="<?php echo($reporter['status']); ?>" />
							<?php echo($reporter['status']); ?><br />
<?php	} ?>
						</td>
						<td class="right"<?php if (mktime() > $request['deadline']) echo(' style="color:red"'); ?>>
							<?php echo(date('d/m/y @ H:i', $request['deadline'])); ?>
						</td>
					</tr>
<?php
	}
} ?>
				</table>
			</div>
			<div class="office_page" id="page_photos"><br /><br /><b>PHOTOS</b></div>
			<div class="office_page" id="page_bylines"><br /><br /><b>BYLINES</b></div>
			<div class="office_page" id="page_related"><br /><br /><b>RELATED</b></div>
			<div class="office_page" id="page_comments"><br /><br /><b>COMMENTS</b></div>
			<div class="office_page" id="page_publish"><br /><br /><b>PUBLISH</b></div>
		</div>
		<div class="clear"></div>
	</div>
</div>

*/
?>
