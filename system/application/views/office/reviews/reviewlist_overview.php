<div class="RightToolbar">
	<h4 class="first">Page Information</h4>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
	<h4>My Assigned Venues</h4>
	<div class="Entry">
		<?php echo $assigned_venues_text; ?>
			<?php
			if(!empty($assigned_venues) && count($assigned_venues)>0) {
			echo '<ul>';
			foreach ($assigned_venues as $venue) { 
				echo '<li><a href="/office/reviews/'.$venue['venue_shortname'].'/'.$content_type_codename.'/information">'.$venue['venue_name'].'</a></li>';
			}
			echo '</ul>';
			}else{
			echo "<p>You have no assigned venues.</p>";
			}
			?>
	</div>
	<?php
	if(!empty($waiting_revisions) && count($waiting_revisions)>0) {
	?>
	<h4>Revisions Needing Approval</h4>
	<div class="Entry">
	<?php
	echo $revisions_needing_approval;
			echo '<ul>';
			foreach ($waiting_revisions as $venue) { 
				echo '<li><a href="/office/reviews/'.$venue['venue_shortname'].'/'.$content_type_codename.'/information">'.$venue['venue_name'];
				if($venue['revisions_waiting']>1) {
					echo ' ('.$venue['revisions_waiting'].' waiting)';
				}
				echo '</a></li>';
			}
			echo '</ul>
	</div>';
	}
	?>
	<?php
	if(!empty($waiting_review_revisions) && count($waiting_review_revisions)>0) {
	?>
	<h4>Reviews Needing Approval</h4>
	<div class="Entry">
	<?php
	echo $reviews_needing_approval;
			echo '<ul>';
			foreach ($waiting_review_revisions as $venue) { 
				echo '<li><a href="/office/reviews/'.$venue['venue_shortname'].'/'.$content_type_codename.'/review">'.$venue['venue_name'].'<br /><span class="orange" style="font-size: x-small">';
				if($venue['published']) {
					if($venue['revisions_waiting']>1){
						echo $venue['revisions_waiting'].' revisions by '.$venue['user_name'];
					}else{
						//if a venue is returned and is published it must have some revisions waiting, as its not >1 there should be one waiting.
						echo 'Revision by '.$venue['user_name'];
					}
				}else{
					echo 'New review by '.$venue['user_name'];
				}
				echo '</span></a></li>';
			}
			echo '</ul>
	</div>';
	}
	if(!empty($leagues) && count($leagues)>0) {
	?>
	<h4>Leagues</h4>
	<div class="Entry">
		<ul>
			<?php
			foreach ($leagues as $league) { 
				echo '<li><a href="/office/league/edit/'.$league['id'].'">'.$league['name'].' ('.$league['current_size'].'/'.$league['size'].')</a></li>';
			}
			?>
		</ul>
	</div>
	<?php
		}
	?>
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
	<?php if(!empty($information_venues)){ ?>
	<h2>Information</h2>
	<?php echo $list_text_information; ?>
	<table style="border: 1px solid #ccc;" cellspacing="0" cellpadding="2">
		<thead>
			<tr style="background-color: #eee">
				<th>
					Name
				</th>
				<th align="center">
					Blurb
				</th>
				<th align="center">
					Rating
				</th>
				<th align="center">
					Quote
				</th>
				<th align="center">
					Av Price
				</th>
				<th align="center">
					Rec Item
				</th>
				<th align="center">
					Times
				</th>
				<th>
					Assigned
				</th>
			</tr>
		</thead>
<?php
		foreach($information_venues as $venue) {
			if($venue['assigned_user_id']==$this->user_auth->entityId){
				echo('			<tr style="background-color: #CCFFFF">'."\n");
			}else{
				echo('			<tr>'."\n");
			}
			echo('				<td><a href="/office/reviews/'.$venue['venue_shortname'].'/'.$content_type_codename.'/information">');
			echo(htmlspecialchars($venue['venue_name']));
			echo('</a></td>'."\n");
			echo('				<td align="center">');
			if(htmlspecialchars($venue['venue_has_blurb'])){
				echo("<img src='/images/prototype/members/confirmed.png'>");
			}else{
				echo("<img src='/images/prototype/members/no9.png'>");
			}
			echo('</td>'."\n");
			echo('				<td align="center">');
			if(htmlspecialchars($venue['venue_has_rating'])){
				echo("<img src='/images/prototype/members/confirmed.png'>");
			}else{
				echo("<img src='/images/prototype/members/no9.png'>");
			}
			echo('</td>'."\n");
			echo('				<td align="center">');
			if(htmlspecialchars($venue['venue_has_quote'])){
				echo("<img src='/images/prototype/members/confirmed.png'>");
			}else{
				echo("<img src='/images/prototype/members/no9.png'>");
			}
			echo('</td>'."\n");
			echo('				<td align="center">');
			if(htmlspecialchars($venue['venue_has_average_price'])){
				echo("<img src='/images/prototype/members/confirmed.png'>");
			}else{
				echo("<img src='/images/prototype/members/no9.png'>");
			}
			echo('</td>'."\n");
			echo('				<td align="center">');
			if(htmlspecialchars($venue['venue_has_recommend_item'])){
				echo("<img src='/images/prototype/members/confirmed.png'>");
			}else{
				echo("<img src='/images/prototype/members/no9.png'>");
			}
			echo('</td>'."\n");
			echo('				<td align="center">');
			if(htmlspecialchars($venue['venue_has_serving_times'])){
				echo("<img src='/images/prototype/members/confirmed.png'>");
			}else{
				echo("<img src='/images/prototype/members/no9.png'>");
			}
			echo('</td>'."\n");
			echo('				<td>');
			echo htmlspecialchars($venue['assigned_user_name']);
			echo('</td>'."\n");
			echo('			</tr>'."\n");
		}
		?>
	</table>
	<br />
	<?php } ?>
	<h2>Reviews</h2>
	<?php echo $list_text_reviews; ?>
	<table style="border: 1px solid #ccc;" cellspacing="0" cellpadding="2">
		<thead>
			<tr style="background-color: #eee">
				<th>
					Name
				</th>
				<th align="center">
					Reviews
				</th>
				<th align="center">
					Last Review Date
				</th>
				<th>
					Assigned User
				</th>
			</tr>
		</thead>
		<?php
		foreach($reviews_venues as $venue) {
			if($venue['assigned_user_id']==$this->user_auth->entityId){
				echo('			<tr style="background-color: #CCFFFF">'."\n");
			}else{
				echo('			<tr>'."\n");
			}
			echo('				<td><a href="/office/reviews/'.$venue['venue_shortname'].'/'.$content_type_codename.'/review">');
			echo(htmlspecialchars($venue['venue_name']));
			echo('</a></td>'."\n");
			echo('				<td align="center">');
			if(htmlspecialchars($venue['review_count'])>0){
				echo htmlspecialchars($venue['review_count']);
			}else{
				echo("<img src='/images/prototype/members/no9.png'>");
			}
			echo('</td>'."\n");
			echo('				<td align="center">');
			if(!empty($venue['date_of_last_review'])){
				echo htmlspecialchars($venue['date_of_last_review']);
			}else{
				echo("&nbsp;");
			}
			echo('</td>'."\n");
			echo('				<td>');
			echo htmlspecialchars($venue['assigned_user_name']);
			echo('</td>'."\n");
			echo('			</tr>'."\n");
		}
		?>
	</table>
	<br />
	<h2>Photos</h2>
	<?php echo $list_text_photos; ?>
	<table style="border: 1px solid #ccc;" cellspacing="0" cellpadding="2">
		<thead>
			<tr style="background-color: #eee">
				<th>
					Name
				</th>
				<th align="center">
					Photos
				</th>
				<th align="center">
					Last Photo Date
				</th>
				<th>
					Assigned User
				</th>
			</tr>
		</thead>
		<?php
		foreach($photos_venues as $venue) {
			if($venue['assigned_user_id']==$this->user_auth->entityId){
				echo('			<tr style="background-color: #CCFFFF">'."\n");
			}else{
				echo('			<tr>'."\n");
			}
			echo('				<td><a href="/office/reviews/'.$venue['venue_shortname'].'/'.$content_type_codename.'/photos">');
			echo(htmlspecialchars($venue['venue_name']));
			echo('</a></td>'."\n");
			echo('				<td align="center">');
			if(htmlspecialchars($venue['photo_count'])>0){
				echo htmlspecialchars($venue['photo_count']);
			}else{
				echo("<img src='/images/prototype/members/no9.png'>");
			}
			echo('</td>'."\n");
			echo('				<td align="center">');
			if(!empty($venue['date_of_last_photo'])){
				echo htmlspecialchars($venue['date_of_last_photo']);
			}else{
				echo("&nbsp;");
			}
			echo('</td>'."\n");
			echo('				<td>');
			echo htmlspecialchars($venue['assigned_user_name']);
			echo('</td>'."\n");
			echo('			</tr>'."\n");
		}
		?>
	</table>
	<br />
	<h2>Tags</h2>
	<?php echo $list_text_tags; ?>
	<table style="border: 1px solid #ccc;" cellspacing="0" cellpadding="2">
		<thead>
			<tr style="background-color: #eee">
				<th>
					Name
				</th>
				<th align="center">
					Number Of Tags
				</th>
				<th>
					Assigned User
				</th>
			</tr>
		</thead>
		<?php
		foreach($tags_venues as $venue) {
			if($venue['assigned_user_id']==$this->user_auth->entityId){
				echo('			<tr style="background-color: #CCFFFF">'."\n");
			}else{
				echo('			<tr>'."\n");
			}
			echo('				<td><a href="/office/reviews/'.$venue['venue_shortname'].'/'.$content_type_codename.'/tags">');
			echo(htmlspecialchars($venue['venue_name']));
			echo('</a></td>'."\n");
			echo('				<td align="center">');
			if(htmlspecialchars($venue['tags_count'])>0){
				echo htmlspecialchars($venue['tags_count']);
			}else{
				echo("<img src='/images/prototype/members/no9.png'>");
			}
			echo('</td>'."\n");
			echo('				<td>');
			echo htmlspecialchars($venue['assigned_user_name']);
			echo('</td>'."\n");
			echo('			</tr>'."\n");
		}
		?>
	</table>
	<br />
	<h2>Leagues</h2>
	<?php echo $list_text_leagues; ?>
	<table style="border: 1px solid #ccc;" cellspacing="0" cellpadding="2">
		<thead>
			<tr style="background-color: #eee">
				<th>
					Name
				</th>
				<th align="center">
					Leagues
				</th>
				<th align="center">
					Star Rating
				</th>
				<th>
					Assigned User
				</th>
			</tr>
		</thead>
		<?php
		foreach($leagues_venues as $venue) {
			if($venue['assigned_user_id']==$this->user_auth->entityId){
				echo('			<tr style="background-color: #CCFFFF">'."\n");
			}else{
				echo('			<tr>'."\n");
			}
			echo('				<td><a href="/office/reviews/'.$venue['venue_shortname'].'/'.$content_type_codename.'/leagues">');
			echo(htmlspecialchars($venue['venue_name']));
			echo('</a></td>'."\n");
			echo('				<td align="center">');
			if(htmlspecialchars($venue['leagues_count'])>0){
				echo htmlspecialchars($venue['leagues_count']);
			}else{
				echo("<img src='/images/prototype/members/no9.png'>");
			}
			echo('</td>'."\n");
			echo('				<td align="center">');
			if(!empty($venue['venue_rating'])){
				$whole = floor($venue['venue_rating'] / 2);
				$part = $venue['venue_rating'] % 2;
				$empty = 5 - $whole - $part;
				for($i=0;$i<$whole;$i++)
				{
					echo '<img src="/images/prototype/reviews/star.png" width="10" alt="*" title="*" />';
				}
				if ($part == 1)
				{
					echo '<img src="/images/prototype/reviews/halfstar.png" width="10" alt="-" title="-" />';
				}
				for($i=0;$i<$empty;$i++)
				{
					echo '<img src="/images/prototype/reviews/emptystar.png" width="10" alt=" " title=" " />';
				}
			}else{
				echo("&nbsp;");
			}
			echo('</td>'."\n");
			echo('				<td>');
			echo htmlspecialchars($venue['assigned_user_name']);
			echo('</td>'."\n");
			echo('			</tr>'."\n");
		}
		?>
	</table>
</div>