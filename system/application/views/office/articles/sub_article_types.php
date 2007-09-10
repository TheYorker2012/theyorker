<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>current subtypes</h2>
		<table>
			<thead>
				<tr>
					<th>Name</th><th>Parent</th><th>Image</th><th>Archive</th><th>Blurb</th><th>Edit</th><th>Del</th>
				</tr>
			</thead>
			<?php
			foreach($sub_articles as $sub_article){
				echo('<tr>');
				echo('<td>'.$sub_article['name'].'</td>');
				echo('<td>'.$sub_article['parent_name'].'</td>');
				echo('<td>');
				if($sub_article['image']==NULL){echo("<img src='/images/prototype/members/no9.png'>");}else{echo("<img src='/images/prototype/members/confirmed.png'>");}
				echo('</td>');
				echo('<td>');
				if($sub_article['in_archive']){echo("<img src='/images/prototype/members/confirmed.png'>");}else{echo("<img src='/images/prototype/members/no9.png'>");}
				echo('</td>');
				echo('<td>');
				if($sub_article['blurb']==''){echo("<img src='/images/prototype/members/no9.png'>");}else{echo("<img src='/images/prototype/members/confirmed.png'>");}
				echo('</td>');
				echo('<td><a href="/office/articletypes/edit/'.$sub_article['id'].'">Edit</a></td>');
				echo('<td><a href="/office/articletypes/delete/'.$sub_article['id'].'">Del</a></td>');
				echo('</tr>');
			}
			?>
		</table>
	</div>
	<div class="BlueBox">
		<h2>create new subtypes</h2>
		<p> sorry no image or list order support yet! delete not working</p>
		<form method="post" action="/office/articletypes">
			<fieldset>
				<label for="article_type_name">Name:</label>
				<input type="text" name="article_type_name" value="
				<?php if(!empty($article_type_form['article_type_name'])){echo $article_type_form['article_type_name'];} ?>
				" />
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
				<input name="article_type_add" type="submit" value="Add" class="button" />
			</fieldset>
		</form>
	</div>
</div>