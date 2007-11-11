<?php
	function print_comment ($comment) {
		if ($comment['comment_anonymous']) {
			echo('			<li class="anonymous">'."\n");
			echo('				<i>Anonymous</i>'."\n");
		} else {
			echo('			<li>'."\n");
			echo('				<i>' . $comment['user_firstname'] . ' ' . $comment['user_surname'] . '</i>'."\n");
		}
		echo('				on <a href="/news/' . $comment['content_type_codename'] . '/' . $comment['article_id'] . '#CommentItem' . $comment['comment_id'] . '">' . $comment['article_content_heading'] . '</a>'."\n");
		echo('			</li>'."\n");
	}
?>

<div id="RightColumn">
	<h2 class="first">My Links</h2>
	<div class="Entry">
<?php 	if ($link->num_rows() > 0)
	{
	foreach($link->result() as $picture){
		echo('	<a href="'.$picture->link_url.'" target="_blank">'.$this->image->getImage($picture->link_image_id, 'link', array('title' => $picture->link_name, 'alt' => $picture->link_name)).'</a>'."\n");
		}
	} else {
		echo('	<a href="http://theyorker.co.uk">You have no links :(</a>'."\n");
	}
?>
		<a class="RightColumnAction"  href="/account/links">Customise</a>
	</div>

	<h2>Search the Web</h2>
	<div class="Entry">
		<form method="get" action="http://www.google.co.uk/search" target="_blank">
			<input type="hidden" name="ie" value="UTF-8" />
			<input type="hidden" name="oe" value="UTF-8" />
			<a href="http://www.google.co.uk/" target="_blank">
				<img src="http://www.google.co.uk/logos/Logo_40wht.gif" alt="Google" />
			</a>
			<fieldset class="inline">
				<input type="text" name="q" value="" />
				<input type="submit" class="button" name="btnG" value="Search" target="_blank" />
			</fieldset>
		</form>
	</div>

	<h2>My Webmail </h2>
	<div class="Entry">
		<a class="MailLogo" href="https://webmail.york.ac.uk/" target="_blank">
			<img src="/images/prototype/news/test/webmail_large.jpg" alt="Webmail Logo" />
		</a>
		<p class="MailText">
			<a href="https://webmail.york.ac.uk/" target="_blank">E-mail</a>
		</p>
	</div>

	<!--
	<h2>Upcoming Events</h2>
	<div class="Entry">
		<?php $events->Load(); ?>
	</div>

	<h2>To Do</h2>
	<div class="Entry">
		<?php $todo->Load(); ?>
	</div>
	-->

<?php
if ($weather_forecast != null) {
?>
	<h2>York Weather</h2>
	<div class="Entry">
		<?php echo($weather_forecast);?>
	</div>

	<h2>Quote of the Day</h2>
	<div class="Entry">
		"<?php echo $quote->quote_text;?>" - <b><?php echo $quote->quote_author;?></b>
	</div>
<?php
}
?>
</div>

<div id="MainColumn">
	<div id="HomeBanner">
		<?php 
		$this->homepage_boxes->print_homepage_banner($banner);
		?>
	</div>

	<?php 
		$this->homepage_boxes->print_box_with_picture_list($articles['uninews'],'latest news','news');
		if($special['lifestyle']['show']) $this->homepage_boxes->print_specials_box($special['lifestyle']['title'],$special['lifestyle']['data']);
		$this->homepage_boxes->print_box_with_picture_list($articles['features'],'latest features','news');
		$this->homepage_boxes->print_box_with_picture_list($articles['arts'],'latest arts','news');
		if($special['lifestyle']['show']) $this->homepage_boxes->print_specials_box($special['blogs']['title'],$special['blogs']['data']);
		$this->homepage_boxes->print_box_with_picture_list($articles['sport'],'latest sport','news');
		$this->homepage_boxes->print_box_with_picture_list($articles['videocasts'],'latest videocasts','videocasts');
	?>

	<div class="BlueBox">
		<h2>latest comments</h2>
		<ul class="comments">
			<?php foreach ($latest_comments as $comment) print_comment($comment); ?>
		</ul>
	</div>
</div>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>
