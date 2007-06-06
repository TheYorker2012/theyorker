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
		echo('				<a href="/contact">'.$reporter['name'].'</a>'."\n");
	echo('			</div>'."\n");
	echo('		</div>'."\n");
	echo('		<p>'.$article['blurb'].'</p>'."\n");
	echo('	</div>'."\n");
}
?>

<div id="RightColumn">
	<h2 class="first">What is this?</h2>
	The news archive contains a list of every single article published on The Yorker in chronological order.
	<br /><br />
	In the near future you will be able to provide criterion to search this list by, so please check back soon.
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>news archive</h2>

		<?php echo($this->pagination->create_links()); ?>
		<div>Viewing <?php echo(($offset + 1) . ' - ' . ($offset + 10) . ' of ' . $total . ' articles'); ?></div>
		<div style="border-bottom:1px #999 solid;clear:both"></div>

<?php
		foreach($articles as $article) {
			printarticlelink($article);
		}
?>

		<?php echo($this->pagination->create_links()); ?>
		<div>Viewing <?php echo(($offset + 1) . ' - ' . ($offset + 10) . ' of ' . $total . ' articles'); ?></div>
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