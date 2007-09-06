<?php
//This get link ref is special for the sport section.
function get_link_ref($article,$prefix){
	return 'href="/news/'.$prefix.'/'.$article['id'].'"';
};

function print_box_with_picture_list($articles,$heading){
	if (count($articles) != 0) {
		echo('  <h2>'.$heading.'</h2>'."\n");
		echo('  <div class="NewsBox">'."\n");
		echo('          <a class="NewsImg"'.get_link_ref($articles[0],$articles[0]['article_type']).'>'."\n");
		echo('                  '.$articles[0]['photo_xhtml']."\n");
		echo('          </a>'."\n");
		echo('          <h3 class="Headline"><a '.get_link_ref($articles[0],$articles[0]['article_type']).'>'.$articles[0]['heading'].'</a></h3>'."\n");
		echo('          <div class="Date">'.$articles[0]['date'].'</div>'."\n");
		echo('		<p class="More">'.$articles[0]['blurb'].'</p>'."\n");
		if (count($articles) > 1) {
			echo('<div class="LineContainer NewsBox"></div>'."\n");
			echo('</div>'."\n");
			echo('<div class="LeftNewsBox NewsBox">'."\n");
			echo('<a class="NewsImgSmall" '.get_link_ref($articles[1],$articles[1]['article_type']).'>'."\n");
			echo('<img src="http://www.theyorker.co.uk/photos/small/332">'."\n");
			echo('</a>'."\n");
			echo('<p class="More">'."\n");
			echo('<a '.get_link_ref($articles[1],$articles[1]['article_type']).'>'.$articles[1]['heading'].'</a>'."\n");
			echo('</p>'."\n");
			echo('</div>'."\n");
			if (count($articles) > 2)
			{
				echo('<div class="RightNewsBox NewsBox">'."\n");
				echo('<a class="NewsImgSmall" '.get_link_ref($articles[2],$articles[2]['article_type']).'>'."\n");
				echo('<img src="http://www.theyorker.co.uk/photos/small/332">'."\n");
				echo('</a>'."\n");
				echo('<p class="More">'."\n");
				echo('	<a '.get_link_ref($articles[2],$articles[2]['article_type']).'>'.$articles[2]['heading'].'</a>'."\n");
				echo('</p>'."\n");
				echo('</div>'."\n");
			}
		} else {
			echo('</div>'."\n");
		}

	}
};

function print_middle_box($title,$article_array){
	echo('  <h4>'.$title.'</h4>'."\n");
	if (count($article_array) > 0) {
		echo('  <ul class="TitleList">'."\n");
		foreach ($article_array as $article) {
			echo('          <li><a href="/news/'.$article['article_type'].'/'.$article['id'].'" >'."\n");
			echo('                  '.$article['heading']."\n");
			echo('          </a></li>'."\n");
		}
		echo('  </ul>'."\n");
	}
};
?>

<div id="RightColumn">
	<h2 class="first"><?php echo $links_heading; ?></h2>
	<div class="Entry">
		Links
	</div>
</div>

<div id="MainColumn">
	<div id="HomeBanner">
		<?php echo($banner) ?>
	</div>
	<div class="BlueBox">
		<?php if(!empty($main_sport)){print_box_with_picture_list($main_sport,$latest_heading);} ?>
	</div>
	<div class="BlueBox">
		<h2><?php echo$more_heading;?></h2>
		<?php
			$index = 0;
			$lrindex = 0;
			$lr_array = array("Left","Right");
			foreach($show_sports as $sport)
			{	
				if(!empty($sports[$index])){
					echo ('<div class="'.$lr_array[$lrindex % 2].'NewsBox NewsBox">'."\n");
					print_middle_box($sport['name'],$sports[$index]);
					echo ('</div>'."\n");
					$lrindex++;
				}
				$index++;
			}
		?>
	</div>
</div>