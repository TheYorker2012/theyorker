<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<?php if(!empty($reviews)){ ?>
	<div class="BlueBox">
		<h2>Reviews</h2>
		<div class="ArticleBox">
			<table>
				<thead>
					<tr>
						<th>
							Author
						</th>
						<th>
							Created
						</th>
						<th>
							Status
						</th>
						<th>
							Revisions
						</th>
						<th>
							Edit
						</th>
					</tr>
				</thead>
				<?php
					$count=0;
					foreach ($reviews as $review)
					{
						$count++;
						if (isset($review['writers'][0]))
						{
							echo('				<tr class="tr'.(($count%2)+1).'">'."\n");
							echo('					<td>'."\n");
							echo xml_escape($review['writers'][0]['name']);
							echo('</td>'."\n");
							echo('					<td>'."\n");
							echo xml_escape(substr($review['article']['created'],0,10));
							echo('</td>'."\n");
							echo('					<td>'."\n");
							if($review['article']['status'] !='published'){echo'<span class="red">';}
							echo xml_escape($review['article']['status']);
							if($review['article']['status'] !='published'){echo'</span>';}
							echo('</td>'."\n");
							echo('					<td>'."\n");
							//Does the article have any revisions waiting to be published?
							if(!empty($review['article']['live_content']) && $review['article']['revisions_waiting']){
								echo '<span class="red">Waiting</span>';
							}else{
								echo 'Up To Date';
							}
							echo('</td>'."\n");
							echo('					<td>'."\n");
							echo('<a href="/office/reviews/'.$parameters['organistion'].'/'.$parameters['context_type'].'/reviewedit/'.$review['article']['id'].'">Edit</a>');
							echo('</td>'."\n");
							echo('				</tr>'."\n");
						}
					}
				?>
			</table>
		</div>
		<br />
	</div>
	<?php } ?>
	<div class="BlueBox">
		<h2>Add review</h2>
		<form class="form" action="<?php echo($_SERVER['REQUEST_URI']); ?>" method="POST">
			<fieldset>
				<label for="a_review_author">Author :</label>
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
				<label for="a_review_blurb" class="full">Short Review Blurb</label>
				<textarea name="a_review_blurb" class="full" id="a_review_blurb" cols="50" rows="3"></textarea>
				<label for="a_review_text" class="full">Main Review Contents</label>
				<div id="toolbar" style="clear: both;"></div>
				<textarea name="a_review_text" class="full" id="a_review_text" cols="50" rows="10"></textarea>
			</fieldset>
			<fieldset>
				<input type="submit" name="r_submit_newreview" value="Create New Review" class="button"/>
			</fieldset>
		</form>
	</div>
	<script type="text/javascript">
		mwSetupToolbar('toolbar','a_review_text', false);
	</script>
	<a href="/office/reviewlist/<?php echo($parameters['context_type']); ?>">Back to the attention list</a>
</div>