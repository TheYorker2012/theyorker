	<div class="RightToolbar">
		<h4><?php echo $tasks_heading; ?></h4>
		<ul>
			<li><a href="/office/news/request"><?php echo $tasks['request']; ?></a></li>
		</ul>
		<h4>Notices</h4>
<!--
		<div class="information_box">
			You have been requested to write an article about <b>Dogs</b> by <b>Chris Travis</b>
			<br />
			<span style="float:right"><a href="/office/news">Delete</a></span>
			<br class='clear' />
		</div>
-->
		<br />
	</div>
	<div class="blue_box">
		<h2><?php echo $mine_heading; ?></h2>
		<div id="ArticleBox">
			<table>
			    <thead>
			        <tr>
				        <th>Article Title</th>
				        <th>Box</th>
				        <th>Reporters</th>
				        <th>Status</th>
	    		    </tr>
			    </thead>
            <tbody>
				<?php if (count($my_requests) == 0) { ?>
				<tr>
				<td colspan="4" style="text-align:center;">You have no requests</td>
				</tr>
				<?php } else {
					$row_style = false;
					foreach ($my_requests as $request) { ?>
						<tr<?php if ($row_style) { echo ' class="tr2"'; }?>>
							<td><a href="/office/news/<?php echo $request['id']; ?>"><?php echo $request['title']; ?></a></td>
							<td><?php echo $request['box']; ?></td>
							<td><?php foreach ($request['reporters'] as $reporter) { echo ($reporter['name'] . '<br />'); } ?></td>
							<td><?php foreach ($request['reporters'] as $reporter) { echo ($reporter['status'] . '<br />'); } ?></td>
						</tr>
					<?php $row_style = !$row_style;
					}
				} ?>
			    </tbody>
			</table>
		</div>
	</div>
	<div class="grey_box">
		<h2><?php echo $box_display_name; ?> box...</h2>
		<div id="ArticleBox">
			<table>
			    <thead>
			        <tr>
				        <th>Article Title</th>
						<?php if ($parent_type) { ?><th>Box</th><?php } ?>
				        <th>Reporters</th>
				        <th>Status</th>
	    		    </tr>
			    </thead>
            <tbody>
				<?php if ((count($box_contents) == 0) && (count($suggestions) == 0)) { ?>
				<tr>
				<td colspan="0" style="text-align:center;">Box is empty</td>
				</tr>
				<?php } else {
					$row_style = false;
					foreach ($suggestions as $suggestion) { ?>
						<tr<?php if ($row_style) { echo ' class="tr2"'; }?>>
							<td><a href="/office/news/<?php echo $suggestion['id']; ?>"><?php echo $suggestion['title']; ?></a></td>
							<?php if ($parent_type) { ?><td><?php echo $suggestion['box']; ?></td><?php } ?>
							<td><?php echo $suggestion['username']; ?></td>
							<td>suggestion</td>
						</tr>
					<?php $row_style = !$row_style;
					}
					foreach ($box_contents as $request) { ?>
						<tr<?php if ($row_style) { echo ' class="tr2"'; }?>>
							<td><a href="/office/news/<?php echo $request['id']; ?>"><?php echo $request['title']; ?></a></td>
							<?php if ($parent_type) { ?><td><?php echo $request['box']; ?></td><?php } ?>
							<td>
							<?php foreach ($request['reporters'] as $reporter) { echo ($reporter['name'] . '<br />'); } ?>
							</td>
							<td>
							<?php foreach ($request['reporters'] as $reporter) { echo ($reporter['status'] . '<br />'); } ?>
							</td>
						</tr>
					<?php $row_style = !$row_style;
					}
				} ?>
			    </tbody>
			</table>
		</div>
	</div>
