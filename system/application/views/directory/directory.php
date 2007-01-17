<p>The Directory contains many different organisations. Every page has useful information about each organisation such as its contact details, reviews, events and members</p>
<div>
<div style='width: 90%;margin-left: auto;margin-right: auto;'>
	<table width='100%'>
		<?php foreach ($organisations as $organisation) { ?>
		<tr>
			<td>
				<?php echo '<a href=\'/directory/' . $organisation['shortname'] . '\'>' . $organisation['name']; ?></a>
			</td>
			<td>
				<?php echo $organisation['type']; ?>
			</td>
			<td>
				<?php echo $organisation['description']; ?>
			</td>
		</tr>
		<?php } ?>
	</table>
</div>
<div align='center'>
	<h5>Showing 50 results, containing Venues, Societies, Athletics Union, Organisation and College &#038; Campus</h5>
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