<?php
define("ADDR_YORKIPEDIA", "http://yorkipedia.theyorker.co.uk/index.php?title=");
?>
	<?php if (isset($articles) and $articles->num_rows() > 0) { foreach($articles->result() as $link) {
		echo '<li><a href="/news/'.$link->category.'/'.$link->id.'">'.$link->title.'</a></li>';
	}} else {
		echo '<li>None Found</li>';
	}?>
	<?php if (isset($directory) and $directory->num_rows() > 0) { foreach($directory->result() as $entry) {
		echo '<li><a href="/directory/'.$entry->link.'">'.$entry->title.'</a></li>';
	}} else {
		echo '<li>None Found</li>';
	}?></ul>
	<?php if (isset($events) & count($events)>0) { foreach ($events as $event) {
		echo '<li><a href="/calendar/event/'.$event['EventId'].'/'.$event['SourceEventId'].'/">'.$event['Name'].'</a></li>';
	}} else {
		echo '<li>No results found</li>';
	}?>
	<?php if (isset($wiki)) { foreach($wiki as $page) {
		echo '<li><a href="'.ADDR_YORKIPEDIA.$page.'">'.$page.'</a></li>';
	}} else {
		echo '<li>No results found</li>';
	}?>
<div id="RightColumn">
	<h2>Search Options</h2>
	<form>
		<fieldset>
			<p>Sort by Date/Score(radio)</p>
			<p>define("FILTER_ALL", 			16777216);</p>
			<p>define("FILTER_ALL_ARTICLES", 	511);</p>
			<p>define("FILTER_NEWS", 			1);</p>
			<p>define("FILTER_FEATURES", 		2);</p>
			<p>define("FILTER_LIFESTYLE", 		4);</p>
			<p>define("FILTER_FOOD",			8);</p>
			<p>define("FILTER_DRINK", 			16);</p>
			<p>define("FILTER_ARTS",			64);</p>
			<p>define("FILTER_SPORTS",			128);</p>
			<p>define("FILTER_BLOGS",			256);</p>
			<p>define("FILTER_YORK",			512);</p>
			<p>define("FILTER_DIR", 1024);</p>
			<p>define("FILTER_EVENTS",			2048);</p>
		</fieldset>
	</form>
</div>
<div id="MainColumn">
	<div class="BlueBox">
<?php if (isset($results)) {?>
		<h2>Search Results</h2>
		<?php foreach ($results as $type=>$resultGroup) {
			switch ($type) {
				case "articles":
					if (count($resultGroup->result()) == 0) break;
					echo '<h3>Articles</h3><ul>';
					foreach ($resultGroup->result() as $article) {
						echo '<li><a href="/news/'.$article->type_codename.'/'.$article->id.'">'.$article->heading.'</a> - '.$article->blurb.'</li>';
					}
					echo '</ul>'
					break;
				case "wiki":
					if (count($resultGroup) == 0) break;
					echo '<h3>Yorkipedia Pages</h3><ul>';
					foreach ($resultGroup as $page) {
						echo '<li><a href="'.ADDR_YORKIPEDIA.urlencode($page).'">'.$page.'</a></li>'
					}
					echo '</ul>';
					break;
				case "directory":
					if (count($resultGroup->result()) == 0) break;
					echo '<h3>Organisations in the directory</h3><ul>';
					foreach ($resultGroup->result() as $article) {
						echo '<li><a href="/news/'.$article->type_codename.'/'.$article->id.'">'.$article->heading.'</a> - '.$article->blurb.'</li>';
					}
					break;
				case "events":
					if (count($resultGroup->result()) == 0) break;
					echo '<h3>Events</h3>';
					break;
			}
		}
} else { ?>
		<h2>Search</h2>
		<fieldset>
			<label for="search-text">Search for: </label><input type="text" name="search-text" />
			<input type="submit" value="Search" />
		</fieldset>
<?php }?>
	</div>
</div>