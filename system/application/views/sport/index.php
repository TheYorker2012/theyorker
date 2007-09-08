<?php
/*
function get_link_ref($article,$prefix){
	return 'href="/'.$prefix.'/'.$article['article_type'].'/'.$article['id'].'"';
};

//@heading -- Title for the blue box
//@input articles -- List of simple articles, with the first item being a summery article
function print_box_with_picture_list($articles,$heading){
	if (count($articles) > 0)
	{
		//print main article
		echo('<div class="BlueBox">'."\n");
		echo('	<h2>'.$heading.'</h2>'."\n");
		echo('	<div class="NewsBox">'."\n");
		echo('		<a class="NewsImg"'.get_link_ref($articles[0],'news').'>'."\n");
		echo('			'.$articles[0]['photo_xhtml']."\n");
		echo('		</a>'."\n");
		echo('		<h3 class="Headline"><a '.get_link_ref($articles[0],'news').'>'.$articles[0]['heading'].'</a></h3>'."\n");
		echo('		<div class="Date">'.$articles[0]['date'].'</div>'."\n");
		echo('		<p class="More">'.$articles[0]['blurb'].'</p>'."\n");
		if (count($articles) > 1){echo('		<div class="LineContainer NewsBox"></div>'."\n");}
		echo('	</div>'."\n");
		//loop printing the rest as small articles.
		$index = 0;
		$lr_array = array("Left","Right");
		$articles = array_slice($articles,1);//remove the first article from the array
		foreach($articles as $article){
			echo('	<div class="'.$lr_array[$index % 2].'NewsBox NewsBox">'."\n");
			echo('		<a class="NewsImgSmall" '.get_link_ref($articles[$index],'news').'>'."\n");
			echo('			'.$articles[$index]['photo_xhtml']."\n");
			echo('		</a>'."\n");
			echo('		<p class="More">'."\n");
			echo('			<a '.get_link_ref($articles[$index],'news').'>'.$articles[$index]['heading'].'</a>'."\n");
			echo('		</p>'."\n");
			echo('	</div>'."\n");
			$index++;
		}
		echo('</div>'."\n");
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

//@input $box_header --title for the box
//@input $article_types -- array of article types eg. from $this->News_model->getSubArticleTypes()
//@input $article_lists -- array of lists of simple articles to print out.
function print_box_of_category_lists($box_header,$article_types,$article_lists)
{
	echo('<div class="BlueBox">'."\n");
	echo('<h2>'.$box_header.'</h2>'."\n");
		$index = 0;
		$lrindex = 0;
		$lr_array = array("Left","Right");
		foreach($article_types as $article_type)
		{	
			if(!empty($article_lists[$index])){
				echo ('<div class="'.$lr_array[$lrindex % 2].'NewsBox NewsBox">'."\n");
				print_middle_box($article_type['name'],$article_lists[$index]);
				echo ('</div>'."\n");
				$lrindex++;
			}
			$index++;
		}
	echo('</div>');
}
*/
?>

<div id="RightColumn">
	<h2 class="first">
		<?php echo $links_heading; ?>
	</h2>
	<div class="Entry">
		Links
	</div>
</div>
<div id="MainColumn">
	<div id="HomeBanner">
		<?php echo($banner) ?>
	</div>
	<?php
	$this->homepage_boxes->print_box_with_picture_list($main_sport,$latest_heading,'news');
	$this->homepage_boxes->print_box_of_category_lists($more_heading,$show_sports,$sport_lists);
	?>
</div>