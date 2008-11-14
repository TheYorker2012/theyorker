<div id="RightColumn">
	<h2 class="first">Quick Links</h2>
	<div class="Entry">
		<ul>
			<li><a href="/wizard/organisation/">Organisations wizard</a></li>
		</ul>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>rep summary</h2>
		<div id="ArticleBox">
			<table>
				<thead>
					<tr>
						<th>Organisation</th>
						<th>Priority</th>
						<th>Rating</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$alternate = 1;
					foreach($orgs as $org)
					{
						echo('				<tr class="tr'.$alternate.'">'."\n");
						echo('					<td>'."\n");
						echo('						<a href="/office/pr/summaryorg/'.$org['org_dir_entry_name'].'">'.xml_escape($org['org_name']).'</a>'."\n");
						echo('					</td>'."\n");
						echo('					<td>'."\n");
						echo('						'.$org['org_priority']."\n");
						echo('					</td>'."\n");
						echo('					<td>'."\n");
						echo('						100%'."\n");
						echo('					</td>'."\n");
						echo('				</tr>'."\n");
						$alternate == 1 ? $alternate = 2 : $alternate = 1;
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
