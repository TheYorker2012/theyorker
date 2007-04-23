<div class="RightToolbar">
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
		<?php echo 'whats_this'; ?>
	</p>
<h4>Other tasks</h4>
<ul>
	<li><a href="#">Maintain my account</a></li>
	<li><a href="#">Remove this directory entry</a></li>
</ul>
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
					echo '<option>'.$option['name'].'</option>';
				}
				?>
				</optgroup>
				<optgroup label="Personal:">
				<?php 
				foreach ($bylines['user'] as $option)
				{
					echo '<option>'.$option['name'].'</option>';
				}
				?>
				</optgroup>
			</select>
			<label for="review">Review:</label>
			<textarea name="a_review_text" id="a_review_text" cols="50" rows="10"><?php echo 'review'; ?></textarea>
		</fieldset>
		<fieldset>
			<input type="submit" name="r_submit_newreview" value="Create New Review" />
		</fieldset>
	</form>
</div>
<div class="grey_box">
	<h2>maintain reviews</h2>
		<img src="/images/prototype/news/benest.png" alt="Reporter" title="Reporter" style="float: right;" />
		<span style="font-size: medium;"><b>Chris Travis</b></span><br />
		25th March 2007<br />
		<a href="/office/reviews/theyorker/food/reviewedit/2"><span class="orange">Edit this review</a> <span class="black">|</span> <a href="#">Delete this review</a>
	        <p>
		A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks about nothing.<br /><br />

		<img src="/images/prototype/news/benest.png" alt="Reporter" title="Reporter" style="float: right;" />
		<span style="font-size: medium;"><b>Chris Travis</b></span><br />
		25th March 2007<br />
		<a href="/office/reviews/theyorker/food/reviewedit/2"><span class="orange">Edit this review</span></a> | <a href="#">Delete this review</a>
	        <p>
		A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks A whole load of bollocks about nothing.
</div>

<?php
echo '<pre>';
print_r($data);
echo '</pre>';
?>