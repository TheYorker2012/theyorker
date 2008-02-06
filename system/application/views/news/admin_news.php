<div id='newsnav'>
	<ul id='newsnavlist'>
	<li><a href='<?php echo site_url('admin/'); ?>'><img src='/images/prototype/news/archive.png' alt='Admin' title='Admin' /> Admin</a></li>
	<li><a href='<?php echo site_url('admin/news/'); ?>' id='current'><img src='/images/prototype/news/uk.png' alt='News' title='News' /> News</a></li>
	</ul>
</div>
<div id='clear'>&nbsp;</div>

<form name='' id='' action='' method='' class='form'>
    <fieldset>
        <legend>Box : News</legend>
		<table id='ArticleBox'>
		    <thead>
		        <tr>
			        <th>Article Title</th>
			        <th>Reporter</th>
			        <th>Status</th>
			        <th>&nbsp;</th>
    		    </tr>
		    </thead>
		    <tbody>
  			    <tr>
				    <td>He's been shagging girls for years</td>
					<td>Ian Benest</td>
					<td>Review</td>
					<td><?php echo anchor('admin/news/request/view/1', 'View...'); ?></td>
  			    </tr>
  			    <tr class='tr2'>
				    <td>Girl found murdered</td>
					<td>Unassigned</td>
					<td>Idea</td>
					<td><?php echo anchor('admin/news/request/view/1', 'View...'); ?></td>
  			    </tr>
  			    <tr>
				    <td>Fast food restaurant shut down</td>
					<td>Joe Bloggs</td>
					<td>Research</td>
					<td><?php echo anchor('admin/news/request/view/1', 'View...'); ?></td>
  			    </tr>
  			    <tr class='tr2'>
				    <td>Abseiling from Central Hall</td>
					<td>Dan Ashby</td>
					<td>Writing</td>
					<td><?php echo anchor('admin/news/request/view/1', 'View...'); ?></td>
  			    </tr>
  			    <tr>
				    <td>Fresher Flu Epidemic</td>
					<td>John Doe</td>
					<td>Accepted</td>
					<td><?php echo anchor('admin/news/request/view/1', 'View...'); ?></td>
  			    </tr>
		    </tbody>
		</table>
		<div class='link_right'>
		    <label for='n_box'>View other box:</label>
			<select name='n_box' id='n_box' size='1'>
  			    <option value='News' selected='selected'>News</option>
  		   		<option value='Features'>Features</option>
				<option value='Lifestyle'>Lifestyle</option>
				<option value='Photographs'>Photographs</option>
			</select>
		</div>
    </fieldset>
    
    <fieldset>
        <legend>My Assignments</legend>
		<table id='ArticleBox'>
		    <thead>
		        <tr>
			        <th>Article Title</th>
			        <th>Box</th>
			        <th>Status</th>
			        <th>&nbsp;</th>
    		    </tr>
		    </thead>
		    <tbody>
  			    <tr>
				    <td>Fast food restaurant shut down</td>
					<td>News</td>
					<td>Research</td>
					<td><?php echo anchor('admin/news/request/view/1', 'View...'); ?></td>
  			    </tr>
  			    <tr class='tr2'>
				    <td>Chocolate Cake Recipe</td>
					<td>Lifestyle</td>
					<td>Idea</td>
					<td><?php echo anchor('admin/news/request/view/1', 'View...'); ?></td>
  			    </tr>
  			    <tr>
				    <td>Workout Routine - Week 10</td>
					<td>Lifestyle</td>
					<td>Review</td>
					<td><?php echo anchor('admin/news/request/view/1', 'View...'); ?></td>
  			    </tr>
  			    <tr class='tr2'>
				    <td>Student Spending</td>
					<td>Feature</td>
					<td>Draft</td>
					<td><?php echo anchor('admin/news/request/view/1', 'View...'); ?></td>
  			    </tr>
		    </tbody>
		</table>
    </fieldset>

    <fieldset>
        <legend>I want to...</legend>
        <ul id='TaskList'>
            <li><?php echo anchor('admin/news/request/new', 'Create a new request for someone else'); ?></li>
            <li><?php echo anchor('admin/news/article/new', 'Create a new article for myself'); ?></li>
        </ul>
    </fieldset>
</form>