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
<h2>Organisation Information</h2>
Organisation name : <strong>The Yorker</strong><br />
Organisation type : <strong>Society</strong>
<form id='orgdetails' name='orgdetails' action='/admin/yorkerdirectoryview/theyorker/editdetails' method='POST'>
	<table>
		<tr>
			<td>Image :</td><td><input type="file" name="orgdetails_image"></td>
		</tr>
		<tr>
			<td>Website :</td><td><input type='text' name='orgdetails_website'></td>
		</tr>
		<tr>
			<td>Location :</td><td><input type='text' name='orgdetails_location'></td>
		</tr>
		<tr>
			<td>Opening Times :</td><td><input type='text' name='orgdetails_openingtimes'></td>
		</tr>
		<tr>
			<td valign='top'>About :</td><td><textarea name="orgdetails_about" cols="40" rows="10"></textarea></td>
		</tr>
	</table>
	<input type='submit' name='Submit' value='Update'>
</form>
<h2>Members</h2>
The members of your society or organisation you list here will all be shown online but only the members that are givin a title will apear on the main part of the website. Only members with a title will have their full information listed.
<h3>Full member list</h3>
<p>This is a complete list of the members of your society/organisation.</p>
<table width='100%'>
	<tr>
		<td><strong>Name</strong></td>
		<td><strong>Email</strong></td>
		<td><strong>Paid</strong></td>
		<td><strong>Delete?</strong></td>
	</tr>
	<tr>
		<td>Leroy Jenkins</td>
		<td><a href='mailto:lj500@york.ac.uk'>lj500@york.ac.uk</a></td>
		<td><a href='/admin/yorkerdirectoryview/theyorker/paid/123'>N</a></td>
		<td><a href='/admin/yorkerdirectoryview/theyorker/delete/123'>X</a></td>
	</tr>
	<tr>
		<td>Brian Peppers</td>
		<td><a href='mailto:bp500@york.ac.uk'>bp500@york.ac.uk</a></td>
		<td><a href='/admin/yorkerdirectoryview/theyorker/unpaid/456'>Y</a></td>
		<td><a href='/admin/yorkerdirectoryview/theyorker/delete/456'>X</a></td>
	</tr>
</table>
<h3>Add member</h3>
<form name='memberadd' method='post' action='/admin/yorkerdirectoryview/theyorker/addmember'>
	<table>
		<tr>
			<td>Name :</td><td><input type='text' name='memberadd_name'></td>
		</tr>
		<tr>
			<td>Title :</td><td><input type='text' name='memberadd_title'></td>
		</tr>
		<tr>
			<td>Course :</td><td><input type='text' name='memberadd_course'></td>
		</tr>
		<tr>
			<td>Email :</td><td><input type='text' name='memberadd_email'></td>
		</tr>
		<tr>
			<td valign='top'>About :</td><td><textarea name="memberadd_about" cols="40" rows="5"></textarea></td>
		</tr>
		<tr>
			<td valign='top'>Postal Address :</td><td><textarea name="memberadd_address" cols="20" rows="4"></textarea></td>
		</tr>
		<tr>
			<td>Phone Mobile :</td><td><input type='text' name='memberadd_phone_mobile'></td>
		</tr>
		<tr>
			<td>Phone Internal :</td><td><input type='text' name='memberadd_phone_internal'></td>
		</tr>
		<tr>
			<td>Phone External :</td><td><input type='text' name='memberadd_phone_external'></td>
		</tr>
		<tr>
			<td>Paid :</td><td><input type='checkbox' name='memberadd_paid' value='1'></td>
		</tr>
	</table>
	<input name='memberadd_add' type='submit' id='memberadd_add' value='Add'>
</form>
