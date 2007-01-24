<div id='pageheader' style='background-image: url(/images/subheadericons/pageicon_pagename.gif);'>
	<div id='titleheader'>
		<h1>The Yorker Directory</h1>
	</div>
	<div style='margin-left: 10px;'>
		<form id='form1' name='form1' action='/directory/' method='POST'>
			<strong>Show:</strong> 
			Venues<input type='checkbox' name='searchrange' value='venues' checked>
			Societies<input type='checkbox' name='searchrange' value='socs' checked>
			Athletics Union<input type='checkbox' name='searchrange' value='au' checked>
			Organisation<input type='checkbox' name='searchrange' value='org' checked>
			College &#038; Campus<input type='checkbox' name='searchrange' value='campus' checked>
			<br />
			<input type='text' name='search'>
			<input type='submit' name='Submit' value='Search'>
		</form>
	</div>
</div>
<div>
<div style='width: 90%;margin-left: auto;margin-right: auto;'>
	<table width='100%'>
		<tr>
			<td>
				<a href='/admin/directory/view/fragsoc'>FragSoc</a>
			</td>
			<td>
				Society
			</td>
			<td>
				A computer gaming society
			</td>
		</tr>
		<tr>
			<td>
				<a href='/admin/directory/view/theyorker'>TheYorker</a>
			</td>
			<td>
				Organisation
			</td>
			<td>
				The people who run this website
			</td>
		</tr>
		<tr>
			<td>
				<a href='/admin/directory/view/toffs'>Toffs</a>
			</td>
			<td>
				Venue
			</td>
			<td>
				A nightclub in york
			</td>
		</tr>
		<tr>
			<td>
				<a href='/admin/directory/view/poledancing'>Pole Dancing</a>
			</td>
			<td>
				Athletics Union
			</td>
			<td>
				A fitness club
			</td>
		</tr>
	</table>
	<div align='center'>
		<h5>50 results showing: Athletics Union, College &#038; Campus, Organisation, Societies and Venue</h5>
	</div>
</div>
<form id='addorgform' name='addorgform' action='/admin/directory/' method='POST' class='form'>
	<fieldset>
		<legend>Add an organisation</legend>
		<label for='addorgform_name'>Name:</label>
		<input name='addorgform_name' type='text' name='name' />
		<br />
		<label for='addorgform_email'>Email:</label>
		<input name='addorgform_email' type='text' name='email' />
		<br />
		<label for='addorgform_orgtype'>Organisation type:</label>
		<select name='addorgform_orgtype'>
			<option value='au' selected>Athletics Union</option>
			<option value='college'>College &#038; Campus</option>
			<option value='orgs'>Organisation</option>
			<option value='socs'>Societies</option>
			<option value='venue'>Venue</option>
		</select>
	</fieldset>
	<fieldset>
		<label for='addorgform_addbutton'></label>
		<input type='submit' name='addorgform_addbutton' value='Add' class='button' />
	</fieldset>
</form>