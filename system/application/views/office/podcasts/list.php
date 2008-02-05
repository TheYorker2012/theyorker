<script type="text/javascript">
	function add_entry()
	{
		if (document.getElementById('add_entry').style.display!="")
		{
			xajax_list_ftp();
		}
		document.getElementById('list_box').style.display="none";
		document.getElementById('add_entry').style.display="";
	}
	
	function list_response()
	{
		for (var i=0;i<arguments.length; i++)
		{
			document.add_entry_form.add_entry_file.options[
				document.add_entry_form.add_entry_file.options.length] =
					new Option(arguments[i],arguments[i]);
		}
		document.getElementById('add_entry_wait').style.display="none";
		document.getElementById('add_entry_form_box').style.display="";
	}
	
	function hide_add_entry()
	{		
		document.getElementById('add_entry').style.display="none";
		document.getElementById('list_box').style.display="";
	}
</script>

<div id="RightColumn">
	<h4 class="first"><?php echo(xml_escape($page_info_title)); ?></h4>
	<div class="Entry">
		<?php echo($page_info_text); ?>
	</div>
	<h4><?php echo(xml_escape($actions_title)); ?></h4>
	<ul>
		<li><a href="#" onclick="add_entry(); return false;">Add Entry</a></li>
	</ul>
</div>

<div id="MainColumn">
	<div id="add_entry" class="BlueBox" style="Display:none">
		<div id="add_entry_wait">
			Please Wait... connecting to FTP share...
		</div>
		<div id="add_entry_form_box" style="Display:none">
			Select the podcasts file from the list below to add it to the system.
			<form 
				name="add_entry_form"
				id="add_entry_form"
				action="/office/podcasts/add_entry"
				method="post"
				class="form">
				<fieldset>
					<label for="add_entry_file">File:</label>
					<select name="add_entry_file">
					</select>
					<input type='submit' name='entry_submit' id='entry_submit' value='Add' class='button' />
				</fieldset>
			</form>
		</div>
		<a href="#" onclick="hide_add_entry();return false;">Hide</a>
	</div>

	<div id="list_box" class="BlueBox">
		<div class="ArticleBox">
			<table>
				<thead>
					<tr>
						<th style="width:100%;padding-right:5px;">
							Title
						</th>
						<th style="text-align:right;padding-right:5px;">
							Is&nbsp;Live
						</th>
						<th>
							Del
						</th>
					</tr>
				</thead>
				<tbody>
<?php
	foreach ($podcasts as $podcast) {
		$podcast['is_live'] ? $is_live = "Yes" : $is_live = "No";
		strlen($podcast['name'])>0 ? $name=$podcast['name'] : $name='<em>No Name</em>';
?>
					<tr>
						<td>
							<?php echo('<a href="/office/podcasts/edit/'.$podcast['id'].'">'.xml_escape($name).'</a>'); ?>
						</td>
						<td style="text-align:right;padding-right:5px;">
							<?php echo($is_live); ?>
						</td>
						<td>
							<a href="/office/podcasts/del/<?php echo($podcast['id']); ?>">Del</a>
					</tr>
<?php
	}
?>
				</tbody>
			</table>
		</div>
	</div>
</div>
