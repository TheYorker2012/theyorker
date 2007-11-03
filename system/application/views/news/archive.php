<?php
function printarticlelink($article) {
	echo('	<div style="border-bottom:1px #999 solid;">'."\n");
	echo('		<a href="/news/'.$article['type_codename'].'/'.$article['id'].'">'."\n");
	echo('			'.$article['photo_xhtml']."\n");
	echo('		</a>'."\n");
	echo('		<div class="ArticleEntry">'."\n");
	echo('			<h3 class="Headline">'."\n");
	echo('				<a href="/news/'.$article['type_codename'].'/'.$article['id'].'">'."\n");
	echo('					'.$article['heading']."\n");
	echo('				</a>'."\n");
	echo('			</h3>'."\n");
	echo('			<div class="Section" style="float:right;">'.$article['type_name'].'</div>'."\n");
	echo('			<div class="Date">'.date('D, jS F Y',$article['date']).'</div>'."\n");
	echo('			<div class="Author">'."\n");
	foreach($article['reporters'] as $reporter)
		echo('				<a href="/news/archive/reporter/'.$reporter['id'].'/">'.$reporter['name'].'</a>'."\n");
	echo('			</div>'."\n");
	echo('		</div>'."\n");
	echo('		<p>'.$article['blurb'].'</p>'."\n");
	echo('	</div>'."\n");
}
?>

<div id="RightColumn">
	<h2 class="first">What is this?</h2>
	The news archive contains a list of every single article published on The Yorker in chronological order.

	<h2>Search</h2>
	<form action="/news/archive/" method="post">
		<label for="archive_section">Section:</label>
		<select name="archive_section" id="archive_section" size="1">
			<option value="all">All</option>
<?php foreach ($sections as $section) { ?>
			<option value="<?php echo($section['id']); ?>"<?php
	if ((isset($filters['section'])) && ($filters['section'] == $section['id'])) {
		echo(' selected="selected"');
	} ?>><?php echo($section['name']); ?></option>
<?php } ?>
		</select>

		<label for="archive_reporter">Reporter:</label>
		<select name="archive_reporter" id="archive_reporter" size="1">
			<option value="all">All</option>
<?php foreach ($reporters as $reporter) { ?>
			<option value="<?php echo($reporter['id']); ?>"<?php
	if ((isset($filters['reporter'])) && ($filters['reporter'] == $reporter['id'])) {
		echo(' selected="selected"');
	} ?>><?php echo($reporter['name']); ?></option>
<?php } ?>
		</select>

        <br style="clear:both" />
        <br style="clear:both" />
		<input type="submit" name="archive_search" id="archive_search" value="Find Articles" />
	</form>
</div>

<div id="MainColumn">
	<?php if (isset($byline_info)) $this->load->view('/office/bylines/byline', $byline_info); ?>

	<div class="BlueBox">
		<h2>news archive</h2>

		<?php echo($this->pagination->create_links()); ?>
		<div>Viewing <?php echo((($total == 0) ? '0' : ($offset + 1)) . ' - ' . ((($offset + 10) <= $total) ? ($offset + 10) : $total) . ' of ' . $total . ' articles'); ?></div>
		<div style="border-bottom:1px #999 solid;clear:both"></div>

<?php
		foreach($articles as $article) {
			printarticlelink($article);
		}
?>

		<?php echo($this->pagination->create_links()); ?>
		<div>Viewing <?php echo((($total == 0) ? '0' : ($offset + 1)) . ' - ' . ((($offset + 10) <= $total) ? ($offset + 10) : $total) . ' of ' . $total . ' articles'); ?></div>
		<div style="clear:both"></div>
	</div>
</div>



<!--
	<div class="BlueBox">
		<h2>search archive</h2>
		<form id="archive_search" action="news/archive" method="post">
			<fieldset>
				<label for="a_category">Category:</label>
					<select name="a_category" id="a_category" size="1">
						<option value="" selected="selected">&nbsp;</option>
						<option value="Campus News">Campus News</option>
						<option value="Features">Features</option>
						<option value="Lifestyle">Lifestyle</option>
					</select><br />
	
				<label for="a_reporter">Reporter:</label>
					<select name="a_reporter" id="a_reporter" size="1">
						<option value="" selected="selected">&nbsp;</option>
						<option value="Dan Ashby">Dan Ashby</option>
						<option value="Nick Evans">Nick Evans</option>
						<option value="Chris Travis">Chris Travis</option>
						<option value="John Doe">John Doe</option>
						<option value="Jane Doe">Jane Doe</option>
						<option value="Alan Smith">Alan Smith</option>
						<option value="Danielle Gerrard">Danielle Gerrard</option>
					</select><br />
	
				<label for="a_subject">Subject:</label>
					<select name="a_subject" id="a_subject" size="1">
						<option value="" selected="selected">&nbsp;</option>
						<option value="Event">Event</option>
						<option value="Christmas">Christmas</option>
						<option value="Easter">Easter</option>
						<option value="Police">Police</option>
						<option value="Vanbrugh">Vanbrugh</option>
						<option value="Football">Football</option>
					</select><br />
	
				<label for="a_text">Text:</label>
					<input type="text" name="a_text" id="a_text" value="" /><br />
			</fieldset>
			<fieldset>
				<input type="submit" name="r_submit" value="Search" class="button" />
			</fieldset>
		</form>
	</div>
-->