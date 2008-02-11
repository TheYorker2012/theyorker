<div id="RightColumn">
	<h2 class="first">Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>current tags</h2>
		<table>
			<thead>
				<tr>
					<th>Tag</th><th>Group</th><th>Section</th><th>Order</th><th>Edit</th><th>Del</th>
				</tr>
			</thead>
			<?php
			foreach($tags as $tag){
				echo('<tr>');
				echo('<td>');
				echo(xml_escape($tag['name']));
				echo('</td>');
				echo('<td>'.xml_escape($tag['group_name']).'</td>');
				echo('<td>'.xml_escape($tag['content_type_name']).'</td>');
				echo('</td>');
				echo('<td>');
				if($tag['group_ordered']){
					echo("<a href='/office/reviewtags/moveup/".$tag['id']."'><img src='/images/prototype/members/sortdesc.png'></a>");
					echo("<a href='/office/reviewtags/movedown/".$tag['id']."'><img src='/images/prototype/members/sortasc.png'></a>");
				}
				echo('</td>');
				echo('<td><a href="/office/reviewtags/edit/'.$tag['id'].'">Edit</a></td>');
				echo('<td><a href="/office/reviewtags/delete/'.$tag['id'].'">Del</a></td>');
				echo('</tr>');
			}
			?>
		</table>
	</div>
	<div class="BlueBox">
		<h2>current tag groups</h2>
		<table>
			<thead>
				<tr>
					<th>Group</th><th>Section</th><th>Ordered</th><th>Order</th><th>Edit</th><th>Del</th>
				</tr>
			</thead>
			<?php
			foreach($tag_groups as $tag_group){
				echo('<tr>');
				echo('<td>'.xml_escape($tag_group['group_name']).'</td>');
				echo('<td>'.xml_escape($tag_group['content_type_name']).'</td>');
				echo('</td>');
				echo('<td>');
				if($tag_group['group_ordered']) {
					echo("<img src='/images/prototype/members/confirmed.png'>");
				} else {
					echo("<img src='/images/prototype/members/no9.png'>");
				}
				echo('</td>');
				echo('<td>');
				echo("<a href='/office/reviewtags/movegroupup/".$tag_group['group_id']."'><img src='/images/prototype/members/sortdesc.png'></a>");
				echo("<a href='/office/reviewtags/movegroupdown/".$tag_group['group_id']."'><img src='/images/prototype/members/sortasc.png'></a>");
				echo('</td>');
				echo('<td><a href="/office/reviewtags/editgroup/'.$tag_group['group_id'].'">Edit</a></td>');
				echo('<td><a href="/office/reviewtags/deletegroup/'.$tag_group['group_id'].'">Del</a></td>');
				echo('</tr>');
			}
			?>
		</table>
	</div>
	<div class="BlueBox">
		<h2>create new tag</h2>
		<form method="post" action="/office/reviewtags">
			<fieldset>
				<label for="tag_name">Name:</label>
				<input type="text" name="tag_name" value="<?php
				if(!empty($tag_form['tag_name'])) {
					echo(xml_escape($tag_form['tag_name']));
				}
				?>" />
				<label for="tag_group_id">Group:</label>
				<select name="tag_group_id"><?php
				foreach ($tag_groups as $tag_group) {
					echo('					<option value="'.$tag_group['group_id'].'"');
					if(!empty($tag_form['tag_group_id'])) {
						if ($tag_group['group_id'] == $tag_form['tag_group_id']) {
							echo('selected="selected"');
						}
					}
					echo('>'."\n");
					echo('						'.xml_escape($tag_group['content_type_name']).'->'.xml_escape($tag_group['group_name'])."\n");
					echo('					</option>'."\n");
				}?>
				</select>
			</fieldset>
			<fieldset>
				<input name="tag_add" type="submit" value="Add" class="button" />
			</fieldset>
		</form>
	</div>
	<div class="BlueBox">
		<h2>create new group</h2>
		<form method="post" action="/office/reviewtags">
			<fieldset>
				<label for="tag_group_name">Name:</label>
				<input type="text" name="tag_group_name" value="<?php
				if(!empty($tag_group_form['tag_group_name'])) {
					echo(xml_escape($tag_group_form['tag_group_name']));
				}
				?>" />
				<label for="content_type_id">Section:</label>
				<select name="content_type_id"><?php
				foreach ($group_types as $group_type) {
					echo('					<option value="'.$group_type['type_id'].'"');
					if(!empty($tag_group_form['content_type_id'])) {
						if ($group_type['type_id']==$tag_group_form['content_type_id']) {
							echo('selected="selected"');
						}
					}
					echo('>'."\n");
					echo('						'.xml_escape($group_type['type_name'])."\n");
					echo('					</option>'."\n");
				}?>
				</select>
				<label for="tag_group_ordered">Ordered:</label>
				<input type="checkbox" name="tag_group_ordered" value="1" <?php
				if(empty($tag_group_form) || !empty($tag_group_form['tag_group_ordered'])) {
					echo('checked="checked"');
				}
				?>/>
			</fieldset>
			<fieldset>
				<input name="tag_group_add" type="submit" value="Add" class="button" />
			</fieldset>
		</form>
	</div>
</div>
