<div class="RightToolbar">
	<h4>Make Suggestion</h4>
	<div class="Entry">
		<a href="/wizard/organisation/">Wizard</a>
	</div>
</div>

<div class="blue_box">
	<h2>organisation summary</h2>
	<div class="Entry">
<?php 
	echo('		Name: '.$org['org']['name']."\n");
	echo('		<br />'."\n");
	echo('		Rep: '.$org['user']['firstname'].' '.$org['user']['surname']."\n");
	echo('		<br />'."\n");
	echo('		Organisation Rating: ??/66'."\n");
	echo('		<br />'."\n");
	echo('		PR Info: <a href="/office/pr/info/'.$org['org']['dir_entry_name'].'">Click Here</a>'."\n");
	echo('		<br />'."\n");
?>
	</div>
	<br />
	<div class="Entry">
		To set the new priority for this organisation select an option from the drop down list.
<?php
	echo('		<form class="form" action="/office/pr/modify" method="post">'."\n");
	echo('			<fieldset>'."\n");
	echo('				<input type="hidden" name="r_redirecturl" value="'.$_SERVER['REQUEST_URI'].'" />'."\n");
	echo('				<input type="hidden" name="r_direntryname" value="'.$org['org']['dir_entry_name'].'" />'."\n");
	echo('			</fieldset>'."\n");
	echo('			<fieldset>'."\n");
	echo('				<select name="a_priority">'."\n");
	echo('					<optgroup label="Set New Priority:">'."\n");
	for($i=1;$i<=5;$i++)
	{
		if ($org['org']['priority'] == $i)
			echo('						<option value="'.$i.'" selected="selected">to '.$i.'</option>'."\n");
		else
			echo('						<option value="'.$i.'">to '.$i.'</option>'."\n");
	}
	echo('					</optgroup>'."\n");
	echo('				</select>'."\n");
	echo('			</fieldset>'."\n");
	echo('			<fieldset>'."\n");
	echo('				<input type="submit" value="Set Priority" class="button" name="r_submit_priority" />'."\n");
	echo('			</fieldset>'."\n");
	echo('		</form>'."\n");
?>
	</div>
	<div id="ArticleBox">
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
<?php
	if (count($org['tags']) > 0)
	{
		echo('				<tr class="tr2">'."\n");
		echo('					<td>'."\n");
		echo('						Tags'."\n");
		echo('					</td>'."\n");
		echo('					<td>'."\n");
		echo('						&nbsp;'."\n");
		echo('					</td>'."\n");
		echo('				</tr>'."\n");
		foreach($org['tags'] as $tag)
		{
			echo('				<tr class="tr1">'."\n");
			echo('					<td>'."\n");
			echo('						&nbsp;&nbsp;&nbsp;&nbsp;<a href="/office/reviews/'.$org['org']['dir_entry_name'].'/'.$tag['type_codename'].'/tags/">'.$tag['type_name'].' - '.$tag['group_name'].'</a>'."\n");
			echo('					</td>'."\n");
			echo('					<td>'."\n");
			if ($tag['tag_count'] == 0)
				echo('						<span class="orange">'.$tag['tag_count'].' / 1</span>'."\n");
			else
				echo('						'.$tag['tag_count'].' / 1'."\n");
			echo('					</td>'."\n");
			echo('				</tr>'."\n");
		}
	}
?>
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

echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');

?>
