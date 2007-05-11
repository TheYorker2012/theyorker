<?php
	function get_link_ref($article,$prefix){
		return 'href="/'.$prefix.'/'.$article['article_type'].'/'.$article['id'].'"';
	};

	function print_box($primary_article,$secondary_article,$tertiary_article,$heading,$prefix){
		echo('  <h2>'.$heading.'</h2>'."\n"); 
		echo('  <div class="NewsBox">'."\n"); 
		echo('          <a class="NewsImg"'.get_link_ref($primary_article,$prefix).'>'."\n");
		echo('                  '.$primary_article['photo_xhtml']."\n").'';
		echo('          </a>'."\n");
		echo('          <h3 class="Headline"><a '.get_link_ref($primary_article,$prefix).'>'.$primary_article['heading'].'</a></h3>'."\n");
		echo('          <div class="Date">'.$primary_article['date'].'</div>'."\n");
		echo('		<p class="More">'.$primary_article['blurb'].'</p>'."\n");
		echo('          <ul class="TitleList">'."\n");
		echo('                  <li><a '.get_link_ref($secondary_article,$prefix).'>'.$secondary_article['heading'].'</a></li>'."\n");
		echo('                  <li><a '.get_link_ref($tertiary_article,$prefix).'>'.$tertiary_article['heading'].'</a></li>'."\n");
		echo('          </ul>'."\n");
		echo('  </div>'."\n");
	};

	
	function print_middle_box($title,$article_array){
		echo('  <h4>'.$title.'</h4>'."\n");
		echo('  <ul class="TitleList">'."\n");
		foreach ($article_array as $article) {
			echo('          <li><a href="/news/'.$article['article_type'].'/'.$article['id'].'" >'."\n");
			echo('                  '.$article['heading']."\n");
			echo('          </a></li>'."\n");
		}
		echo('  </ul>'."\n");
	};
?>

<div id="RightColumn">
	<h2 class="first">My Links</h2>
	<div class="Entry">
<?php 	if ($link->num_rows() > 0) 
	{ 
	foreach($link->result() as $picture){
		echo('	<a href="'.$picture->link_url.'">'.imageLocTag($picture->link_image_id, 'link', false, $picture->link_name, null, $picture->image_file_extension, null, 'title = "'.$picture->link_name.'"').'</a>'."\n");
		} 
	} else { 
		echo('	<a href="http://theyorker.co.uk">You have no links :(</a>'."\n");
	}
?>
		<a class="RightColumnAction"  href="/account/links">Customise</a>
	</div>

	<h2> My Webmail </h2>
	<a class="MailLogo" href="https://webmail.york.ac.uk">
		<img src="/images/prototype/news/test/webmail_large.jpg" alt="Webmail Logo" />
	</a>
	<div class="Entry">
			<p class="MailText">
				<a href="https://webmail.york.ac.uk/">E-mail</a>
			</p>
	</div>

	<h2>Upcoming Events</h2>
	<div class="Entry">
		<?php $events->Load(); ?>
	</div>

	<h2>To Do</h2>
	<div class="Entry">
		<?php $todo->Load(); ?>
	</div>

	<h2>York Weather</h2>
	<div class="Entry">
		<?php echo($weather_forecast);?>
	</div>

	<h2>Quote of the Day</h2>
	<div class="Entry">
		"<?php echo $quote->quote_text;?>" - <b><?php echo $quote->quote_author;?></b>
	</div>
</div>

<div id="MainColumn">
	<div id="HomeBanner">
		<?php echo($banner) ?>
	</div>

	<div class="BlueBox">
		<?php print_box($primary_article,$secondary_article,$tertiary_article,'latest news','news') ?>
	</div>

	<div class="BlueBox">
		<h2><?php echo('and today...')?></h2>
		<div class="LeftNewsBox NewsBox">
			<?php print_middle_box('IN FEATURES',$features) ?>
		</div>
		<div class="RightNewsBox NewsBox">
			<?php print_middle_box('IN ARTS',$arts) ?>
		</div>
	</div>

	<div class="BlueBox">
		<?php print_box($primary_sports,$secondary_sports,$tertiary_sports,'latest sport','news/sport') ?>
	</div>
</div>
