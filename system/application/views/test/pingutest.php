<script type='text/javascript'>

	function xajaxTest()
	{
		if (document.getElementById('new_xajaxTest').value == '') {
			alert('Please enter a comment to submit.');
		} else {
			document.getElementById('xajaxTest_form').className = 'hide';
			document.getElementById('xajaxTest_load').className = 'ajax_loading show';
			xajax__xajaxTest(document.getElementById('new_xajaxTest').value);
		}
	}

	function doxajaxTest()
	{
		document.getElementById('new_xajaxTest').value = '';
		document.getElementById('xajaxTest_form').className = 'show';
		document.getElementById('xajaxTest_load').className = 'ajax_loading hide';
		var container = document.getElementById('xajaxTest_container');
		container.innerHTML = '<div class="feedback"><div class="top"><span class="right">dntyn</span>dyftujd</div><div class="main">fghjfgh</div></div>' + container.innerHTML;
	}

</script>

	<div class="blue_box">
		<h2>xajaxTest...</h2>
		<div id="xajaxTest_load" class="ajax_loading hide">
			<img src="/images/prototype/prefs/loading.gif" alt="Loading" title="Loading" /> Saving new xajaxTest...
		</div>
		<fieldset id="xajaxTest_form" class="form">
			<label for="new_xajaxTest" class="full">Add New xajaxTest</label>
			<textarea name="new_xajaxTest" id="new_xajaxTest" class="full"></textarea>
			<br />
		 	<input type="button" name="add_xajaxTest" id="add_xajaxTest" value="Add xajaxTest" class="button" onclick="xajaxTest();" />
		</fieldset>
		<br />
		<div id="xajaxTest_container">
		</div>
	</div>

<pre>
<?php

/*
echo '<br />';
echo print_r($content_types);
echo '<br />';
*/


?>
</pre>