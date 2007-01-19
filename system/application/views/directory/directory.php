<p>The Directory contains many different organisations. Every page has useful information about each organisation such as its contact details, reviews, events and members</p>
<div>
<div id='searchresults' style="padding:0px 0px 0px 0px">
    <ul>
	<?php
	foreach ($organisations as $organisation) { ?>
		<li>
			<?php echo '<a href=\'/directory/' . $organisation['shortname'] . '\'>' . $organisation['name']; ?> (<?php echo $organisation['type']; ?>)</a>
			<?php echo $organisation['description']; ?>
		</li>
	<?php } ?>
	</ul>
</div>
<div align='center'>
	<h5>Showing <?php echo count($organisations); ?> results.</h5>
</div>
<form id='form1' name='form1' action='/directory/' method='POST' class='form'>
	<fieldset>
		<legend>Search</legend>
		<ul>
			<li>
				Venues <input type='checkbox' name='searchrange' id="chkbox1" value="venues" checked>
			</li>
			<li>
				Societies <input type='checkbox' name='searchrange' id="chkbox2" value="socs" checked>
			</li>
			<li>
				Athletics Union <input type='checkbox' name='searchrange' id="chkbox3" value="au" checked>
			</li>
			<li>
				Organisation <input type='checkbox' name='searchrange' id="chkbox4" value="org" checked>
			</li>
			<li>
			College &#038; Campus <input type='checkbox' name='searchrange' id="chkbox5" value="campus" checked>
			</li>
		</ul>
		<input type='text' name='search'>
		<input type='submit' name='Submit' value='Search'>
	</fieldset>
	<fieldset>
	</fieldset>
</form>