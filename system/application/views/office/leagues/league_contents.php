<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
	<h2>Related Actions</h2>
	<ul>
		<li><a href="/office/leagues/edit/<?php echo $league_id; ?>">Edit this league</a></li>
	</ul>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>league contents</h2>
		<p>This league has a maximum of <?php echo $venues_limit; ?> venues, any venues over this limit will not be displayed.</p>
		<table>
			<thead>
				<tr>
					<th>Rank</th><th>Venue Name</th><th>Order</th><th>Remove</th>
				</tr>
			</thead>
			<?php
			$index=1;
			foreach($venues as $venue){
				echo('	<tr>'."\n");
				if($index > $venues_limit){
					echo('		<td><span class="red"><b>'.$index.')</b></span></td>'."\n");
				}else{
					echo('		<td><b>'.$index.')</b></td>'."\n");
				}
				echo('		<td>'."\n");
				echo('			<a href="/office/reviews/'.$venue['codename'].'/'.$venue['section_codename'].'/review">'.$venue['name'].'</a>'."\n");
				echo('		</td>'."\n");
				echo('		<td>'."\n");
				echo("			<a href='/office/league/moveup/".$venue['league_id']."/".$venue['id']."'><img src='/images/prototype/members/sortdesc.png'></a>"."\n");
				echo("			<a href='/office/league/movedown/".$venue['league_id']."/".$venue['id']."'><img src='/images/prototype/members/sortasc.png'></a>"."\n");
				echo('		</td>'."\n");
				echo('		<td><a href="/office/league/delete/'.$venue['league_id'].'/'.$venue['id'].'" ');
				?>onclick="return(confirm ('Are you sure you want to remove <?php echo $venue['name']; ?> from this league?'));" <?php
				echo('>Remove</a></td>'."\n");
				echo('	</tr>'."\n");
				$index++;
			}
			?>
		</table>
	</div>
	<div class="BlueBox">
	<h2>suggested venues</h2>
	<?php echo $suggestion_information; //echo print_r($suggestions);
	?>
		<table>
			<thead>
				<tr>
					<th>Venue Name</th><th>Tags</th><th>Add</th>
				</tr>
			</thead>
			<?php
			foreach($suggestions as $suggestion){
				echo('	<tr>'."\n");
				echo('		<td>'."\n");
				echo('			<a href="/office/reviews/'.$suggestion['venue_shortname'].'/'.$suggestion['section_codename'].'/review">'.$suggestion['venue_name'].'</a>'."\n");
				echo('		</td>'."\n");
				echo('		<td>'."\n");
				$count=0;
				foreach ($suggestion['tags'] as $tag){
					if($count>0) echo ", ";
					echo "'".$tag['tag_name']."'";
					$count++;
				}
				echo('		</td>'."\n");
				echo('		<td><a href="/office/league/add/'.$league_id.'/'.$suggestion['venue_id'].'" >Add</a></td>'."\n");
				echo('	</tr>'."\n");
			}
			?>
		</table>
	</div>
	<a href='/office/leagues/'>Back To Leagues</a>
</div>