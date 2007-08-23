<?php

// base reviews/organisation/context/information url
$reviews_information_url = site_url('office/reviews/'.$organisation['shortname'].'/'.$context_type.'/information/');

?>

<div class='RightToolbar'>
	<h4>Areas for Attention</h4>
	<div class="Entry">
		<div class="information_box">
			The following reviews are waiting to be published:
			<ul>
			<?php
			$new = false;
			foreach ($revisions as $the_revision) {
    			if ($new) {
					echo('<li><a href="'.$reviews_information_url.'/view');
					if ($the_revision['deleted']) {
						echo('all');
					}
					echo('/'.$the_revision['id'].'">'.$the_revision['author'].' '.date('d/m/Y', $the_revision['timestamp']).'</a></li>');
				} elseif ($the_revision['published']) {
					$new = true;
				}
			}
			?>
			</ul>
		</div>
		<div class="information_box">
			<img src="/images/prototype/homepage/warning.png" />
			<a href='#'>Information</a> has been updated and is waiting to be published.
		</div>
		<div class="information_box">
			There are <a href='#'>Comments</a> that have been reported for abuse.
		</div>
	</div>
	<h4>Revisions</h4>
	<div class="Entry">
		<ol>
		<?php
		// $revision is already in use and seems to conflict
		foreach($revisions as $the_revision) {
			echo('<li>');
			if ($the_revision['deleted']){echo('<span class="red">');}
			echo('Author : '.$the_revision['author'].'<br />');
			echo('Created : '.date('d/m/Y H:i', (int)$the_revision['timestamp']).'<br />');
			if ($content_id == $the_revision['id']) {
				echo(' <b>(Editing)</b>');
			} else {
				echo(' <a href="'.$reviews_information_url.'/view');
				if ($the_revision['deleted']){echo('all');}
				echo('/'.$the_revision['id'].'">Edit</a>');
			}
			echo(' <a href="'.$reviews_information_url.'/preview/'.$the_revision['id'].'">Preview');
			if (!$the_revision['published'] && $user_is_editor) {
				echo(' &amp; Publish');
			}
			echo('</a>');
			if ($the_revision['published']==true){
				echo(' <span class="orange">(Published)</span>');
			}
			if ($the_revision['deleted']){echo('</span>');}
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
		/// @todo revisions information text
		//echo($revisions_information_text);
		?>
	</div>

<h4>What's this?</h4>
	<p>
		<?php echo('whats_this'); ?>
	</p>
<h4>Other tasks</h4>
<ul>
	<li><a href='#'>Maintain my account</a></li>
	<li><a href='#'>Remove this directory entry</a></li>
</ul>
</div>

<form id='reviewinfo' name='reviewinfo' action='/office/reviews/<?php echo($organisation['shortname']); ?>/<?php echo($context_type); ?>' method='POST' class='form'>
<div class='blue_box'>
	<h2>objective blurb</h2>
	You are currently editing <span class="orange"><?php echo($organisation['name']); ?></span><br />
	<textarea name='reviewinfo_about' cols='48' rows='10'><?php echo($content_blurb); ?></textarea>
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
    		if ($rating == $content_rating) echo(' selected');
    		echo(' value="'.$rating.'">'.($rating / 2).'</option>');
		}
		?>
		</select>
		<br />
		<label for='reviewinfo_quote'>Summary Quote:</label>
		<textarea name='reviewinfo_quote' cols='25' rows='4'><?php echo($content_quote); ?></textarea>
		<br />
		<label for='reviewinfo_recommended'>Recommended Item:</label>
		<input type='text' name='reviewinfo_recommended' style='width: 220px;' value='<?php echo($recommended_item); ?>'/>
		<br />
		<label for='reviewinfo_average_price'>Average Price:</label>
		<input type='text' name='reviewinfo_average_price' style='width: 220px;' value='<?php echo($average_price); ?>'/>
		<br />
		<label for='reviewinfo_serving_hours'>Serving Hours:</label>
		<textarea name='reviewinfo_serving_hours' cols='25' rows='4'><?php echo($serving_times); ?></textarea>
		<br />
		
		<label for='reviewinfo_submitbutton'></label>
		<input type='submit' name='submitbutton' value='Create new revision' class='button' />
	</fieldset>
</div>


<?php

echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');

?>