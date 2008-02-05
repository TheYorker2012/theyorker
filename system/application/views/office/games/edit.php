<script type="text/javascript">
	
	function show_game()
	{
		document.getElementById('game_box').style.display = '';
		document.getElementById('show_link').style.display = 'none';
		document.getElementById('hide_link').style.display = '';
	}

	function hide_game()
	{
		document.getElementById('game_box').style.display = 'none';
		document.getElementById('show_link').style.display = '';
		document.getElementById('hide_link').style.display = 'none';
	}
	
	function change_width()
	{
		document.getElementById('game_object').width = document.getElementById('game_width_field').value;
		document.getElementById('game_embed').width = document.getElementById('game_width_field').value;
	}

	function change_height()
	{
		document.getElementById('game_object').height = document.getElementById('game_height_field').value;
		document.getElementById('game_embed').height = document.getElementById('game_height_field').value;
	}
	
	function numbers_only(event)
	{
		event = (event) ? event : window.event;
 		var charCode = (event.which) ? event.which : event.keyCode;	
 		return !(charCode > 31 && (charCode < 48 || charCode > 57));
	}
		
</script>

<div class="RightToolbar">
	<h4><?php echo($section_games_edit_page_info_title); ?></h4>
	<?php echo($section_games_edit_page_info_text); ?>
</div>
<div id="MainColumn">
	<form name='edit_game_form' id='edit_game_form' action='<?php echo $_SERVER["REQUEST_URI"]; ?>' method='post' class='form' >
		<div class="BlueBox">
			&nbsp;
			<fieldset>
				<label for='game_title_field'>Title:</label>
				<input
					type='text'
					name='game_title_field'
					id='game_title_field'
					value='<?php echo($game['title']); ?>'
					size='30' />
				
				<label for='game_activated_field'>Activated:</label>
				<input
					type='checkbox'
					name='game_activated_field'
					id='game_activated_field'
					<?php if($game['activated']){echo('CHECKED');} 
					if(!$is_editor){echo('disabled="disabled"');} ?> />
				<br />
			</fieldset>
		&nbsp;
		</div>
		<div class="BlueBox">
			<div style="float:left; margin: 5px"><?php echo($game['image']);?></div>
			<div style="margin:5px">
				<a href="/office/games/changeimage/<?php echo($game_id); ?>"> Change Image</a>
			</div>
		</div>
		<div class="BlueBox">
			&nbsp;
			<fieldset>
			
				<label for='game_filename_field'>Filename:</label>
				<input
					name='game_filename_field'
					id='game_filename_field'
					value='<?php echo($game['filename']); ?>'
					size='30'
					disabled="disabled" />
				<br />
				
				<label for='game_width_field'>Width:</label>
				<input
					type='text'
					name='game_width_field'
					id='game_width_field'
					value='<?php echo($game['width']); ?>'
					size='3'
					maxlength='3'
					onchange='change_width()'
					onkeypress='return numbers_only(event);' />
				<br />
				
				<label for='game_height_field'>Height:</label>
				<input
					type='text'
					name='game_height_field'
					id='game_height_field'
					value='<?php echo($game['height']); ?>'
					size='3'
					maxlength='3'
					onchange='change_height()'
					onkeypress='return numbers_only(event);' />
				<br />
			</fieldset>
			<div id="show_link">
				<a href="#" onclick="show_game()">View&gt;</a>
			</div>
			<div id="hide_link" style="display:none">
				<a href="#" onclick="hide_game()">&lt;Hide</a>
			</div>
		</div>
			<div style="float:left; margin: 5px"><a href="/office/games">Return to games</a></div>
			<input type='submit' name='submit' id='submit' value='Save' class='button' />
	</form>
</div>

<div class="BlueBox" style="display:none; margin:5px" id="game_box">
	<object id ="game_object"
		width="<?php echo($game['width']); ?>"
		height="<?php echo($game['height']); ?>"
		classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
		codebase="http://fpdownload.macromedia.com/pub/
				shockwave/cabs/flash/swflash.cab#version=8,0,0,0">
			<param name="movie" value="<?php echo($game['pathname']); ?>" />
			<embed 
				id ="game_embed"
				src="<?php echo($game['pathname']); ?>"
				width="<?php echo($game['width']); ?>"
				height="<?php echo($game['height']); ?>"
			  	type="application/x-shockwave-flash"
			  	pluginspage="http://www.macromedia.com/go/getflashplayer" />
	</object>
</div>
