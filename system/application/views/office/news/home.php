	<div class='RightToolbar'>
		<h4><?php echo $tasks_heading; ?></h4>
		<ul>
			<li><a href='/office/<?php echo $link; ?>/request'><?php echo $tasks['request']; ?></a></li>
		</ul>
		<h4>Photos</h4>
		<br />
		<h4>Photo Requests</h4>
		<br />
		<h4>Revisions</h4>
		<br />
	</div>
	<div class='blue_box'>
		<h2><?php echo $mine_heading; ?></h2>
		<div id='ArticleBox'>
			<table>
			    <thead>
			        <tr>
				        <th>Article Title</th>
				        <th>Reporter</th>
				        <th>Status</th>
	    		    </tr>
			    </thead>
            <tbody>
	  			    <tr class='tr2'>
					    <td><a href='/office/news/test'>Girl found murdered</a></td>
						<td>Unassigned</td>
						<td>Idea</td>
	  			    </tr>
	  			    <tr>
					    <td><a href='/office/news/test'>Fast food restaurant shut down</a></td>
						<td>Joe Bloggs</td>
						<td>Research</td>
	  			    </tr>
	  			    <tr class='tr2'>
					    <td><a href='/office/news/test'>Abseiling from Central Hall</a></td>
						<td>Dan Ashby</td>
						<td>Writing</td>
	  			    </tr>
	  			    <tr>
					    <td><a href='/office/news/test'>Fresher Flu Epidemic</a></td>
						<td>John Doe</td>
						<td>Accepted</td>
	  			    </tr>
			    </tbody>
			</table>
		</div>
	</div>
	<div class='grey_box'>
		<h2><?php echo $section; ?> box...</h2>
		<div id='ArticleBox'>
			<table>
			    <thead>
			        <tr>
				        <th>Article Title</th>
				        <th>Reporter</th>
				        <th>Status</th>
	    		    </tr>
			    </thead>
            <tbody>
				<?php $row_style = false;
				foreach ($box_contents as $request) { ?>
					<tr<?php if ($row_style) { echo ' class=\'tr2\''; }?>>
						<td><a href='/office/news/article/<?php echo $request['id']; ?>'><?php echo $request['title']; ?></a></td>
						<td>
						<?php foreach ($request['reporters'] as $reporter) { echo ($reporter['name'] . '<br />'); } ?>
						</td>
						<td>
						<?php foreach ($request['reporters'] as $reporter) { echo ($reporter['status'] . '<br />'); } ?>
						</td>
					</tr>
				<?php $row_style = !$row_style; } ?>
			    </tbody>
			</table>
		</div>
	</div>
