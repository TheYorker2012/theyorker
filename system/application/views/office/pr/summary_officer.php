<div id="RightColumn">
	<h2 class="first">Quick Links</h2>
	<div class="Entry">
		<ul>
			<li><a href="/wizard/organisation/">Organisations wizard</a></li>
			<li><a href="/office/pr/summaryall/">All organisations summary</a></li>
		</ul>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>officer summary</h2>
		<div id="ArticleBox">
			<table>
				<thead>
					<tr>
						<th>PR Rep</th>
						<th>Rating</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$alternate = 1;
						foreach($reps as $rep)
						{
							echo('				<tr class="tr'.$alternate.'">'."\n");
							echo('					<td>'."\n");
							echo('						<a href="/office/pr/summaryrep/'.$rep['user_id'].'">'.xml_escape($rep['user_firstname'].' '.$rep['user_surname']).'</a>'."\n");
							echo('					</td>'."\n");
							echo('					<td>'."\n");
							echo('						10%'."\n");
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
