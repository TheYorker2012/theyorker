<?php
// base reviews/organisation/context/information url
$reviews_information_url = site_url('office/reviews/'.xml_escape($organisation['shortname']).'/'.xml_escape($context_type).'/information/');
?>
<div class='RightToolbar'>
	<h4>Page Information</h4>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
	<?php 
	if (!empty($revisions)){
	?>
	<h4>Revisions</h4>
	<div class="Entry">
		<ol>
		<?php
		$newer_that_published = true;
		$new_revisions_present = false;
		$deleted_count=0;
		foreach($revisions as $the_revision) {
			echo('<li>');
			if ($the_revision['deleted']) {
				echo('<span class="red">');
				$deleted_count++;
			}
			echo('Author : '.xml_escape($the_revision['author']).'<br />');
			echo('Created : '.date('d/m/Y H:i', (int)$the_revision['timestamp']).'<br />');
			if ($main_revision['content_id'] == $the_revision['id']) {
				echo(' <b>(Editing)</b> |');
			} else {
				echo(' <a href="'.$reviews_information_url.'/view');
				if ($the_revision['deleted']||$show_all_revisions) {
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
				echo(' | <span class="orange">(Live)</span>');
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
		if ($user_is_editor && $show_all_revisions){
			echo ('		<hr>'."\n");
			echo('		<p><a href="'.$reviews_information_url.'/view/'.$main_revision['content_id'].'">Hide deleted revisions</a></p>');
		}
		if ($user_is_editor && !$show_all_revisions && $deleted_count>0){
			echo ('		<hr>'."\n");
			echo('		<p><a href="'.$reviews_information_url.'/viewall/'.$main_revision['content_id'].'">Show deleted revisions ('.$deleted_count.' hidden)</a></p>');
		}
		?>
	</div>
	<?php
	}
	?>
	<h4>Assigned User</h4>
	<div class="Entry">
	<?php 
	if($user_is_editor) {
		if(empty($main_revision['assigned_user_id'])){
			echo $assigned_user_none;
		}else{
			echo '<p>Assigned to <b>'.xml_escape($main_revision['assigned_user_firstname'].' '.$main_revision['assigned_user_surname']).'</b></p>';
		}
		echo $assigned_user_editor;
		?>
		<form action='/office/reviews/<?php echo xml_escape($organisation['shortname'].'/'.$context_type.'/information/assign'); ?>' method='POST' class='form'>
			<fieldset>
			<label for="assign_reporter">Reviewer:</label>
			<select name="assign_reporter" size="1">
				<option value="unassign">#Unassign#</option><?php 
				foreach ($reviewers as $reviewer){
					echo '				<option value="'.$reviewer['id'].'"';
					if (isset($main_revision['assigned_user_id']) && $main_revision['assigned_user_id'] == $reviewer['id'])
					{
						echo(' selected="selected"');
					}
					echo '>'.xml_escape($reviewer['firstname'].' '.$reviewer['surname']).'</option>'."\n";
				} ?>
			</select>
			<input type='submit' name='unassign_button' value='Assign' class='button'/>
			</fieldset>
		</form>
	<?php 
	}else{ 
		if(empty($main_revision['assigned_user_id'])){
			echo $assigned_user_none;
			echo('<ul>');
			echo('<li><a href="/office/reviews/'.xml_escape($organisation['shortname'].'/'.$context_type.'/information/assign').'">Assign Myself</a></li>');
			echo('</ul>');
		}else{
			echo '<p>Assigned to <b>'.xml_escape($main_revision['assigned_user_firstname'].' '.$main_revision['assigned_user_surname']).'</b></p>';
			if($main_revision['assigned_user_id']==$this->user_auth->entityId){
			echo $assigned_user_you;
			echo('<ul>');
			echo('<li><a href="/office/reviews/'.xml_escape($organisation['shortname'].'/'.$context_type.'/information/unassign').'">Unassign Myself</a></li>');
			echo('</ul>');
			}
		}
	} ?>
	</div>
</div>

<div id="MainColumn">
	<form id='reviewinfo' action='/office/reviews/<?php echo xml_escape($organisation['shortname']); ?>/<?php echo xml_escape($context_type); ?>' method='POST' class='form'>
	<div class='BlueBox'>
		<h2>objective blurb</h2>
		<fieldset>
			<textarea name='reviewinfo_about' cols='48' rows='10'><?php echo(xml_escape($main_revision['content_blurb'])); ?></textarea>
		</fieldset>
	</div>
	<div class='BlueBox'>
	<h2>details</h2>
		<fieldset>
			<label for='reviewinfo_rating' id='reviewinfo_rating_label'>Rating:</label>
			<select name="reviewinfo_rating" id='reviewinfo_rating'>
			<?php
			for ($rating = 0; $rating <= 10; $rating++)
			{
	    		echo('<option');
	    		if ($rating == $main_revision['content_rating']) {
	    			echo(' selected');
				}
	    		echo(' value="'.$rating.'">'.floor($rating / 2).' ');
				if($rating % 2 == 1) echo ('.5 ');
				echo('Stars</option>');
			}
			?>
			</select>
			<input type='hidden' name='reviewinfo_use_js_rating' id='reviewinfo_use_js_rating' value='0'/>
			<script language="JavaScript">
				var stars = new Array(0,0,0,0,0);
				document.getElementById('reviewinfo_rating').style.display = 'none';
				document.getElementById('reviewinfo_rating_label').style.display = 'none';
				document.getElementById('reviewinfo_use_js_rating').value = 1;
				function change_rating(star_number)
				{
					if(stars[star_number]>=2){stars[star_number]=0;}else{stars[star_number]++;}
					
					for(i=0;i<star_number;i++){
						document.getElementById('star'+i).src='/images/prototype/reviews/star.png';
						stars[i]=2;
					}
					for(i=star_number+1;i<=4;i++){
						document.getElementById('star'+i).src='/images/prototype/reviews/emptystar.png';
						stars[i]=0;
					}
					
					if(stars[star_number]==0){
						document.getElementById('star'+star_number).src='/images/prototype/reviews/emptystar.png';
					}
					if(stars[star_number]==1){
						document.getElementById('star'+star_number).src='/images/prototype/reviews/halfstar.png';
					}
					if(stars[star_number]==2){
						document.getElementById('star'+star_number).src='/images/prototype/reviews/star.png';
					}
					document.getElementById('star_rating').value=stars[0]+stars[1]+stars[2]+stars[3]+stars[4];
				}
			</script>
			<br />
			<label for='reviewinfo_js_rating'>Rating:</label>
			<input type='hidden' name='reviewinfo_js_rating' id='star_rating' value='<?php echo $main_revision['content_rating']; ?>'/>&nbsp;
			<?php 
				$whole = floor($main_revision['content_rating'] / 2);
				$part = $main_revision['content_rating'] % 2;
				$empty = 5 - $whole - $part;
				$n=0;
				for($i=0;$i<$whole;$i++)
				{
					echo '<a onclick="change_rating('.$n.')"><img src="/images/prototype/reviews/star.png" id="star'.$n.'" alt="*" title="*" /></a>';
					$n++;
				}
				if ($part == 1)
				{
					echo '<a onclick="change_rating('.$n.')"><img src="/images/prototype/reviews/halfstar.png" id="star'.$n.'" alt="-" title="-" /></a>';
					$n++;
				}
				for($i=0;$i<$empty;$i++)
				{
					echo '<a onclick="change_rating('.$n.')"><img src="/images/prototype/reviews/emptystar.png" id="star'.$n.'" alt=" " title=" " /></a>';
					$n++;
				}
			?>
			<i>Click To Select Stars</i>
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
	</form>
	</div>
	<a href="/office/reviewlist/<?php echo xml_escape($context_type); ?>">Back to the attention list</a>
</div>