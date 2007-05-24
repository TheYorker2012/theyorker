<div class="RightToolbar">
<!--
	<h4>Areas for Attention</h4>
	<div class="Entry">
		<div class="information_box">
			The following reviews are waiting to be published:
			<ul>
				<li><a href="#">Dan Ashby 02/02/2007</a></li>
				<li><a href="#">Charlotte Chung 02/02/2007</a></li>
			</ul>
		</div>
		<div class="information_box">
			<img src="/images/prototype/homepage/information.png" />
			<a href="#">Information</a> has been updated and is waiting to be published.
		</div>
		<div class="information_box">
			There are <a href="#">Comments</a> that have been reported for abuse.
		</div>
	</div>
	<h4>Whats this?</h4>
		<p>
			<?php //echo 'whats_this'; ?>
		</p>
	<h4>Other tasks</h4>
	<ul>
		<li><a href="#">Maintain my account</a></li>
		<li><a href="#">Remove this directory entry</a></li>
	</ul>
-->
</div>

<div class="blue_box">
	<h2>add review</h2>
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
			<div id="toolbar" style="clear: both;"></div>
			<textarea name="a_review_text" id="a_review_text" cols="50" rows="10"><?php echo 'review'; ?></textarea>
		</fieldset>
		<fieldset>
			<input type="submit" name="r_submit_newreview" value="Create New Review" />
		</fieldset>
	</form>
</div>
<script type="text/javascript">
	mwSetupToolbar('toolbar','a_review_text', false);
</script>
<div class="grey_box">
	<h2>maintain reviews</h2>
	<?php
		foreach ($reviews as $review)
		{
			if (isset($review['writers'][0]))
			{
				echo '<span style="font-size: medium;"><b>'.$review['writers'][0]['name'].'</b></span><br />';
				echo $review['article']['created'].'<br />';
				echo '<a href="/office/reviews/'.$parameters['organistion'].'/'.$parameters['context_type'].'/reviewedit/'.$review['article']['id'].'"><span class="orange">Edit this review</a><br /><br />';
			}
		}
	?>
</div>