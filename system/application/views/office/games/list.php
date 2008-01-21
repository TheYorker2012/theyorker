<?php
	function Make_Game_Table($game_array)
	{
		echo('<table><thead><tr>
				<th></th>
				<th width="100%">Title</th>
				<th>Added</th>
				<th>Count</th>
				<th>Edit</th>
				<th>Del</th>
				</tr></thead><tbody>');

		$alternate=1;
		foreach ($game_array as $game_id=>$game)
		{
			echo('<tr id="row_'.$game_id.'" class="tr'.$alternate.'">');
			echo('
				<td width="14px">
				<a
					href="#"
					onclick="xajax_toggle_activation('.$game_id.')">
						<img
							id="activation_'.$game_id.'"
							src="');
			if($game['activated'])
			{
				echo('/images/prototype/prefs/success.gif');
			}else{
				echo('/images/prototype/news/delete.gif');
			}
			echo('" /></a></td>');
			echo('<td style="padding-right:5px">'.$game['title'].'</td>');
			echo('<td> '.$game['date_added'].'</td>');
			echo('<td>'.$game['play_count'].'</td>');
			echo('<td><a href="/office/games/edit/'.$game_id.'">Edit</a></td>');
			echo('<td>');
			// echo('<a href="#" onclick="del_game('.$game_id.')">Del</a>');
			echo('</td>');
			echo('</tr>');
			$alternate==1 ? $alternate = 2 : $alternate = 1;
		}
		echo('</tbody></table>');
	}
?>

<script type="text/javascript">
	function del_game(id)
	{
		if(confirm("Are you sure you want to delete this game?"))
		{
			xajax_del_game(id);
		}
		return false;
	}
	
</script>



<div class="RightToolbar">
	<h4><?php echo($section_games_list_page_info_title); ?></h4>
	<?php echo($section_games_list_page_info_text); ?>
	<h4><?php echo($section_games_list_actions_title); ?></h4>
	<form name='add_game_form' id='add_game_form' action='/office/games/add' method='post' class='form' enctype="multipart/form-data">
		<fieldset>
			<!--<input type="file" name="new_game_file_field" /> -->
			<br />
			<!-- some clever code needs to replace the following, but depending on how the file is handled server side as to best way! -->
			<!-- <input type="submit" name="submit" value="Add"> -->
		</fieldset>
	</form>

</div>

<div id="MainColumn">
	
	<?php
		if($incomplete_games != 0)
		{
			echo('<div class="BlueBox"><div class="ArticleBox">');
			echo('<h2>Incomplete Game Entries</h2>');
			Make_Game_Table($incomplete_games);
			echo('</div></div>');
		}
	?>
	
	<div class="BlueBox">
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
			<?php Make_Game_Table($games); ?>
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
