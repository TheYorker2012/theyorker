<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
	<h2>Related Actions</h2>
	<div class="Entry">
		<ul>
			<li><a href="/office/leagues/edit/<?php echo($league_id); ?>">Edit this league</a></li>
			<li><a href="/office/reviewtags">Create/Edit Tags.</a></li>
		</ul>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>league contents</h2>
		<p>This league has a maximum of <?php echo($venues_limit); ?> venues, any venues over this limit will not be displayed.</p>
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
				if($index > $venues_limit) {
					echo('		<td><span class="red"><b>'.$index.')</b></span></td>'."\n");
				} else {
					echo('		<td><b>'.$index.')</b></td>'."\n");
				}
				echo('		<td>'."\n");
				echo('			<a href="/office/reviews/'.$venue['codename'].'/'.$venue['section_codename'].'/review">'.xml_escape($venue['name']).'</a>'."\n");
				echo('		</td>'."\n");
				echo('		<td>'."\n");
				echo("			<a href='/office/league/moveup/".$venue['league_id']."/".$venue['id']."'><img src='/images/prototype/members/sortdesc.png'></a>"."\n");
				echo("			<a href='/office/league/movedown/".$venue['league_id']."/".$venue['id']."'><img src='/images/prototype/members/sortasc.png'></a>"."\n");
				echo('		</td>'."\n");
				echo('		<td><a href="/office/league/delete/'.$venue['league_id'].'/'.$venue['id'].'" ');
				echo('onclick="return(confirm ('.xml_escape(js_literalise('Are you sure you want to remove '.$venue['name'].' from this league?')).'));"');
				echo('>Remove</a></td>'."\n");
				echo('	</tr>'."\n");
				++$index;
			}
			?>
		</table>
	</div>
	<div class="BlueBox">
	<h2>suggested venues</h2>
	<?php 
	echo($suggestion_information);
	if(!empty($suggestions)){
	?>
		<form method="post" action="/office/league/edit/<?php echo($league_id); ?>">
			<table>
				<thead>
					<tr>
						<th>Venue Name</th><th>Tags</th><th>Rating</th><th>Add</th>
					</tr>
				</thead>
				<?php
				$index=0;
				foreach($suggestions as $suggestion){
					echo('	<tr>'."\n");
					echo('		<td>'."\n");
					echo('			<a href="/office/reviews/'.$suggestion['venue_shortname'].'/'.$suggestion['section_codename'].'/review">'.xml_escape($suggestion['venue_name']).'</a>'."\n");
					echo('		</td>'."\n");
					echo('		<td>'."\n");
					$count=0;
					foreach ($suggestion['tags'] as $tag){
						if($count>0) echo ", ";
						echo "'".xml_escape($tag['tag_name'])."'";
						$count++;
					}
					echo('		</td>'."\n");
					echo('		<td>'."\n");
					if(!empty($suggestion['venue_rating'])){
						$whole = floor($suggestion['venue_rating'] / 2);
						$part = $suggestion['venue_rating'] % 2;
						$empty = 5 - $whole - $part;
						for($i=0;$i<$whole;$i++)
						{
							echo('<img src="/images/prototype/reviews/star.png" width="10" alt="*" title="*" />');
						}
						if ($part == 1)
						{
							echo('<img src="/images/prototype/reviews/halfstar.png" width="10" alt="-" title="-" />');
						}
						for($i=0;$i<$empty;$i++)
						{
							echo('<img src="/images/prototype/reviews/emptystar.png" width="10" alt=" " title=" " />');
						}
					}else{
						echo("&nbsp;");
					}
					echo('		</td>'."\n");
					echo('		<td><input type="checkbox" name="venue_add_'.$index.'" value="'.$suggestion['venue_id'].'" /></td>'."\n");
					echo('	</tr>'."\n");
					$index++;
				}
				?>
				<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td><input name="venue_add" type="submit" value="Add" class="button" /></td></tr>
			</table>
			<input type="hidden" name="venue_add_max" value="<?php echo($index); ?>" />
		</form>
		<?php
		}
		?>
	</div>
	<a href='/office/leagues/'>Back To Leagues</a>
</div>