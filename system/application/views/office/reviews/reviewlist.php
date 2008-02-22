<div class="RightToolbar">
	<h4 class="first">Page Information</h4>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
	<h4>Actions</h4>
	<div class="Entry">
		<ul>
			<li><a href="/office/reviewtags">Create/Edit Tags.</a></li>
			<li><a href="/office/leagues">Create/Edit Leagues.</a></li>
			<li><a href="/office/prlist">Enable/Disable Reviews On A Venue</a></li>
		</ul>
	</div>
</div>
<div id="MainColumn">
	<div class="blue_box">
		<h2>Venues</h2>
		<div class="ArticleBox">
			<table>
				<thead>
					<tr>
						<th>
							Name
						</th>
						<th>
							Info
						</th>
						<th>
							Reviews
						</th>
						<th>
							Last Review
						</th>
						<th>
							Tags
						</th>
						<th>
							Leagues
						</th>
						<th>
							Assigned
						</th>
					</tr>
				</thead>
	<?php
		$count=0;
			foreach($organisations as $organisation) {
				$count++;
				if($organisation['assigned_user_id']==$this->user_auth->entityId){
					echo('			<tr class="tr'.(($count%2)+1).'" style="background-color: #CCFFFF">'."\n");
				}else{
					echo('			<tr class="tr'.(($count%2)+1).'">'."\n");
				}
				echo('				<td><a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/information">');
				echo(xml_escape($organisation['name']));
				echo('</a></td>'."\n");
				echo('				<td><a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/information">');
				if($organisation['info_complete']){
					echo("<img src='/images/prototype/members/confirmed.png'>");
				}else{
					echo("<img src='/images/prototype/members/no9.png'>");
				}
				echo('</a></td>'."\n");
				echo('				<td><a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/review">');
				if($organisation['review_count'] == 0){
					echo("<img src='/images/prototype/members/no9.png'>");
				}else{
					echo($organisation['review_count']);
				}
				echo('</a></td>'."\n");
				echo('				<td><a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/review">');
				if($organisation['review_count'] > 0) {
					echo(xml_escape(date("d/m/y",$organisation['date_of_last_review'])));
				}
				echo('</a></td>'."\n");
				echo('				<td><a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/tags">');
				if($organisation['number_of_tags'] == 0){
					echo("<img src='/images/prototype/members/no9.png'>");
				}else{
					echo(xml_escape($organisation['number_of_tags']));
				}
				echo('</a></td>'."\n");
				echo('				<td><a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/leagues">');
				if($organisation['number_of_leagues']==0){
					echo("<img src='/images/prototype/members/no9.png'>");
				}else{
					echo(xml_escape($organisation['number_of_leagues']));
				}
				echo('</a></td>'."\n");
				echo('				<td><a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/information">');
				echo(xml_escape($organisation['assigned_user_name']));
				echo('</a></td>'."\n");
				echo('			</tr>'."\n");
			}
			?>
			</table>
		</div>
	</div>
</div>
