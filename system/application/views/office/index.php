<?php
function PrintRequestList ($data) {
	echo('		<div class="ArticleBox">'."\n");
	echo('			<table>'."\n");
	echo('			    <thead>'."\n");
	echo('			        <tr>'."\n");
	echo('				        <th style="width:20%;">Title</th>'."\n");
	echo('				        <th style="width:20%;">Box</th>'."\n");
	echo('				        <th style="width:20%;">Reporters</th>'."\n");
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
			echo('						<td><a href="/office/news/' . $row['id'] . '/">' . $row['title'] . '</a></td>'."\n");
			echo('						<td>' . $row['box'] . '</td>'."\n");
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
	<h4>Forgotten your Password</h4>
	<div class="Entry">
		If you have forgotten your password, get in contact with your editor, and he will reset it.
	</div>
	<h4>Get Involved</h4>
	<div class="Entry">
		If you would like to get involved in writing for the yorker, click <a href='/office/register/'>here</a>.
	</div>
</div>


<div class='grey_box'>
	<h2>welcome</h2>
	<p>
		<?php echo $main_text; ?>
	</p>
</div>
<div class="blue_box">
	<h2>office home page</h2>
	<a href="/office/faq/add">Add faq</a><br />
	<a href="/office/howdoi/add">Add to how do I</a><br />
	<a href="/office/faq/edit">Edit FAQ entry</a><br />
	<br />
	<a href="/office/news/request">Make new article request</a><br />
	<a href="/office/news/article">View/Edit Article</a><br />
	<a href="/office/gallery">Gallery</a><br />
	<br />
	<a href="/admin/pages">Page properties, custom pages, etc.</a><br />
</div>

<div class="grey_box" style="width:auto">
	<h2>my article requests...</h2>
	<?php PrintRequestList($my_requests); ?>
</div>
