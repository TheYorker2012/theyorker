<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>create new subtypes</h2>
		<form method="post" action="/office/articletypes/create">
			<fieldset>
				<label for="article_type_image_id">Image:</label>
				<input type="hidden" name="article_type_image_id" value="<?php if(!empty($article_type_form['article_type_image_id'])){ echo $article_type_form['article_type_image_id']; }?>"><?php
				if(!empty($image_preview)){echo $image_preview;} ?>
				<label for="article_type_name">Name:</label>
				<input type="text" name="article_type_name" value="<?php
				if(!empty($article_type_form['article_type_name'])){echo $article_type_form['article_type_name'];}
				?>" />
				<label for="article_type_parent">Parent Type:</label>
				<select name="article_type_parent"><?php
				foreach ($main_articles as $main_article) {
					echo('					<option value='.$main_article['id'].'"');
					if(!empty($article_type_form['article_type_parent']))
							{
								if ($main_article['id']==$article_type_form['article_type_parent'])
								{echo 'selected="selected"';}
							}
					echo('>'."\n");
					echo('						'.$main_article['name']."\n");
					echo('					</option>'."\n");
				}?>
				</select>
				<label for="article_type_archive">Archive:</label>
				<input type="checkbox" name="article_type_archive" value="1" <?php
				if(empty($article_type_form) || !empty($article_type_form['article_type_archive'])){echo 'checked';}
				?>/>
				<label for="article_type_blurb">Blurb:</label>
				<textarea name="article_type_blurb" cols="26" rows="4"><?php if(!empty($article_type_form['article_type_blurb'])){echo $article_type_form['article_type_blurb'];} ?></textarea>
			</fieldset>
			<fieldset>
				<input name="article_type_add" type="submit" value="Add" class="button" />
			</fieldset>
		</form>
	</div>
</div>