<div class="RightToolbar">
	<h4 class="first">Page Information</h4>
	<div class="Entry">
		<?php echo $page_information; ?>
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
		<table style="border: 1px solid #ccc;" cellspacing="0" cellpadding="2">
			<thead>
				<tr style="background-color: #eee">
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
		foreach($organisations as $organisation) {
			if($organisation['assigned_user_id']==$this->user_auth->entityId){
				echo('			<tr style="background-color: #CCFFFF">'."\n");
			}else{
				echo('			<tr>'."\n");
			}
			echo('				<td><a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/information">');
			echo(htmlspecialchars($organisation['name']));
			echo('</a></td>'."\n");
			echo('				<td><a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/information">');
			if(htmlspecialchars($organisation['info_complete'])){
				echo("<img src='/images/prototype/members/confirmed.png'>");
			}else{
				echo("<img src='/images/prototype/members/no9.png'>");
			}
			echo('</a></td>'."\n");
			echo('				<td><a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/review">');
			if(htmlspecialchars($organisation['review_count'])==0){
				echo("<img src='/images/prototype/members/no9.png'>");
			}else{
				echo $organisation['review_count'];
			}
			echo('</a></td>'."\n");
			echo('				<td><a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/review">');
			if(htmlspecialchars($organisation['review_count'])>0) {echo(htmlspecialchars($organisation['date_of_last_review']));}
			echo('</a></td>'."\n");
			echo('				<td><a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/tags">');
			if(htmlspecialchars($organisation['number_of_tags'])==0){
				echo("<img src='/images/prototype/members/no9.png'>");
			}else{
				echo htmlspecialchars($organisation['number_of_tags']);
			}
			echo('</a></td>'."\n");
			echo('				<td><a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/leagues">');
			if(htmlspecialchars($organisation['number_of_leagues'])==0){
				echo("<img src='/images/prototype/members/no9.png'>");
			}else{
				echo htmlspecialchars($organisation['number_of_leagues']);
			}
			echo('</a></td>'."\n");
			echo('				<td><a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/information">');
			echo htmlspecialchars($organisation['assigned_user_name']);
			echo('</a></td>'."\n");
			echo('			</tr>'."\n");
		}
		?>
		</table>
	</div>
</div>
