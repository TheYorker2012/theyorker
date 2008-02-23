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
	<div class="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>
						Name
					</th>
					<th>
						Blurb
					</th>
					<th>
						Rating
					</th>
					<th>
						Quote
					</th>
					<th>
						Av Price
					</th>
					<th>
						Rec Item
					</th>
					<th>
						Times
					</th>
					<th>
						Assigned
					</th>
				</tr>
			</thead>
<?php
			$count=0;
			foreach($information_venues as $venue) {
				$count++;
				if($venue['assigned_user_id']==$this->user_auth->entityId){
					echo('			<tr class="tr'.(($count%2)+1).'" style="background-color: #CCFFFF">'."\n");
				}else{
					echo('			<tr class="tr'.(($count%2)+1).'">'."\n");
				}
				echo('				<td><a href="/office/reviews/'.$venue['venue_shortname'].'/'.$content_type_codename.'/information">');
				echo(xml_escape($venue['venue_name']));
				echo('</a></td>'."\n");
				echo('				<td>');
				if($venue['venue_has_blurb']){
					echo("<img src='/images/prototype/members/confirmed.png'>");
				}else{
					echo("<img src='/images/prototype/members/no9.png'>");
				}
				echo('</td>'."\n");
				echo('				<td>');
				if($venue['venue_has_rating']){
					echo("<img src='/images/prototype/members/confirmed.png'>");
				}else{
					echo("<img src='/images/prototype/members/no9.png'>");
				}
				echo('</td>'."\n");
				echo('				<td>');
				if($venue['venue_has_quote']){
					echo("<img src='/images/prototype/members/confirmed.png'>");
				}else{
					echo("<img src='/images/prototype/members/no9.png'>");
				}
				echo('</td>'."\n");
				echo('				<td>');
				if($venue['venue_has_average_price']){
					echo("<img src='/images/prototype/members/confirmed.png'>");
				}else{
					echo("<img src='/images/prototype/members/no9.png'>");
				}
				echo('</td>'."\n");
				echo('				<td>');
				if($venue['venue_has_recommend_item']){
					echo("<img src='/images/prototype/members/confirmed.png'>");
				}else{
					echo("<img src='/images/prototype/members/no9.png'>");
				}
				echo('</td>'."\n");
				echo('				<td>');
				if($venue['venue_has_serving_times']){
					echo("<img src='/images/prototype/members/confirmed.png'>");
				}else{
					echo("<img src='/images/prototype/members/no9.png'>");
				}
				echo('</td>'."\n");
				echo('				<td>');
				echo xml_escape($venue['assigned_user_name']);
				echo('</td>'."\n");
				echo('			</tr>'."\n");
			}
			?>
		</table>
	</div>
	<br />
	<?php } ?>
	<h2>Reviews</h2>
	<?php echo $list_text_reviews; ?>
	<div class="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>
						Name
					</th>
					<th>
						Reviews
					</th>
					<th>
						Last Review
					</th>
					<th>
						Assigned User
					</th>
				</tr>
			</thead>
			<?php
			$count=0;
			foreach($reviews_venues as $venue) {
				$count++;
				if($venue['assigned_user_id']==$this->user_auth->entityId){
					echo('			<tr class="tr'.(($count%2)+1).'" style="background-color: #CCFFFF">'."\n");
				}else{
					echo('			<tr class="tr'.(($count%2)+1).'">'."\n");
				}
				echo('				<td><a href="/office/reviews/'.$venue['venue_shortname'].'/'.$content_type_codename.'/review">');
				echo(xml_escape($venue['venue_name']));
				echo('</a></td>'."\n");
				echo('				<td align="center">');
				if($venue['review_count']>0){
					echo xml_escape($venue['review_count']);
				}else{
					echo("<img src='/images/prototype/members/no9.png'>");
				}
				echo('</td>'."\n");
				echo('				<td align="center">');
				if(!empty($venue['date_of_last_review'])){
					echo xml_escape(date("d/m/y",$venue['date_of_last_review']));
				}else{
					echo("&nbsp;");
				}
				echo('</td>'."\n");
				echo('				<td>');
				echo xml_escape($venue['assigned_user_name']);
				echo('</td>'."\n");
				echo('			</tr>'."\n");
			}
			?>
		</table>
	</div>
	<br />
	<h2>Photos</h2>
	<?php echo $list_text_photos; ?>
	<div class="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>
						Name
					</th>
					<th>
						Photos
					</th>
					<th>
						Last Photo
					</th>
					<th>
						Assigned User
					</th>
				</tr>
			</thead>
			<?php
			$count=0;
			foreach($photos_venues as $venue) {
				$count++;
				if($venue['assigned_user_id']==$this->user_auth->entityId){
					echo('			<tr class="tr'.(($count%2)+1).'" style="background-color: #CCFFFF">'."\n");
				}else{
					echo('			<tr class="tr'.(($count%2)+1).'">'."\n");
				}
				echo('				<td><a href="/office/reviews/'.$venue['venue_shortname'].'/'.$content_type_codename.'/photos">');
				echo(xml_escape($venue['venue_name']));
				echo('</a></td>'."\n");
				echo('				<td align="center">');
				if($venue['photo_count']>0){
					echo xml_escape($venue['photo_count']);
				}else{
					echo("<img src='/images/prototype/members/no9.png'>");
				}
				echo('</td>'."\n");
				echo('				<td align="center">');
				if(!empty($venue['date_of_last_photo'])){
					echo xml_escape(date("d/m/y",$venue['date_of_last_photo']));
				}else{
					echo("&nbsp;");
				}
				echo('</td>'."\n");
				echo('				<td>');
				echo xml_escape($venue['assigned_user_name']);
				echo('</td>'."\n");
				echo('			</tr>'."\n");
			}
			?>
		</table>
	</div>
	<br />
	<?php if(!empty($thumbnails_venues)){ ?>
	<h2>Photo Thumbnails</h2>
	<?php echo $list_text_thumbnails; ?>
	<div class="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>
						Name
					</th>
					<th>
						Photos
					</th>
					<th>
						Thumbnail?
					</th>
					<th>
						Assigned User
					</th>
				</tr>
			</thead>
			<?php
			$count=0;
			foreach($thumbnails_venues as $venue) {
				$count++;
				if($venue['assigned_user_id']==$this->user_auth->entityId){
					echo('			<tr class="tr'.(($count%2)+1).'" style="background-color: #CCFFFF">'."\n");
				}else{
					echo('			<tr class="tr'.(($count%2)+1).'">'."\n");
				}
				echo('				<td><a href="/office/reviews/'.$venue['venue_shortname'].'/'.$content_type_codename.'/photos">');
				echo(xml_escape($venue['venue_name']));
				echo('</a></td>'."\n");
				echo('				<td align="center">');
				if($venue['photo_count']>0){
					echo xml_escape($venue['photo_count']);
				}else{
					echo("<img src='/images/prototype/members/no9.png'>");
				}
				echo('</td>'."\n");
				echo('				<td align="center"><img src="/images/prototype/members/no9.png"></td>'."\n");
				echo('				<td>');
				echo xml_escape($venue['assigned_user_name']);
				echo('</td>'."\n");
				echo('			</tr>'."\n");
			}
			?>
		</table>
	</div>
	<br />
	<?php } ?>
	<h2>Tags</h2>
	<?php echo $list_text_tags; ?>
	<div class="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>
						Name
					</th>
					<th>
						Number Of Tags
					</th>
					<th>
						Assigned User
					</th>
				</tr>
			</thead>
			<?php
			$count=0;
			foreach($tags_venues as $venue) {
				$count++;
				if($venue['assigned_user_id']==$this->user_auth->entityId){
					echo('			<tr class="tr'.(($count%2)+1).'" style="background-color: #CCFFFF">'."\n");
				}else{
					echo('			<tr class="tr'.(($count%2)+1).'">'."\n");
				}
				echo('				<td><a href="/office/reviews/'.$venue['venue_shortname'].'/'.$content_type_codename.'/tags">');
				echo(xml_escape($venue['venue_name']));
				echo('</a></td>'."\n");
				echo('				<td align="center">');
				if($venue['tags_count']>0){
					echo xml_escape($venue['tags_count']);
				}else{
					echo("<img src='/images/prototype/members/no9.png'>");
				}
				echo('</td>'."\n");
				echo('				<td>');
				echo xml_escape($venue['assigned_user_name']);
				echo('</td>'."\n");
				echo('			</tr>'."\n");
			}
			?>
		</table>
	</div>
	<br />
	<h2>Leagues</h2>
	<?php echo $list_text_leagues; ?>
	<div class="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>
						Name
					</th>
					<th>
						Leagues
					</th>
					<th>
						Star Rating
					</th>
					<th>
						Assigned User
					</th>
				</tr>
			</thead>
			<?php
			$count=0;
			foreach($leagues_venues as $venue) {
				$count++;
				if($venue['assigned_user_id']==$this->user_auth->entityId){
					echo('			<tr class="tr'.(($count%2)+1).'" style="background-color: #CCFFFF">'."\n");
				}else{
					echo('			<tr class="tr'.(($count%2)+1).'">'."\n");
				}
				echo('				<td><a href="/office/reviews/'.$venue['venue_shortname'].'/'.$content_type_codename.'/leagues">');
				echo(xml_escape($venue['venue_name']));
				echo('</a></td>'."\n");
				echo('				<td align="center">');
				if($venue['leagues_count']>0){
					echo xml_escape($venue['leagues_count']);
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
				echo xml_escape($venue['assigned_user_name']);
				echo('</td>'."\n");
				echo('			</tr>'."\n");
			}
			?>
		</table>
	</div>
</div>