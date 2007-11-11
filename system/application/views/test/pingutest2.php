<div id="RightColumn">
	<h2 class="first">Make Suggestion</h2>
	<div class="Entry">
		<a href="#">Wizard</a>
	</div>
</div>
<div id="MainColumn">
	<p>index page</p>
	<div class="BlueBox">
		<h2>live campaign list</h2>
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><a href="#">ADO on facebook</a></td>
					<td>voting</td>

				</tr>
				<tr>
					<td><a href="#">Spam for Pam</a></td>
					<td>voting</td>
				</tr>
				<tr>
					<td><a href="#">Swimming Pool</a></td>
					<td>voting</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="BlueBox">
		<h2>future campaign list</h2>
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><a href="#">Dr. Cox for Prime Minister</a></td>
					<td>ready to go live</td>

				</tr>
				<tr>
					<td><a href="#">Random item in the future</a></td>
					<td>incomplete</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="BlueBox">
		<h2>expired campaign list</h2>
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><a href="#">Rugby League for National Sport</a></td>
					<td>petition completed</td>

				</tr>
			</tbody>
		</table>
	</div>
	<p>campaign view</p>
	<div class="BlueBox">
		<h2>campaign edit</h2>
		<form id="campaignedit" action="#" method="post" class="form">
			<fieldset>
				<label for="a_title">Title: </label>
				<input type="text" name="a_title" id="a_title" style="width: 220px;" value="Rugby League for National Sport" />
				<label for="a_facts">Fact Box: (wikitext)</label>
				<textarea name="a_facts" id="a_facts"" cols="25" rows="5">* its a fantastic sport
* high speed and fun to watch
* pingu loves it </textarea>
				<label for="a_web_links">Web Links: (each on a new line)</label>
				<textarea name="a_web_links" id="a_web_links"" cols="25" rows="5">http://www.rleague.com
http://www.sportinglife.com/rugbyleague/</textarea>
			</fieldset>
			<fieldset>
				<input type="submit" name="r_submit_save" value="Save" class="button" />
			</fieldset>
		</form>
	</div>
</div>

<?php

/*
echo '<div class="BlueBox"><pre>';
echo print_r($content_types);
echo '</pre></div>';
*/


?>
