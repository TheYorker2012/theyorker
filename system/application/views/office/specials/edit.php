<div id="RightColumn">
	<h2 class="first">
		Information
	</h2>
	<div class="Entry">
	<?php echo $page_information; ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2><?php echo $article_type['name'];?>'s special article</h2>
		<form method="post" action="/office/specials">
			<fieldset>
				<label for="special_article"></label>
				<select name="special_article">
				<option value="">#No Special#</option>
				<?php
				foreach ($articles as $article)
				{
					echo('				<option value="'.$article['id'].'"');
					if(!empty($current_special_id))
					{
						if ($article['id']==$current_special_id)
						{echo 'selected="selected"';}
					}
					echo('>');
					echo ('					'.$article['heading']);
					echo('				</option>'."\n");
				}
				?>
				</select>
				<input type="hidden" name="article_type" value="<?php echo $article_type['codename']; ?>">
			</fieldset>
			<fieldset>
				<input name="specials_edit" type="submit" value="Edit" class="button" />
			</fieldset>
		</form>
	</div>
	<a href='/office/specials/'>Go Back</a>
</div>