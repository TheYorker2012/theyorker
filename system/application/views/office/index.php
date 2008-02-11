<?php
function PrintRequestList ($data) {
	echo('		<div class="ArticleBox">'."\n");
	echo('			<table>'."\n");
	echo('			    <thead>'."\n");
	echo('			        <tr>'."\n");
	echo('				        <th style="width:20%;">Title</th>'."\n");
	echo('				        <th style="width:20%;">Box</th>'."\n");
	echo('				        <th style="width:20%;">Assignees</th>'."\n");
	echo('				        <th style="width:20%;">Status</th>'."\n");
	echo('				        <th style="width:20%;text-align:right;">Deadline</th>'."\n");
	echo('	    		    </tr>'."\n");
	echo('			    </thead>'."\n");
	echo('	            <tbody>'."\n");
	$RowStyle = FALSE;
	if (count($data) == 0) {
		echo('						<tr>'."\n");
		echo('							<td colspan="0" style="text-align:center; font-style:italic;">None</td>'."\n");
		echo('						</tr>'."\n");
	} else {
		foreach ($data as $row) {
			if ($row['title'] == '') {
				$row['title'] = '<i>no title</i>';
			}
			echo('					<tr ');
			if ($RowStyle) {
				echo('class="tr2"');
			}
			echo('>'."\n");
			echo('						<td><a href="');
			if ($row['type'] == 'photo') {
				echo('/office/photos/view/');
			} else {
				echo('/office/news/');
			}
			$type_xml = xml_escape($row['type']);
			echo($row['id'] . '/"><img src="/images/prototype/news/'.$type_xml.'-small.gif" alt="'.$type_xml.' Request" title="'.$type_xml.' Request" /> ' . xml_escape($row['title']) . '</a></td>'."\n");
			echo('						<td>' . xml_escape($row['box']) . '</td>'."\n");
			echo('						<td>');
			foreach ($row['reporters'] as $reporter) {
				echo('<img src="/images/prototype/news/person.gif" alt="Assignee" title="Assignee" /> ' . xml_escape($reporter['name']) . '<br />');
			}
			echo('</td>'."\n");
			echo('						<td>');
			foreach ($row['reporters'] as $reporter) {
				$status_xml = xml_escape($reporter['status']);
				echo('<img src="/images/prototype/news/' . $status_xml . '.gif" alt="' . $status_xml . '" title="' . $status_xml . '" /> ' . $status_xml . '<br />');
			}
			echo('</td>'."\n");
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

<div class="RightToolbar">
	<h4>Quick Links</h4>
	<div class="Entry">
		<ul>
			<li><a href="/office/news/create">Create New Article</a></li>
			<li><a href="/office/guide/">View Style Guide</a></li>
			<li><a href="/office/news/contentschedule/">Check Content Schedule</a></li>
		</ul>
	</div>
</div>

<div class='blue_box'>
	<h2>from the editor</h2>
	<p>
		<?php echo($main_text); ?>
	</p>
</div>

<div class="BlueBox" style="width:auto">
	<h2>my tasks...</h2>
	<?php PrintRequestList($my_requests); ?>
</div>
