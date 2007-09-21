<script type="text/javascript">
	function addNameToSend() {
		var table_element;
		var list_element;
		var list_text;
		var checkbox_element;
		
		list_element = document.getElementById('a_add_member');
		table_element = document.getElementById('t_' + list_element.value);
		checkbox_element = document.getElementById('cb_' + list_element.value);
		
		table_element.style.display = '';
		checkbox_element.checked = true;
	}
	
	function removeNameFromSend(user_id) {
		var table_element;
		var checkbox_element;
		
		table_element = document.getElementById('t_' + user_id);
		checkbox_element = document.getElementById('cb_' + user_id);
		
		table_element.style.display = 'none';
		checkbox_element.checked = false;
	}
</script>

<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<div class="Entry">
		<?php echo $main_text; ?>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>Compose Email</h2>
<?php
	echo('		<form id="member_select_form" action="'.$target.'" method="post" class="form">'."\n");
?>
			<fieldset>
				<label for="a_send_to">Recipients:</label>
				<table id="a_send_to" style="padding: 5px;">
					<tbody id="RecipientContainer">
<?php
	foreach ($members as $member) {
		if (in_array($member['id'], $to_members)) {
			echo('					<tr id="t_'.$member['id'].'" style="display: ;">'."\n");
		}
		else {
			echo('					<tr id="t_'.$member['id'].'" style="display: none;">'."\n");
		}
		echo('						<td>'."\n");
		if (in_array($member['id'], $to_members)) {
			echo('							<input type="checkbox" name="cb['.$member['id'].']" id="cb_'.$member['id'].'" style="display: none;" checked="checked" />'."\n");
		}
		else {
			echo('							<input type="checkbox" name="cb['.$member['id'].']" id="cb_'.$member['id'].'" style="display: none;" />'."\n");
		}
		echo('							'.$member['name'].' ('.$member['email'].') <small><a href="#" onclick="removeNameFromSend('.$member['id'].');">(remove)</a></small>'."\n");
		echo('						</td>'."\n");
		echo('					</tr>'."\n");
	}
?>
					</tbody>
				</table>
				<label for="a_add_member">Add Recipient:</label>
				<select id="a_add_member" style="width: 200px;">
<?php
	foreach ($members as $member) {
		echo('					<option value="'.$member['id'].'">'.$member['name'].' ('.$member['email'].')</option>'."\n");
	}
?>
				</select>
				<input type="button" id="r_submit_add_member" value="Add" onclick="addNameToSend()" />
				<label for="a_subject">Subject:</label>
<?php
	echo('				<input type="text" name="a_subject" id="a_subject" style="width: 250px;" value="'.$subject.'" />'."\n");
?>
				<label for="a_content">Message:</label>
<?php
	echo('				<textarea name="a_content" id="a_content" rows="10" cols="28">'.$content.'</textarea>'."\n");
?>
			</fieldset>
			<fieldset>
				<input type="submit" name="r_submit_send_email" id="r_submit_send_email" value="Send Email To Members" />
			</fieldset>
		</form>
	</div>
</div>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>