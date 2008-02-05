<div class="RightToolbar">
	<h4 class="first">Page Information</h4>
	<?php echo($page_information); ?>
</div>
<div id="MainColumn">
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
						echo '<option value="'.$option['id'].'">'.xml_escape($option['name']).'</option>';
					}
					?>
					</optgroup>
					<optgroup label="Personal:">
					<?php
					foreach ($bylines['user'] as $option)
					{
						echo '<option value="'.$option['id'].'">'.xml_escape($option['name']).'</option>';
					}
					?>
					</optgroup>
				</select>
				<br /><br />
				<div id="toolbar" style="clear: both;"></div>
				<textarea name="a_review_text" id="a_review_text" cols="50" rows="10">review</textarea>
			</fieldset>
			<fieldset>
				<input type="submit" name="r_submit_newreview" value="Create New Review" />
			</fieldset>
		</form>
	</div>
	<script type="text/javascript">
	// <![CDATA[
		mwSetupToolbar('toolbar','a_review_text', false);
	// ]]>
	</script>
	<div class="grey_box">
		<h2>maintain reviews</h2>
		<?php
			foreach ($reviews as $review)
			{
				if (isset($review['writers'][0]))
				{
					echo('<span style="font-size: medium;"><b>'.xml_escape($review['writers'][0]['name']).'</b></span><br />');
					echo(xml_escape($review['article']['created']).'<br />');
					if(empty($review['article']['live_content'])) {
						echo('This article is waiting to be published<br />');
					} else {
						echo ('This article is published<br />');
					}
					echo('<a href="/office/reviews/'.$parameters['organistion'].'/'.$parameters['context_type'].'/reviewedit/'.$review['article']['id'].'">');
					if(empty($review['article']['live_content'])) {
						echo('<span class="orange">Edit or <b>publish</b> this review.');
					} else {
						echo('<span class="orange">Edit or <b>pull</b> this review.');
					}
					echo('</a><br /><br />');
				}
			}
		?>
	</div>
	<a href="/office/reviewlist/<?php echo($parameters['context_type']); ?>">Back to the attention list</a>
</div>