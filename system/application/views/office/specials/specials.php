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
			<h2>current featured articles</h2>
			<table>
				<thead>
					<tr>
						<th>Section</th><th>Title</th><th>Change</th>
					</tr>
				</thead>
			<?php
			foreach($main_articles as $article_section){
				echo('<tr>');
				echo('<td>'.$article_section['name'].'</td>');
				if(empty($article_section['featured_article'])){
					echo('<td><span class="red">Empty</span></td>');
				}else{
					echo('<td><a href="/news/'.$article_section['featured_article']['article_type'].'/'.$article_section['featured_article']['id'].'">');
					echo($article_section['featured_article']['heading']);
					echo('</a></td>');
				}
				echo('<td><a href="/office/specials/edit/'.$article_section['id'].'">Change</a>');
				echo('</tr>');
			}
			?>
			</table>
	</div>
</div>