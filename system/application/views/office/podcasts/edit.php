<script type="text/javascript">
	function Change_File_Show()
	{
		if (document.getElementById('change_file_box').style.display!="")
		{
			xajax_list_files();
		}
		document.getElementById('change_file_box').style.display="";

	}
	
	function list_response()
	{
		for (var i=0;i<arguments.length; i++)
		{
			document.change_file_form.new_file.options[
				document.change_file_form.new_file.options.length] =
					new Option(arguments[i],arguments[i]);
		}
		document.getElementById('change_file_wait').style.display="none";
		document.getElementById('change_file_form_box').style.display="";
	}
	
	function hide_change_file()
	{		
		document.getElementById('change_file_box').style.display="none";
	}
</script>

<div id="RightColumn">
	<h2 class="first">Quick Links</h2>
	<div class="Entry">
		
	</div>
</div>

<div id="MainColumn">
	<div id="change_file_box" class="BlueBox" style="Display:none">
		<div id="change_file_wait">
			Please Wait...
		</div>
		<div id="change_file_form_box" style="Display:none">
			Select the podcasts file from the list below.
			<form 
				name="change_file_form"
				id="change_file_form"
				action="/office/podcasts/edit/<?php echo($podcast['id']); ?>"
				method="post"
				class="form">
				<fieldset>
					<label for="new_file">File:</label>
					<select name="new_file" id="new_file">
					</select>
					<input type='button' value='Change' class='button' onclick='xajax_change_file(<?php echo($podcast['id']); ?>,document.getElementById("new_file").value);hide_change_file();return false;'/>
				</fieldset>
			</form>
		</div>
		<a href="#" onclick="hide_change_file();return false;">Hide</a>
	</div>


	<div id="randomname" class="BlueBox">
		<h2>podcast details</h2>
		<form name="podcast_form" class="form" action="<?php echo($target); ?>" method="post" enctype="multipart/form-data">
			<fieldset>
				<label for="a_name">Name: </label>
					<input
						type="text"
						name="a_name"
						size="31"
						onchange="if(podcast_form.a_name.value==null||podcast_form.a_name.value==''){podcast_form.is_live.checked=''}"

						value="<?php echo(xml_escape($podcast['name'])); ?>" />
				<label for="a_description">Description: </label>
					<textarea
						name="a_description"
						rows="7"
						cols="33"
						onchange="if(podcast_form.a_description.value==null||podcast_form.a_description.value==''){podcast_form.is_live.checked=''}">
							<?php echo(xml_escape($podcast['description'])); ?>
					</textarea>
				<label for="file_address">File Address:</label>
					<input	
						type="text"
						name="file_address"
						id="file_address"
						size="31"
						readonly="readonly"
						value="<?php echo(
									$this->config->item('static_web_address').
									'/media/podcasts/'.
									xml_escape($podcast['file'])); ?>">
					<input type="button" class="button" value="Change" name="podcast_change_file_button" onclick="Change_File_Show();return false;">
				<label for="file_size">File Size:</label>
					<input	
						type="text"
						name="file_size"
						id="file_size"
						size="10"
						readonly="readonly"
						value="<?php echo(round($podcast['file_size']/1048576,1).' MBytes'); ?>">
				<label for="is_live">Is Live</label>
					<input
						type="checkbox"
						name="is_live"
						onclick="return !(podcast_form.a_name.value==null||podcast_form.a_name.value==''||podcast_form.a_description.value==null||podcast_form.a_description.value=='');"
						<?php if($podcast['is_live']==1){echo('checked="checked"');} ?>
						<?php if($podcast['live_can_change']){echo('disabled="disabled"');} ?>>
						
				<br />
				<div style="float:left; margin: 5px;width:150px">
					<a href="/office/podcasts">Return to Podcasts</a>
				</div>
				<div style="float:right; margin: 5px;width:120px">
					<div style="float:left; margin: 5px">
						<a href="<?php echo(
								$this->config->item('static_web_address').
								'/media/podcasts/'.
								xml_escape($podcast['file'])); ?>">Play</a>
					</div>
					<input type="submit" class="button" value="Save" name="r_submit_save" />
				</div>
			</fieldset>
		</form>
	</div>
</div>
