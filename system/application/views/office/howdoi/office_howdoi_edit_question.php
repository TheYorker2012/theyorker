<div class="RightToolbar">
	<h4>Areas for Attention</h4>
	<div class="Entry">
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b>3</b> <a href='#'>Questions</a> that are waiting to be published.
		</div>
		<div class="information_box">
			<img src="/images/prototype/homepage/infomark.png" />
			There are <b>3</b> <a href='#'>Suggestions</a> that require attention.
		</div>
	</div>
	<h4>Revisions</h4>
	<div class="Entry">
		<ol>
			<li>Dan Ashby 04/02/2007 3:39PM
			<li>Nick Evans 04/02/2007 3:20PM <span class="orange">(Published)</span>
			<li>Dan Ashby 03/02/2007 3:11PM 
			<li>John Smith 03/02/2007 3:11PM 
			<li>Rich Rout 02/02/2007 1:11AM 
		</ol>
	</div>
</div>

<div class="grey_box">
	<h2>edit question</h2>
	<form class="form">
		<fieldset>
		<?php
			echo '<label for="a_question">Question:</label>
			<input type="text" name="a_question" value="';
			if ($article['displayrevision'] != FALSE)
				echo $article['displayrevision']['heading'];
			echo '" /><br />
			<label for="a_category">Category:</label>';
		?>
			<select name="a_category">
				<?php
					foreach ($categories as $category_id => $category)
					{
					echo '<option value="'.$category_id.'"';
					if ($category_id == $article['header']['content_type'])
						echo ' selected';
					echo '>'.$category['name'].'</option>';
					//echo '<option selected>Opening Times</option>';
					}
				?>
			</select><br />
			<label for="a_answer">Answer:</label>
			<?php
				if ($article['displayrevision'] != FALSE)
					echo '<textarea name="a_answer" rows="5" cols="30" />'.$article['displayrevision']['wikitext'].'</textarea><br />';
				else
					echo '<textarea name="a_answer" rows="5" cols="30" /></textarea><br />'
			?>
			<input type="submit" value="Save" class="button" name="r_submit_save" />
		</fieldset>
	</form>
</div>
<div class="blue_box">
	<h2>publish options</h2>
	<form class="form">
	To publish the question now, click publish now
		<fieldset>
			<input type="submit" value="Publish Now" class="button" />
		</fieldset>
	Or to publish at a later date...
		<fieldset>
			<label for"a_publishdate">Publish On (dd/mm/yyyy):</label>
			<input type="text" name="a_publishdate" />
			<input type="submit" value="Publish Then" class="button" />
		</fieldset>
	</form>
</div>

<?php

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>


