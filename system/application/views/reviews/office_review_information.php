<?php

// base reviews/organisation/context/information url
$reviews_information_url = site_url('office/reviews/'.$organisation['shortname'].'/'.$context_type.'/information/');

?>

<div class='RightToolbar'>
	<h4>Page Information</h4>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
	<h4>Revisions</h4>
	<div class="Entry">
		<ol>
		<?php
		$newer_that_published = true;
		$new_revisions_present = false;
		foreach($revisions as $the_revision) {
			echo('<li>');
			if ($the_revision['deleted']) {
				echo('<span class="red">');
			}
			echo('Author : '.xml_escape($the_revision['author']).'<br />');
			echo('Created : '.date('d/m/Y H:i', (int)$the_revision['timestamp']).'<br />');
			if ($main_revision['content_id'] == $the_revision['id']) {
				echo(' <b>(Editing)</b>');
			} else {
				echo(' <a href="'.$reviews_information_url.'/view');
				if ($the_revision['deleted']) {
					echo('all');
				}
				echo('/'.$the_revision['id'].'">Edit</a>');
			}
			echo(' <a href="'.$reviews_information_url.'/preview/'.$the_revision['id'].'">Preview');
			if (!$the_revision['published'] && $user_is_editor) {
				echo(' &amp; Publish');
			}
			echo('</a>');
			if ($the_revision['published']==true){
				echo(' <span class="orange">(Published)</span>');
				$newer_that_published=false;//Any futher revisions are older than this.
			} else {
				if($the_revision['deleted']==false && $newer_that_published){
					//not published and not deleted and newer than current revision.
					$new_revisions_present=true;
				}
			}
			if ($the_revision['deleted']) {
				echo('</span>');
			}
			echo('</li>');
		}?>
		</ol>
		<?php
		if ($show_show_all_revisions_option){
			if ($show_all_revisions){
				echo('<p><a href="'.$reviews_information_url.'/view">Hide deleted revisions</a></p>');
			}else{
				echo('<p><a href="'.$reviews_information_url.'/viewall">Include deleted revisions.</a></p>');
			}
		}
		?>
	</div>
	<h4>Areas for Attention</h4>
	<div class="Entry">
<?php
if(!empty($reviews)){
	echo('		<div class="information_box">'."\n");
	echo('			The following reviews are waiting to be published:'."\n");
	echo('			<ul>'."\n");
	foreach ($reviews as $review) {
		//print_r($review);
		if (!empty($review['writers'][0]) && empty($review['article']['live_content'])){
			echo('				<li><a href="/office/reviews/'.$organisation['shortname'].'/'.$context_type.'/review');
			echo('">'.xml_escape($review['writers'][0]['name']).' '.xml_escape($review['article']['created']).'</a></li>'."\n");
		}
	}
	echo('			</ul>'."\n");
	echo('		</div>'."\n");
}
if($new_revisions_present){
	echo('		<div class="information_box">'."\n");
	echo('			<img src="/images/prototype/homepage/warning.png" />'."\n");
	echo('			New revision(s) have been created and are waiting to be published or deleted.'."\n");
	echo('		</div>'."\n");
}
?>
	</div>
</div>

<div id="MainColumn">
	<form id='reviewinfo' name='reviewinfo' action='/office/reviews/<?php echo($organisation['shortname']); ?>/<?php echo($context_type); ?>' method='POST' class='form'>
	<div class='blue_box'>
		<h2>objective blurb</h2>
		You are currently editing <span class="orange"><?php echo(xml_escape($organisation['name'])); ?></span><br />
		<fieldset>
			<textarea name='reviewinfo_about' cols='48' rows='10'><?php echo(xml_escape($main_revision['content_blurb'])); ?></textarea>
		</fieldset>
	</div>
	<div class='grey_box'>
	<h2>details</h2>
		<fieldset>
			<label for='reviewinfo_rating'>Rating:</label>
			<select name="reviewinfo_rating">
			<?php
			for ($rating = 0; $rating <= 10; $rating++)
			{
	    		echo('<option');
	    		if ($rating == $main_revision['content_rating']) {
	    			echo(' selected');
				}
	    		echo(' value="'.$rating.'">'.($rating / 2).'</option>');
			}
			?>
			</select>
			<br />
			<label for='reviewinfo_quote'>Summary Quote:</label>
			<textarea name='reviewinfo_quote' cols='25' rows='4'><?php echo(xml_escape($main_revision['content_quote'])); ?></textarea>
			<br />
			<label for='reviewinfo_recommended'>Recommended Item:</label>
			<input type='text' name='reviewinfo_recommended' style='width: 220px;' value='<?php echo(xml_escape($main_revision['recommended_item'])); ?>'/>
			<br />
			<label for='reviewinfo_average_price'>Average Price:</label>
			<input type='text' name='reviewinfo_average_price' style='width: 220px;' value='<?php echo(xml_escape($main_revision['average_price'])); ?>'/>
			<br />
			<label for='reviewinfo_serving_hours'>Serving Hours:</label>
			<textarea name='reviewinfo_serving_hours' cols='25' rows='4'><?php echo(xml_escape($main_revision['serving_times'])); ?></textarea>
			<br />
			
			<label for='reviewinfo_submitbutton'></label>
			<input type='submit' name='submitbutton' value='Create new revision' class='button' />
		</fieldset>
	</div>
	<a href="/office/reviewlist/<?php echo($context_type); ?>">Back to the attention list</a>
</div>