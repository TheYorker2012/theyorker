<div class='RightToolbar'>
<h4>Revisions</h4>
	<div class="Entry">
		This review has the following revisions:
		<ol>
			<li><a href='#'>Dan Ashby 02/02/2007 3:13PM</a></li>
			<li><a href='#'>Charlotte Chung 03/02/2007 4:13PM</a> (Published)</li>
			<li><a href='#'>Charlotte Chung 04/02/2007 5:13PM</a></li>
		</ol>
	</div>
<h4>What's this?</h4>
	<p>
		<?php echo 'whats_this'; ?>
	</p>
<h4>Other tasks</h4>
<ul>
	<li><a href='#'>Maintain my account</a></li>
	<li><a href='#'>Remove this directory entry</a></li>
</ul>
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
			<textarea name="a_review_text" id="a_review_text" cols="50" rows="10"><?php echo 'review'; ?></textarea>
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
	<form>
		<input type="submit" value="Delete" />
	</form><br />
</div>
