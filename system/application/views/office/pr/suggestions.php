<div class="RightToolbar">
	<h4>Make Suggestion</h4>
	<div class="Entry">
		<a href="#">Wizard</a>
	</div>
</div>
<?php
	if ($user['access'] == 'Admin' ||
		$user['access'] == 'High')
	{
?>
<div class="blue_box">
	<h2>Suggestions</h2>
	<div id="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>Suggested By</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				<tr class="tr1">
					<td><input type="checkbox"><a href="#">Toffs V2.0</a></td>
					<td>Jamie Hogan</td>
					<td>25th March 07</td>
				</tr>
				<tr class="tr2">
					<td><input type="checkbox"><a href="#">So Cheesy</a></td>
					<td>Christine Travis</td>
					<td>24th March 07</td>
				</tr>
				<tr class="tr1">
					<td><input type="checkbox"><a href="#">Blue Bicycle</a></td>
					<td>Joe Shelley</td>
					<td>24th March 07</td>
				</tr>
				<tr class="tr2">
					<td><input type="checkbox"><a href="#">Vue Cinema</a></td>
					<td>Nicola Evans</td>
					<td>23rd March 07</td>
				</tr>
				<tr class="tr1">
					<td><input type="checkbox"><a href="#">So Cheesy</a></td>
					<td>Daniella Ashby</td>
					<td>21st March 07</td>
				</tr>
				<tr class="tr1">
					<td colspan="2">
						<select>
						<!--optgroup label="Actions:"-->
							<option selected>Reject</option>
							<option>Accept to Unnassigned</option>
						<!--/optgroup-->
						
						<optgroup label="Accept and Assign:">
							<option name="team_456">to Martina Goodall</option>
							<option name="team_455">to Annette Oakley</option>
							<option name="team_454">to Rachael Ingle</option>
							<option name="team_453">to Matilda Tole</option>
						</optgroup>
						</select>
					</td>
					<td>
						<input type="submit" value="Go">
					</td>
				</tr>
		</table>
	</div>
</div>
<?php
	}
?>
<?php
	if ($user['access'] == 'Low')
	{
?>
<div class="grey_box">
	<h2>Suggestions</h2>
	<div id="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>Suggested By</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				<tr class="tr1">
					<td><a href="#">Toffs V2.0</a></td>
					<td>Jamie Hogan</td>
					<td>25th March 07</td>
				</tr>
				<tr class="tr2">
					<td><a href="#">So Cheesy</a></td>
					<td>Christine Travis</td>
					<td>24th March 07</td>
				</tr>
				<tr class="tr1">
					<td><a href="#">Blue Bicycle</a></td>
					<td>Joe Shelley</td>
					<td>24th March 07</td>
				</tr>
				<tr class="tr2">
					<td><a href="#">Vue Cinema</a></td>
					<td>Nicola Evans</td>
					<td>23rd March 07</td>
				</tr>
				<tr class="tr1">
					<td><a href="#">So Cheesy</a></td>
					<td>Daniella Ashby</td>
					<td>21st March 07</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php
	}
?>


<pre>
<?php

/*
echo '<br />';
echo print_r($content_types);
echo '<br />';
*/


?>
</pre>
