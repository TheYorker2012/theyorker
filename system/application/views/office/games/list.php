<?php
	function Make_Game_Table($game_array,$is_editor)
	{
		echo('<table><thead><tr>
				<th></th>
				<th width="100%">Title</th>
				<th>Added</th>
				<th style="padding-right:5px;">Count</th>
				<th>Del</th>
				</tr></thead><tbody>');

		$alternate=1;
		
		foreach ($game_array as $game_id=>$game)
		{
			echo('	<tr id="row_'.$game_id.'" class="tr'.$alternate.'">
					<td style="width:14px">');
			if ($is_editor)
			{echo('
				<a
					href="#"
					onclick="xajax_toggle_activation('.$game_id.')">');}
					
			echo('		<img
							id="activation_'.$game_id.'"
							src="');
			if($game['activated'])
			{
				echo('/images/prototype/prefs/success.gif');
			}else{
				echo('/images/prototype/news/delete.gif');
			}
			echo('" />'.($is_editor ? '</a>' : "").'</td>');
			echo('<td style="padding-right:5px"><a href="/office/games/edit/'.$game_id.'">'.(strlen($game['title'])>0 ? xml_escape($game['title']) : '<em>&lt;No Name&gt;</em>').'</a></td>');
			echo('<td style="padding-right:5px;"> '.$game['date_added'].'</td>');
			echo('<td style="padding-right:5px;text-align:right;">'.$game['play_count'].'</td>');
			echo('<td>');
			echo('<a href="/office/games/del_game/'.$game_id.'" onclick="return check_delete(\''.xml_escape($game['title']).'\');">Del</a>');
			echo('</td>');
			echo('</tr>');
			$alternate==1 ? $alternate = 2 : $alternate = 1;
		}
		echo('</tbody></table>');
	}
?>
<script type="text/javascript">
// <![CDATA[
	function add()
	{
		document.getElementById('add_game').style.display="";
		document.getElementById('list_box').style.display="none";
		document.getElementById('add_entry').style.display="none";
		<?php if($incomplete_games != 0) { ?>
		document.getElementById('incomplete_box').style.display="none";'
		<?php } ?>
	}
	
	function hide_add()
	{
		document.getElementById('add_game').style.display="none";
		document.getElementById('list_box').style.display="";
<?php if($incomplete_games != 0) { ?>
		document.getElementById('incomplete_box').style.display="";
<?php } ?>
	}
	
	function add_entry()
	{
		if (document.getElementById('add_entry').style.display!="")
		{
			xajax_list_ftp();
		}
		document.getElementById('list_box').style.display="none";
		document.getElementById('add_game').style.display="none";
		document.getElementById('add_entry').style.display="";
<?php if($incomplete_games != 0) { ?>
		document.getElementById('incomplete_box').style.display="none";
<?php } ?>
	}
	
	function hide_add_entry()
	{		
		document.getElementById('add_entry').style.display="none";
		document.getElementById('list_box').style.display="";
<?php if($incomplete_games != 0) { ?>
		document.getElementById("incomplete_box").style.display="";
<?php } ?>
	}
	
	function list_response()
	{
		for (var i=0;i<arguments.length; i++)
		{
			document.add_entry_form.add_entry_file.options[document.add_entry_form.add_entry_file.options.length] = new Option(arguments[i],arguments[i]);
		}
		document.getElementById('add_entry_wait').style.display="none";
		document.getElementById('add_entry_form_box').style.display="";
	}
	
	function check_delete(title)
	{
		return window.confirm("Are you sure you want to delete the game '"+title+"'?  This action cannot be undone.");
	}
// ]]>
</script>

<div id="RightColumn">
	<h2 class="first"><?php echo(xml_escape($section_games_list_page_info_title)); ?></h2>
	<div class="Entry">
		<?php echo($section_games_list_page_info_text); ?>
	</div>
	<h2><?php echo(xml_escape($section_games_list_actions_title)); ?></h2>
	<div class="Entry">
		<ul>
			<li><a href="#" onclick="add();return false;">Add</a></li>
			<li><a href="#" onclick="add_entry();return false;">Add Entry</a></li>
		</ul>
	</div>
</div>

<div id="MainColumn">

	<div id="add_game" class="BlueBox"  style="Display:none">
	Choose a file to upload below.  The maximum file size is 2Mb.  Please be patient as the upload may take some time to complete.
		<form 
			name="add_game_form"
			id="add_game_form"
			action="/office/games/add"
			method="post" 
			class="form"
			enctype="multipart/form-data">
			<fieldset>
				<input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
				<label for="add_game_file">Upload File:</label>
				<input
					type="file"
					name="add_game_file"
					id="add_game_file"
					size='20' />
				<input type='submit' name='submit' id='submit' value='Add' class='button' />
			</fieldset>
		</form>
		<a href="#" onclick="hide_add();return false;">Hide</a>
	</div>
	
	<div id="add_entry" class="BlueBox" style="Display:none">
		<div id="add_entry_wait">
			Please Wait... connecting to FTP share...
		</div>
		<div id="add_entry_form_box" style="Display:none">
			Select the game file from the list below to add it to the system.
			<form 
				name="add_entry_form"
				id="add_entry_form"
				action="/office/games/add_entry"
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
	
	<?php
		if($incomplete_games != 0)
		{
			echo('<div class="BlueBox" id="incomplete_box"><div class="ArticleBox">');
			echo('<h2>'.$section_games_list_incomplete_title.'</h2>');
			Make_Game_Table($incomplete_games,$is_editor);
			echo('</div></div>');
		}
	?>
	
	<div class="BlueBox" id="list_box">
		<?php echo($this->pagination->create_links()); ?>
		<div>
			Viewing
			<?php 
				echo(
					($offset + 1) .
					' - ' .
					((($offset + $per_page) <= $total) ? ($offset + $per_page) : $total) .
					' of ' . $total .
					' games');
			?>
		</div>
		<div style="border-bottom:1px #999 solid;clear:both"></div>
		
		<div class="ArticleBox">
			<?php Make_Game_Table($games,$is_editor); ?>
		</div>
		
		<?php echo($this->pagination->create_links()); ?>
		<div>
			Viewing
			<?php 
				echo(
					($offset + 1) .
					' - ' .
					((($offset + 10) <= $total) ? ($offset + 10) : $total) .
					' of ' . $total .
					' games');
			?>
		</div>
	</div>
</div>
