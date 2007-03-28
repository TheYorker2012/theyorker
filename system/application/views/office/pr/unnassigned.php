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
<div class="grey_box">
	<h2>Unnassigned</h2>
	<div id="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>PR Rep(s)</th>
					<th>Assign To</th>
				</tr>
			</thead>
			<tbody>
				<tr class="tr1">
					<td><input type="checkbox"><a href="#">Tesco (Heslington)</a></td>
					<td>Lance Taylor</td>
					<td><select><option>Martina Goodall</option><option>Alexandra Fargus</option></select></td>
				</tr>
				<tr class="tr2">
					<td><input type="checkbox"><a href="#">Bing Bong Bowling</a></td>
					<td>Susan Parker<br />Rachael Rout</td>
					<td><select><option>Martina Goodall</option><option>Alexandra Fargus</option></select></td>
				</tr>
				<tr class="tr1">
					<td colspan="3">
						<input type="submit" value="Assign">
					</td>
				</tr>
			</tbody>
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
<div class="blue_box">
	<h2>Unnassigned</h2>
	<div id="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>PR Rep(s)</th>
					<th>Options</th>
				</tr>
			</thead>
			<tbody>
				<tr class="tr1">
					<td><a href="#">Tesco (Heslington)</a></td>
					<td>Lance Taylor</td>
					<td><input type="submit" value="Request"></td>
				</tr>
				<tr class="tr2">
					<td><a href="#">Bing Bong Bowling</a></td>
					<td>Susan Parker<br />Rachael Rout</td>
					<td><input type="submit" value="Withdraw"></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php
	}
?>
<div class="grey_box">
	<h2>Pending</h2>
	<div id="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>Name</th>
					<th>PR Rep</th>
					<th>Options</th>
				</tr>
			</thead>
			<tbody>
				<tr class="tr1">
					<td><a href="#">Ice Factory</a></td>
					<td>Alexandra Fargus</td>
					<td><input type="submit" value="Accept"><input type="submit" value="Reject"></td>
				</tr>
				<tr class="tr2">
					<td><a href="#">Sports Bar</a></td>
					<td>Knifeman Spurden</td>
					<td><input type="submit" value="Accept"><input type="submit" value="Reject"></td>
				</tr>
			</tbody>
		</table>
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
