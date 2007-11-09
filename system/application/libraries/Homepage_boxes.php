<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file homepage_boxes.php
 * @brief Library for making various blue boxes with article links and summaries for homepages.
 * @author Owen Jones (oj502@york.ac.uk)
 */
 
class Homepage_boxes
{
	//Creates article link
	//@input $article -- article containing at least a type and an id
	//@input $prefix -- section to go into, usually 'news' DO NOT LEAVE BLANK! (double slash causes refrence to webroute)
	function get_link_ref($article,$prefix){
		return 'href="/'.$prefix.'/'.$article['article_type'].'/'.$article['id'].'"';
	}

	//Creates Article summary box with a double column list of articles below it. Small images used in list
	//@input $heading -- Title for the blue box
	//@input $articles -- List of simple articles, with the first item being a summery article
	//@input $prefix -- section to go into, usually 'news' DO NOT LEAVE BLANK! (double slash causes refrence to webroute)
	function print_box_with_picture_list($articles,$heading,$prefix){
		if (count($articles) > 0)
		{
			//print main article
			echo('<div class="BlueBox">'."\n");
			echo('	<h2>'.$heading.'</h2>'."\n");
			echo('	<div class="NewsBox">'."\n");
			echo('		<a class="NewsImg"'.$this->get_link_ref($articles[0],$prefix).'>'."\n");
			echo('			'.$articles[0]['photo_xhtml']."\n");
			echo('		</a>'."\n");
			echo('		<h3 class="Headline"><a '.$this->get_link_ref($articles[0],$prefix).'>'.$articles[0]['heading'].'</a></h3>'."\n");
			echo('		<div class="Date">'.$articles[0]['date'].'</div>'."\n");
			echo('		<p class="More">'.$articles[0]['blurb'].'</p>'."\n");
			if (count($articles) > 1){echo('		<div class="LineContainer"></div>'."\n");}
			echo('	</div>'."\n");
			//loop printing the rest as small articles.
			$index = 0;
			$lr_array = array("Left","Right");
			$articles = array_slice($articles,1);//remove the first article from the array
			foreach($articles as $article){
				echo('	<div class="'.$lr_array[$index % 2].'NewsBox NewsBox">'."\n");
				echo('		<a class="NewsImgSmall" '.$this->get_link_ref($articles[$index],$prefix).'>'."\n");
				echo('			'.$articles[$index]['photo_xhtml']."\n");
				echo('		</a>'."\n");
				echo('		<p class="More">'."\n");
				echo('			<a '.$this->get_link_ref($articles[$index],$prefix).'>'.$articles[$index]['heading'].'</a>'."\n");
				echo('		</p>'."\n");
				echo('	</div>'."\n");
				$index++;
			}
			echo('</div>'."\n");
		}
	}
	
	//Creates Article summary box with a single list of articles below it. Small images used in list
	//@input $heading -- Title for the blue box
	//@input $articles -- List of simple articles, with the first item being a summery article
	//@input $prefix -- section to go into, usually 'news' DO NOT LEAVE BLANK! (double slash causes refrence to webroute)
	function print_box_with_text_list($articles,$heading,$prefix){
		if (count($articles) > 0)
		{
			//print main article
			echo('<div class="BlueBox">'."\n");
			echo('	<h2>'.$heading.'</h2>'."\n");
			echo('	<div class="NewsBox">'."\n");
			echo('		<a class="NewsImg"'.$this->get_link_ref($articles[0],$prefix).'>'."\n");
			echo('			'.$articles[0]['photo_xhtml']."\n");
			echo('		</a>'."\n");
			echo('		<h3 class="Headline"><a '.$this->get_link_ref($articles[0],$prefix).'>'.$articles[0]['heading'].'</a></h3>'."\n");
			echo('		<div class="Date">'.$articles[0]['date'].'</div>'."\n");
			echo('		<p class="More">'.$articles[0]['blurb'].'</p>'."\n");
			if (count($articles) > 1){
				echo('		<div class="LineContainer"></div>'."\n");
				echo('      <ul class="TitleList">'."\n");
			}
			echo('	</div>'."\n");
			//loop printing the rest as small articles.
			$index = 0;
			$lr_array = array("Left","Right");
			$articles = array_slice($articles,1);//remove the first article from the array
			foreach($articles as $article){
				echo('			<li><a '.$this->get_link_ref($articles[$index],$prefix).'>'.$articles[$index]['heading'].'</a></li>'."\n");
				$index++;
			}
			if (count($articles) > 1){echo('		</ul>'."\n");}
			echo('</div>'."\n");
		}
	}

	//Creates a boxed list of article names with a title
	//@input $title -- String title for the list
	//@input $article_array -- List of articles
	function print_list_box($title,$article_array){
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
	}

	//Creates a double columned box of article lists.
	//@input $box_header --title for the box
	//@input $article_types -- array of article types eg. from $this->News_model->getSubArticleTypes()
	//@input $article_lists -- array of lists of simple articles to print out.
	//@note the title for each list comes from the article types name.
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
					$this->print_list_box($article_type['name'],$article_lists[$index]);
					echo ('</div>'."\n");
					$lrindex++;
				}
				$index++;
			}
		echo('</div>');
	}
	
	//Prints a middle sized puffer box for featured articles/specials
	//Make sure you use summery article, to get the article_type_name and the subheading!!
	function print_puffer_box($title, $article){
		echo('<div class="BlueBox PufferBox">'."\n");
		echo('	<a class="PufferImg" href="/news/'.$article['article_type'].'/'.$article['id'].'">'."\n");
		echo('		'.$article['photo_xhtml']."\n");
		echo('	</a>'."\n");
		echo('	<h2>'.$title.'</h2>'."\n");
		echo('	<p class="More">'."\n");
		echo('		<a href="/news/'.$article['article_type'].'/'.$article['id'].'">'.$article['heading'].'</a>'."\n");
		echo('	</p>'."\n");
		echo('</div>'."\n");
	}
	
	//Prints a column of puffers for a right hand side bar.
	//Each puffer must contain 'image' that is the url of where the image is, this has to be made in the controller!
	function print_puffer_column ($puffers){
		foreach ($puffers as $puffer) {
			if(!empty($puffer['image_title'])){
				echo '<div class=\'Puffer\'>';
				echo '<a href=\'/news/' . $puffer['codename'] . '\'>';
				echo '<img src=\'' . $puffer['image'] . '\' alt=\'' . $puffer['image_title'] . '\' title=\'' . $puffer['image_title'] . '\' />';
				echo '</a></div>';
			}else{
				echo '<div class=\'Puffer\'>';
				echo '<a href=\'/news/' . $puffer['codename'] . '\'>';
				echo $puffer['name'];
				echo '</a></div>';
			}
		}
	}
	
	function print_homepage_banner($banner){
		if (isset($banner['image']) && $banner['link']) {
			if ($banner['link'] != "") {
				echo('<a href="'.$banner['link'].'">'.$banner['image'].'</a>'."\n");
			}
			else {
				echo($banner['image']."\n");
			}
		}
	}
}
?>
