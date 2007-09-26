<div class="RightToolbar">
	<h4 class="first">Page Information</h4>
		<p>
			<?php echo $page_information; ?>
		</p>
	<?php
		echo '<h4>Revisions (Latest First)</h4>
		<div class="Entry">';
			if (count($article['revisions']) > 0)
			{
				$first_hr = FALSE;
				foreach ($article['revisions'] as $revision)
				{
					if ($first_hr == FALSE)
						$first_hr = TRUE;
					else
						echo '<hr>';
					$dateformatted = date('F jS Y', $revision['updated']).' at '.date('g.i A', $revision['updated']);
					echo '<a href="/office/reviews/'.$parameters['organisation'].'/'.$parameters['context_type'].'/reviewedit/'.$parameters['article_id'].'/'.$revision['id'].'">'.$dateformatted.'</a>';
					if ($revision['id'] == $article['header']['live_content'])
					{
						echo '<br /><span class="orange">(Published';
						if ($revision['id'] == $article['displayrevision']['id'])
							echo ', Displayed';
						echo ')</span>';
					}
					elseif ($revision['id'] == $article['displayrevision']['id'])
						echo '<br /><span class="orange">(Displayed)</span>';
					echo '<br />by '.$revision['username'].'<br />';
				}
			}
			else
				echo 'No Revisions ... Yet.';
		echo '</div>';
	?>
</div>
<div id="MainColumn">
	<div class="blue_box">
		<h2>edit review</h2>
		<form class="form" action="<?php echo($this_url); ?>" method="POST">
			<fieldset>
				<div id="toolbar"></div>
				<?php
				if ($article['displayrevision'] != FALSE)
					echo '<textarea name="a_review_text" id="review" rows="10" cols="50" />'.$article['displayrevision']['wikitext'].'</textarea><br />';
				else
					echo '<textarea name="a_review_text" id="review" rows="10" cols="50" /></textarea><br />';
				?>
			</fieldset>
			<fieldset>
				<input type="submit" name="r_submit_save" value="Save Revision" />
			</fieldset>
		</form>
	</div>
	<script type="text/javascript">
		mwSetupToolbar('toolbar','review', false);
	</script>
<!--
<div class="grey_box">
	<h2>change author</h2>
	<form class="form" action="<?php echo($this_url); ?>" method="POST">
		<fieldset>
			<label for="a_review_author">Author:</label>
			<select name="a_review_author">
				<optgroup label="Generic:">
				<?php
				foreach ($bylines['generic'] as $option)
				{
					echo '<option value="'.$option['id'].'">'.$option['name'].'</option>';
				}
				?>
				</optgroup>
				<optgroup label="Personal:">
				<?php
				foreach ($bylines['user'] as $option)
				{
					echo '<option value="'.$option['id'].'">'.$option['name'].'</option>';
				}
				?>
				</optgroup>
			</select>
		</fieldset>
		<fieldset>
			<input type="submit" name="r_submit_set" value="Set As Author" />
		</fieldset>
	</form>
</div>
-->
<?php
if ($user['officetype'] != 'Low')
{
?>
<div class="blue_box">
	<h2>editor options</h2>
	If you wish to PUBLISH this review, click the button to do so...<br /><br />
	<form class="form" action="<?php echo($this_url); ?>" method="POST">
		<fieldset>
			<input type="submit" name="r_submit_publish" value="Publish" />
		</fieldset>
	</form>
	<?php
	if ($parameters['revision_id'] == $article['header']['live_content'])
		if ($parameters['revision_id'] == $article['displayrevision']['id'])
			{
	?>
	<br />
	If you wish to PULL this published review, click the button to do so...<br /><br />
	<form class="form" action="<?php echo($this_url); ?>" method="POST">
		<fieldset>
			<input type="submit" name="r_submit_pull" value="Pull" />
		</fieldset>
	</form>
	<?php
			}
	?>
	<br />
	If you wish to DELETE this review, click the button to do so...<br /><br />
	<form class="form" action="<?php echo($this_url); ?>" method="POST">
		<fieldset>
			<input type="submit" name="r_submit_delete" value="Delete" />
		</fieldset>
	</form>
	<br />
</div>
<?php
}
?>
<a href="/office/reviews/<?php echo $parameters['organisation']."/".$parameters['context_type']; ?>/review">Back to the reviews list</a>
</div>
