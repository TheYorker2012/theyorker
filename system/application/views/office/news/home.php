<?php
function PrintRequestList ($data, $parent_type, $suggestion = FALSE) {
	$colCount = 4;
	if ($parent_type)	$colCount++;
	if ($suggestion)	$colCount--;
	$colCount = floor(100 / $colCount);
	echo('		<div class="ArticleBox">'."\n");
	echo('			<table>'."\n");
	echo('			    <thead>'."\n");
	echo('			        <tr>'."\n");
	echo('				        <th style="width:'.$colCount.'%;">Title</th>'."\n");
	if ($parent_type) {
		echo('				        <th style="width:'.$colCount.'%;">Box</th>'."\n");
	}
	if ($suggestion) {
		echo('				        <th style="width:'.$colCount.'%;">Suggested by</th>'."\n");
		echo('				        <th style="text-align:right;width:'.$colCount.'%;">Submitted</th>'."\n");
	} else {
		echo('				        <th style="width:'.$colCount.'%;">Reporters</th>'."\n");
		echo('				        <th style="width:'.$colCount.'%;">Status</th>'."\n");
		echo('				        <th style="text-align:right;width:'.$colCount.'%;">Deadline</th>'."\n");
	}
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
			echo('						<td><a href="/office/news/' . $row['id'] . '/"><img src="/images/prototype/news/article-small.gif" alt="Article Request" title="Article Request" /> ' . $row['title'] . '</a></td>'."\n");
			if ($parent_type) {
				echo('						<td>' . $row['box'] . '</td>'."\n");
			}
			if ($suggestion) {
				echo('						<td>' . $row['suggester'] . '</td>'."\n");
				echo('						<td style="text-align:right;">' . date('d/m/y @ H:i', $row['created']) . '</td>'."\n");
			} else {
				echo('						<td>');
				foreach ($row['reporters'] as $reporter) {
					echo('<img src="/images/prototype/news/person.gif" alt="Reporter" title="Reporter" /> ' . $reporter['name'] . '<br />');
				}
				echo('</td>'."\n");
				echo('						<td>');
				foreach ($row['reporters'] as $reporter) {
					echo('<img src="/images/prototype/news/' . $reporter['status'] . '.gif" alt="' . $reporter['status'] . '" title="' . $reporter['status'] . '" /> ' . $reporter['status'] . '<br />');
				}
				echo('</td>'."\n");
				echo('						<td style="text-align:right;');
				if (mktime() > $row['deadline']) {
					echo('color:red;');
				}
				echo('">' . date('d/m/y @ H:i', $row['deadline']) . '</td>'."\n");
			}
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
		<h4><?php echo $tasks_heading; ?></h4>
		<ul>
			<li><a href="/office/news/request"><?php echo $tasks['request']; ?></a></li>
			<li><a href="/office/news/create">Create New Article</a></li>
		</ul>
	</div>

	<div class="blue_box">
		<h2><?php echo $box_display_name; ?> box...</h2>
		Welcome to this box, there are currently:
		<ul>
			<li><?php echo(count($box_contents['suggestion'])); ?> suggestions</li>
			<li><?php echo(count($box_contents['unassigned'])); ?> unassigned requests</li>
			<li><?php echo(count($box_contents['assigned'])); ?> assigned requests</li>
			<li><?php echo(count($box_contents['ready'])); ?> ready articles</li>
		</ul>
	</div>

	<div class="grey_box" style="width:auto">
		<h2>suggestions...</h2>
		<?php PrintRequestList($box_contents['suggestion'],$parent_type,TRUE); ?>
	</div>

	<div class="grey_box" style="width:auto">
		<h2>unassigned requests...</h2>
		<?php PrintRequestList($box_contents['unassigned'],$parent_type); ?>
	</div>

	<div class="grey_box" style="width:auto">
		<h2>assigned requests...</h2>
		<?php PrintRequestList($box_contents['assigned'],$parent_type); ?>
	</div>

	<div class="grey_box" style="width:auto">
		<h2>ready articles...</h2>
		<?php PrintRequestList($box_contents['ready'],$parent_type); ?>
	</div>
