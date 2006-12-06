<div id='pageheader' style='background-image: url(/images/subheadericons/pageicon_pagename.gif);'>
	<div id='titleheader'>
		<h1>The Yorker Directory</h1>
	</div>
	<div style='margin-left: 10px;'>
		<form id='form1' name='form1' action='/directory/' method='POST'>
			<strong>Show:</strong> 
			Venues<input type='checkbox' name='searchrange' value="venues" checked>
			Societies<input type='checkbox' name='searchrange' value="socs" checked>
			Athletics Union<input type='checkbox' name='searchrange' value="au" checked>
			Organisation<input type='checkbox' name='searchrange' value="org" checked>
			College &#038; Campus<input type='checkbox' name='searchrange' value="campus" checked>
			<br />
			<input type='text' name='search'>
			<input type='submit' name='Submit' value='Search'>
		</form>
	</div>
</div>
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
	<div align='center'>
		<h5>Showing 50 results, containing Venues, Societies, Athletics Union, Organisation and College &#038; Campus</h5>
	</div>
</div>