<div id='pageheader' style='background-image: url(/images/subheadericons/pageicon_pagename.gif);'>
	<div id='titleheader'>
		<h1>Edit: The Yorker Directory</h1>
	</div>
	<div style='margin-left: 10px;'>
		<form id='form1' name='form1' action='/admin/yorkerdirectory/' method='POST'>
			<strong>Show:</strong> 
			Venues<input type='checkbox' name='searchrange' value="venues" checked>
			Societies<input type='checkbox' name='searchrange' value="socs" checked>
			Athletics Union<input type='checkbox' name='searchrange' value="au" checked>
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
				<a href='/admin/yorkerdirectoryview/fragsoc'>FragSoc</a>
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
				<a href='/admin/yorkerdirectoryview/theyorker'>TheYorker</a>
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
				<a href='/admin/yorkerdirectoryview/toffs'>Toffs</a>
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
				<a href='/admin/yorkerdirectoryview/poledancing'>Pole Dancing</a>
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
		<h5>Showing 50 results, containing Organisations & Societies</h5>
	</div>
</div>
<div>
	<h2>Add an organisation</h2>
		<p>This will add an organisation to the directory and email the login password to the address provided.</p>
		<form id='addorgform' name='addorgform' action='/admin/directory/' method='POST'>
			Name :<input type='text' name='name'><br />
			Email :<input type='text' name='email'><br />
			Organisation type :
			<select name="orgtype">
				<option value="soc" selected>Society</option>
				<option value="venue">Venue</option>
				<option value="venue">Athletics Union</option>
			</select><br />
			<input type='submit' name='Add' value='Add'>
		</form>
</div>