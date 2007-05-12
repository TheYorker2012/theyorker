<?php
function PrintRequestList ($data, $AssignedColumn = FALSE) {
	echo('		<div id="ArticleBox">'."\n");
	echo('			<table>'."\n");
	echo('			    <thead>'."\n");
	echo('			        <tr>'."\n");
	echo('				        <th>Request Title</th>'."\n");
	if ($AssignedColumn) {
		echo('				        <th>Photographer</th>'."\n");
	}
	echo('				        <th style="text-align:right;">Submission Date</th>'."\n");
	echo('	    		    </tr>'."\n");
	echo('			    </thead>'."\n");
	echo('	            <tbody>'."\n");
	$RowStyle = FALSE;
	if (count($data) == 0) {
		echo('						<tr>');
		echo('							<td colspan="0" style="text-align:center;">No requests in this section...</td>');
		echo('						</tr>');
	} else {
		foreach ($data as $row) {
			echo('					<tr ');
			if ($RowStyle) {
				echo('class="tr2"');
			}
			echo('>'."\n");
			echo('						<td><a href="/office/photos/view/' . $row['id'] . '/">' . $row['title'] . '</a></td>'."\n");
			if ($AssignedColumn) {
				echo('						<td>' . $row['user_name'] . '</td>'."\n");
			}
			echo('						<td style="text-align:right;">' . date('d/m/y @ H:i', $row['time']) . '</td>'."\n");
			echo('					</tr>'."\n");
			$RowStyle = !$RowStyle;
		}
	}
	echo('			    </tbody>'."\n");
	echo('			</table>'."\n");
	echo('		</div>'."\n");
}
?>

	<div class="RightToolbar">
		<h4>Heading</h4>
		Hey, not sure what should go here but without this toolbar the page becomes quite sparse
		and looks wierd... soooooo this is here.... urm.... not sure what to say really... apart
		from that this is where all the un-completed photo requests are displayed so that photographers
		and the photo editor can see the status of their department. Oh and 24 is uber 1337 :P
		
		<h4>Status: Requested</h4>
		<ul>
		<li><a href="/office/photos/view/editor">Editor</a></li>
		<li><a href="/office/photos/view/reporter">Assigned Reporter</a></li>
		<li><a href="/office/photos/view/photographer">Assigned Photographer</a></li>
		<li><a href="/office/photos/view/all">All office users</a></li>
		</ul>

		<h4>Status: Ready</h4>
		<ul>
		<li><a href="/office/photos/view/flagged">Review photo request</a></li>
		</ul>

		<h4>Status: Completed</h4>
		<ul>
		<li><a href="/office/photos/view/completed">Request History</a></li>
		</ul>
	</div>

	<div class="blue_box">
		<h2>Unassigned</h2>
		<?php PrintRequestList($requests['unassigned']); ?>
	</div>

	<div class="grey_box">
		<h2>Assigned</h2>
		<?php PrintRequestList($requests['assigned'], TRUE); ?>
	</div>

	<div class="blue_box">
		<h2>Ready</h2>
		<?php PrintRequestList($requests['ready'], TRUE); ?>
	</div>

	<!--<pre><?php var_dump($requests); ?></pre>-->
