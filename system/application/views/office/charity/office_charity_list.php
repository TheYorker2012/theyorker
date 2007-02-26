<script type='text/javascript'>

	function addCharity()
	{
		if (document.getElementById('a_charityname').value == '')
		{
			alert('Please enter a charity name.');
		} 
		else
		{
			document.getElementById('charity_form').className = 'hide';
			document.getElementById('charity_load').className = 'ajax_loading show';
			xajax__addCharity(document.getElementById('a_charityname').value);
		}
	}

	function charityAdded(name, id)
	{
		document.getElementById('a_charityname').value = '';
		document.getElementById('charity_form').className = 'show';
		document.getElementById('charity_load').className = 'ajax_loading hide';
		var container = document.getElementById('charity_list');
		container.innerHTML = container.innerHTML + 'hgfnmghjn';
	}

</script>

<div class="RightToolbar">
	<h4>Write Requests</h4>
	<h4>Accepted Requests</h4>
</div>
<div class="grey_box">
	<h2>charities</h2>
	<div class="info" id="charity_list">
	<?php
	foreach ($charities as $charity)
	{
		echo '<form class="form" action="/office/charity/'.$charity['id'].'" method="post" >
		<fieldset>
			<label for="r_submit_delete">'.$charity['name'].'</label>
			<input type="submit" value="Delete" class="button" name="r_submit_delete" />';
		if ($charity['iscurrent'] == 1)
			echo '<input type="submit" value="Current" class="disabled_button" name="r_submit_makecurrent" disabled />';
		else
			echo '<input type="submit" value="Make Current" class="button" name="r_submit_makecurrent" />';
		echo '</fieldset>
	</form>';
	}
	?>
	</div>
</div>

<div class="blue_box">
	<h2>add charity</h2>
	<div id="charity_load" class="ajax_loading hide">
		<img src="/images/prototype/prefs/loading.gif" alt="Creating" title="Creating" /> Creating new charity...
	</div>
	<form name="asdas2" >
	<fieldset id="charity_form">
		<label for="a_charityname">Name:</label>
		<input type="text" name="a_charityname" id="a_charityname" />
		<input type="submit" value="Add" class="button" name="r_submit_add" onclick="addCharity();" />
	</fieldset>
	</form>
</div>

<?php

echo '<pre>';
echo print_r($data);
echo '</pre>';

?>
