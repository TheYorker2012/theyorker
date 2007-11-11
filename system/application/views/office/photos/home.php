<?php
function PrintRequestList ($data, $AssignedColumn = FALSE) {
	$colCount = 5;
	$colCount = floor(100 / $colCount);
	echo('		<div class="ArticleBox">'."\n");
	echo('			<table>'."\n");
	echo('			    <thead>'."\n");
	echo('			        <tr>'."\n");
	echo('				        <th style="width:'.$colCount.'%;">Request Title</th>'."\n");
	echo('				        <th style="width:'.$colCount.'%;">Photographer</th>'."\n");
	echo('				        <th style="width:'.$colCount.'%;">Status</th>'."\n");
	echo('				        <th style="width:'.$colCount.'%;">Submission Date</th>'."\n");
	echo('				        <th style="width:'.$colCount.'%;text-align:right;">Article Deadline</th>'."\n");
	echo('	    		    </tr>'."\n");
	echo('			    </thead>'."\n");
	echo('	            <tbody>'."\n");
	$RowStyle = FALSE;
	if (count($data) == 0) {
		echo('						<tr>');
		echo('							<td colspan="0" style="text-align:center; font-style:italic;">No requests in this section...</td>');
		echo('						</tr>');
	} else {
		foreach ($data as $row) {
			echo('					<tr ');
			if ($RowStyle) {
				echo('class="tr2"');
			}
			echo('>'."\n");
			echo('						<td><a href="/office/photos/view/' . $row['id'] . '/"><img src="/images/prototype/news/photo-small.gif" alt="Photo Request" title="Photo Request" /> ' . $row['title'] . '</a></td>'."\n");
			echo('						<td>');
			if ($row['user_name'] != '') {
				echo('<img src="/images/prototype/news/person.gif" alt="Photographer" title="Photographer" /> '.$row['user_name']);
			}
			echo('</td>'."\n");
			echo('						<td>');
			if ($row['user_status'] != '') {
				echo('<img src="/images/prototype/news/' . $row['user_status'] . '.gif" alt="' . $row['user_status'] . '" title="' . $row['user_status'] . '" /> ' . $row['user_status']);
			}
			echo('</td>');
			echo('						<td>' . date('d/m/y @ H:i', $row['time']) . '</td>'."\n");
			echo('						<td style="text-align:right;');
			if (mktime() > $row['deadline']) {
				echo('color:red;');
			}
			echo('">' . date('d/m/y @ H:i', $row['deadline']) . '</td>'."\n");
			echo('					</tr>'."\n");
			$RowStyle = !$RowStyle;
		}
	}
	echo('			    </tbody>'."\n");
	echo('			</table>'."\n");
	echo('		</div>'."\n");
}
?>

	<div class="blue_box" style="width:auto">
		<h2>unassigned</h2>
		<?php PrintRequestList($requests['unassigned']); ?>
	</div>

	<div class="grey_box" style="width:auto">
		<h2>assigned</h2>
		<?php PrintRequestList($requests['assigned'], TRUE); ?>
	</div>

	<div class="blue_box" style="width:auto">
		<h2>ready</h2>
		<?php PrintRequestList($requests['ready'], TRUE); ?>
	</div>
