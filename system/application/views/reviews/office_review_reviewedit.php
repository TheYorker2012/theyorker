<div class="RightToolbar">
	<h4>What's this?</h4>
		<p>
			<?php echo 'whats_this'; ?>
		</p>
	<h4>Other tasks</h4>
	<ul>
		<li><a href="#">Maintain my account</a></li>
		<li><a href="#">Remove this directory entry</a></li>
	</ul>
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
					echo '<a href="/office/howdoi/editquestion/'.$parameters['article_id'].'/'.$revision['id'].'">'.$dateformatted.'</a>';
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

<div class="blue_box">
	<h2>edit review</h2>
</div>

<div class="blue_box">
	<h2>edit review</h2>
	<form class="form" action="<?php echo($_SERVER['REQUEST_URI']); ?>" method="POST">
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
			<label for="review">Review:</label>
			<?php
			if ($article['displayrevision'] != FALSE)
				echo '<textarea name="a_review_text" id="review" rows="10" cols="50" />'.$article['displayrevision']['wikitext'].'</textarea><br />';
			else
				echo '<textarea name="a_review_text" id="review" rows="10" cols="50" /></textarea><br />';
			?>
		</fieldset>
		<fieldset>
			<input type="submit" name="r_submit_save" value="Save Unpublished" />
			<input type="submit" name="r_submit_publish" value="Publish" />
		</fieldset>
	</form>
</div>

<div class="grey_box">
	<h2>delete review</h2>
	If you wish to delete this review, click the button to do so...<br /><br />
	<form class="form" action="<?php echo($_SERVER['REQUEST_URI']); ?>" method="POST">
		<fieldset>
			<input type="submit" name="r_submit_delete" value="Delete" />
		</fieldset>
	</form>
	<br />
</div>

<?php
echo '<pre>';
print_r($data);
echo '</pre>';
?>
