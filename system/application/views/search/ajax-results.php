<?php
define("ADDR_YORKIPEDIA", "http://yorkipedia.theyorker.co.uk/index.php?title=");
?>
<p class="note"><a href="javascript:search_noShow(0)">toggle</a></a></p>
<h1>Articles</h1>
<ul id="ajax-articles">
	<?php if (isset($articles) and $articles->num_rows() > 0) { foreach($articles->result() as $link) {
		echo '<li><a href="/news/'.$link->category.'/'.$link->id.'">'.xml_escape($link->title).'</a></li>';
	}} else {
		echo '<li>None Found</li>';
	}?>
</ul>
<p class="note"><a href="javascript:search_noShow(1)">toggle</a></p>
<h1>Directory</h1>
<ul id="ajax-dir">
	<?php if (isset($directory) and $directory->num_rows() > 0) { foreach($directory->result() as $entry) {
		echo '<li><a href="/directory/'.$entry->link.'">'.xml_escape($entry->title).'</a></li>';
	}} else {
		echo '<li>None Found</li>';
	}?></ul>
<p class="note"><a href="javascript:search_noShow(2)">toggle</a></p>
<h1>Events</h1>
<?php if(!$this->user_auth->isLoggedIn) echo '<p class="extraLogin">Login to see your own events here</p>';?>
<ul id="ajax-events">
	<?php if (isset($events) & count($events)>0) { foreach ($events as $event) {
		echo '<li><a href="/calendar/event/'.$event['EventId'].'/'.$event['SourceEventId'].'/">'.xml_escape($event['Name']).'</a></li>';
	}} else {
		echo '<li>No results found</li>';
	}?>
</ul>
<p class="note"><a href="javascript:search_noShow(3)">toggle</a></p>
<h1>Yorkipedia</h1>
<ul id="ajax-york">
	<?php if (isset($wiki)) { foreach($wiki as $page) {
		echo '<li><a href="'.ADDR_YORKIPEDIA.urlencode($page).'">'.xml_escape($page).'</a></li>';
	}} else {
		echo '<li>No results found</li>';
	}?>
</ul>