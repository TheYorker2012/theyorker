<div class="RightToolbar">
	<h4 class="first">Page Information</h4>
	<?php 
	echo($page_information);
	
	if (count($article['revisions']) > 0){
	?>
	<h4>Revisions</h4>
	<div class="Entry">
		<ol>
		<?php
			foreach ($article['revisions'] as $revision) 
			{
				$dateformatted = date("d/m/y H:i", $revision['updated']);
				$edit_link = xml_escape('/office/reviews/'.$parameters['organisation'].'/'.$parameters['context_type'].'/reviewedit/'.$parameters['article_id'].'/'.$revision['id']);
				echo '			<li>'."\n";
				echo '				Author : '.xml_escape($revision['username']).'<br />';
				echo '				Created : '.$dateformatted.'<br />';
				if ($revision['id'] == $article['displayrevision']['id']){
					echo '				<b>(Editing)</b>';
				}else{
					echo '				<a href='.$edit_link.'>Edit</a>';
				}
				if ($revision['id'] == $article['header']['live_content']){
					echo ' | <span class="orange">(Published)</span><br />';
				}
			}
		?>
		</ol>
	</div>
	<?php 
	}
	if($user['is_editor']){
	?>
	<h4>Editor Options</h4>
	<div class="Entry">
		<?php
		//Only show publish button if the revision is not currently the live revision.
		//If there is no revision show it anyway
		if($article['header']['live_content']!=$article['displayrevision']['id'])
		{
			?>
		<form class="form" action="<?php echo($this_url); ?>" method="POST">
			<fieldset>
				<input type="submit" name="r_submit_publish" class="button" value="Publish This Revision" />
			</fieldset>
		</form>
			<?php
		}
		?>
		<form class="form" action="<?php echo($this_url); ?>" method="POST">
			<fieldset>
				<input type="submit" name="r_submit_pull" class="button" value="Pull Review" />
			</fieldset>
		</form>
		<form class="form" action="<?php echo($this_url); ?>" method="POST">
			<fieldset>
				<input type="submit" name="r_submit_delete" class="button" value="Delete Review" />
			</fieldset>
		</form>
	</div>
	<?php
	}
	?>
</div>
<div id="MainColumn">
	<div class="blue_box">
		<h2>edit review</h2>
		<form class="form" action="<?php echo($this_url); ?>" method="POST">
			<fieldset>
				<label for="a_review_blurb" class="full">Short Review Blurb</label>
				<textarea name="a_review_blurb" class="full" id="a_review_blurb" cols="50" rows="3"><?php
				if (!empty($article['displayrevision']['blurb'])) {
					echo xml_escape($article['displayrevision']['blurb']);
				}
				?></textarea>
				<label for="a_review_text" class="full">Main Review Contents</label>
				<div id="toolbar"></div>
				<textarea name="a_review_text" class="full" id="review" rows="10" cols="50" /><?php
				if (!empty($article['displayrevision']['wikitext'])) {
					echo xml_escape($article['displayrevision']['wikitext']);
				}
				?></textarea>
			</fieldset>
			<fieldset>
				<input type="submit" name="r_submit_save" class="button" value="Create Revision" />
			</fieldset>
		</form>
	</div>
	<script type="text/javascript">
		mwSetupToolbar('toolbar','review', false);
	</script>
<a href="/office/reviews/<?php echo $parameters['organisation']."/".$parameters['context_type']; ?>/review">Back to the reviews list</a>
</div>
