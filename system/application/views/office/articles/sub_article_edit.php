<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
	<h2>Current Image</h2>
	<div class="Entry" align='center'>
		<?php echo $image; ?>
		<p><a href="/office/articletypes/changeimage/<?php echo $article_type_form['article_type_id']; ?>">Change/Add Image</a></p>
	</div>
</div>
<div id="MainColumn">

	<div class="BlueBox">
		<h2>edit subtype</h2>
		<form method="post" action="/office/articletypes/edit/<?php echo $article_type_form['article_type_id']; ?>">
			<fieldset>
				<label for="article_type_name">Name:</label>
				<input type="text" name="article_type_name" value="
				<?php if(!empty($article_type_form['article_type_name'])){echo $article_type_form['article_type_name'];} ?>
				" />
				<input type="hidden" name="article_type_id" value="<?php echo $article_type_form['article_type_id']; ?>">
				<label for="article_type_parent">Parent Type:</label>
				<select name="article_type_parent">
				<?php
				foreach ($main_articles as $main_article) {
					?>
					<option value="<?php echo $main_article['id'] ?>"
					<?php if(!empty($article_type_form['article_type_parent']))
							{
								if ($main_article['id']==$article_type_form['article_type_parent'])
								{echo 'selected="selected"';}
							}
						?>
						>
						<?php echo $main_article['name'] ?></option>
					<?php
				}
				?>
				</select>
				<label for="article_type_archive">Archive:</label>
				<input type="checkbox" name="article_type_archive" value="1" 
				<?php if(empty($article_type_form) || !empty($article_type_form['article_type_archive'])){echo 'checked';} ?>
				/>
				<label for="article_type_blurb">Blurb:</label>
				<textarea name="article_type_blurb" cols="26" rows="4"><?php if(!empty($article_type_form['article_type_blurb'])){echo $article_type_form['article_type_blurb'];} ?></textarea>
			</fieldset>

			<fieldset>
				<input name="article_type_edit" type="submit" value="Edit" class="button" />
			</fieldset>
		</form>
	</div>
	<a href='/office/articletypes'>Go Back</a>
</div>