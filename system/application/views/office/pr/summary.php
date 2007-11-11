<div class="RightToolbar">
	<h4>Make Suggestion</h4>
	<div class="Entry">
		<a href="#">Wizard</a>
	</div>
</div>
<?php
	if (($user['access'] == 'Admin' || 
		$user['access'] == 'High') &&
		$parameters['type'] == 'def')
	{
?>
<div class="blue_box">
	<h2>Summary</h2>
	<div id="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>PR Rep</th>
					<th>Rating</th>
				</tr>
			</thead>
			<tbody>
				<tr class="tr1">
					<td><a href="/office/pr/summary/rep/1">Francesca Burton</a></td>
					<td>87%</td>
				</tr>
				<tr class="tr2">
					<td><a href="/office/pr/summary/rep/1">Nancy Brehon</a></td>
					<td>75%</td>
				</tr>
				<tr class="tr1">
					<td><a href="/office/pr/summary/rep/1">Jamie Hogan</a></td>
					<td>69%</td>
				</tr>
				<tr class="tr2">
					<td><a href="/office/pr/summary/rep/1">Christine Travis</a></td>
					<td>56%</td>
				</tr>
				<tr class="tr1">
					<td><a href="/office/pr/summary/rep/1">Joe Shelley</a></td>
					<td>52%</td>
				</tr>
				<tr class="tr2">
					<td><a href="/office/pr/summary/rep/1">Nicola Evans</a></td>
					<td>47%</td>
				</tr>
				<tr class="tr1">
					<td><a href="/office/pr/summary/rep/1">Daniella Ashby</a></td>
					<td>30%</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php
	}
?>
<?php
	if ($parameters['type'] == 'rep')
	{
?>
<div class="grey_box">
	<h2>Rep Summary</h2>
	<div id="ArticleBox">
		Rep Business Card Here (with edit if Rep).<br />
		Rep Rating: 75%<br />
		<table>
			<thead>
				<tr>
					<th>Organisation</th>
					<th>Priority</th>
					<th>Rating</th>
				</tr>
			</thead>
			<tbody>
				<tr class="tr1">
					<td><a href="/office/pr/summary/org/1">Toffs</a></td>
					<td>1</td>
					<td>71%</td>
				</tr>
				<tr class="tr2">
					<td><a href="/office/pr/summary/org/1">Evil Eye</a></td>
					<td>1</td>
					<td>35%</td>
				</tr>
				<tr class="tr1">
					<td><a href="/office/pr/summary/org/1">Punch Bowl</a></td>
					<td>2</td>
					<td>67%</td>
				</tr>
				<tr class="tr2">
					<td><a href="/office/pr/summary/org/1">Tesco (Clifton Moor)</a></td>
					<td>3</td>
					<td>95%</td>
				</tr>
				<tr class="tr1">
					<td><a href="/office/pr/summary/org/1">Fantasy World</a></td>
					<td>5</td>
					<td>60%</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php
	}
?>
<?php
	if ($parameters['type'] == 'org')
	{
?>
<div class="blue_box">
	<h2>Toffs Summary</h2>
	<div id="ArticleBox">
		Rep: Nicola Evans<br />
		Organisation Rating: 56/66<br />
		<input type="text" value="1"><input type="submit" value="Set Priority"><br /><br />
		<table>
			<thead>
				<tr>
					<th>Info</th>
					<th>Rating<br />(Current/Expected)</th>
				</tr>
			</thead>
			<tbody>
				<tr class="tr2">
					<td>Calendar</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#">Events</a></td>
					<td><span class="orange">1 / 4</span></td>
				</tr>
				<tr class="tr2">
					<td>Deals</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#">Food</a></td>
					<td>2 / 2</td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#">Drink</a></td>
					<td>3 / 2</td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#">Culture</a></td>
					<td>1 / 1</td>
				</tr>
				<tr class="tr2">
					<td>Information</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/office/pr/org/africancaribbean_society/directory/information">Directory</a></td>
					<td><span class="orange">6 / 8</span></td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/office/reviews/africancaribbean_society/food">Food</a></td>
					<td>8 / 9</td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/office/reviews/africancaribbean_society/drink">Drink</a></td>
					<td><span class="orange">7 / 8</span></td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/office/reviews/africancaribbean_society/culture">Culture</a></td>
					<td>8 / 8</td>
				</tr>
				<tr class="tr2">
					<td>Photos</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/office/pr/org/africancaribbean_society/directory/photos">Directory</a></td>
					<td><span class="orange">3 / 3</span></td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/office/reviews/africancaribbean_society/food/photos">Food</a></td>
					<td>4 / 3</td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/office/reviews/africancaribbean_society/drink/photos">Drink</a></td>
					<td><span class="orange">2 / 3</span></td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/office/reviews/africancaribbean_society/culture/photos">Culture</a></td>
					<td>3 / 3</td>
				</tr>
				<tr class="tr2">
					<td>Tags</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/office/reviews/africancaribbean_society/food/tags">Food</a></td>
					<td>3 / 3</td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/office/reviews/africancaribbean_society/drink/tags">Drink</a></td>
					<td><span class="orange">1 / 3</span></td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/office/reviews/africancaribbean_society/culture/tags">Culture</a></td>
					<td>3 / 3</td>
				</tr>
				<tr class="tr2">
					<td>Other</td>
					<td>&nbsp;</td>
				</tr>
				<tr class="tr1">
					<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#">Business Cards</a></td>
					<td>9 / 6</td>
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
echo print_r($data);
*/

?>
</pre>
