<script type='text/javascript'>

	function xajaxTest()
	{
		if (document.getElementById('new_xajaxTest').value == '') {
			alert('Please enter a comment to submit.');
		} else {
			document.getElementById('xajaxTest_form').className = 'hide';
			document.getElementById('xajaxTest_load').className = 'ajax_loading show';
			xajax__xajaxTest(document.getElementById('new_xajaxTest').value);
		}
	}

	function doxajaxTest()
	{
		document.getElementById('new_xajaxTest').value = '';
		document.getElementById('xajaxTest_form').className = 'show';
		document.getElementById('xajaxTest_load').className = 'ajax_loading hide';
		var container = document.getElementById('xajaxTest_container');
		container.innerHTML = '<div class="feedback"><div class="top"><span class="right">dntyn</span>dyftujd</div><div class="main">fghjfgh</div></div>' + container.innerHTML;
	}

</script>
<div class="RightToolbar">
	<h4>Totals</h4>
	<div class="Entry">
		There are currently
		<ul>
			<li>79 reviews required</li>
			<li>2 Reps under 30%</li>
			<li>32 outdated discounts</li>
		</ul>
	</div>
</div>
<div class="grey_box">
	<h2>PR Officer</h2>
	To maintain the PR Reps, you can view the list below. The list is sorted with the most incompetent authors at the top. Click on their average to quickly view other information about them. Or click on their name to view more detailed information.
</div>
<div class="blue_box">
	<h2>PR Reps</h2>
	<div id="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>PR Rep Name</th>
					<th>Last Logged In</th>
					<th>Average</th>
				</tr>
			</thead>
			<tbody>
				<tr class="tr1">
					<td><a href="#">SteveDave Awesomengine</a></td>
					<td>4th April 2007</td>
					<td><a href="#" onclick="var element = document.getElementById('stevedave'); element.style.display == 'none' ? element.style.display = 'table-row' : element.style.display = 'none';">14%</a></td>
				</tr>
				<tr id="stevedave" style="display: none">
					<td colspan=0>
							<h5>Statistics</h5>
							<table>
								<tbody>
									<tr>
										<td>Events: 20%</td>
										<td>Deals: 35%</td>
									</tr>
									<tr>
										<td>Reviews: 2%</td>
										<td>Directory: 14%</td>
									</tr>
								</tbody>
							</table>
							<br />
							<span class="grey">SteveDave Awesomeengine is responsible for:</span>
							<table>
								<tbody>
									<tr>
										<td>Evil Eye: 10%</td>
										<td>The Blue Bike: 10%</td>
									</tr>
									<tr>
										<td>Floods: 10%</td>
										<td>Barry Whites Death: 10%</td>
									</tr>
									<tr>
										<td>American Cars: 10%</td>
										<td>America: 10%</td>
									</tr>
									<tr>
										<td>World War 1: 10%</td>
										<td>World War 2: 10%</td>
									</tr>
									<tr>
										<td>Idiots and Greenday: 10%</td>
										<td></td>
									</tr>
								</tbody>
							</table>
							<br />
					</td>
				</tr>
				<tr class="tr2">
					<td><a href="#">Tuesday McGee</a></td>
					<td>78th June 2007</td>
					<td><a href="#">23%</a></td>
				</tr>
				<tr class="tr1">
					<td><a href="#">Funf und Funfzig</a></td>
					<td>9st March 2002</td>
					<td><a href="#">52%</a></td>
				</tr>
				<tr class="tr2">
					<td><a href="#">Stevedave Mqueen</a></td>
					<td>30th Febuary 2008</td>
					<td><a href="#">72%</a></td>
				</tr>
				<tr class="tr1">
					<td><a href="#">Your Mother</a></td>
					<td>29th December 2007</td>
					<td><a href="#">98%</a></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="grey_box">
	<h2>PR Rep Organisations</h2>
	<div id="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>Organisation Name</th>
					<th>Average</th>
				</tr>
			</thead>
			<tbody>
				<tr class="tr1">
					<td><a href="#">Toffs</a></td>
					<td><a href="#">14%</a></td>
				</tr>
				<tr id="stevedave">
					<td colspan=0>
							<h5>Statistics</h5>
							<table>
								<tbody>
									<tr>
										<td>Events: 35%</td>
										<td>Deals: 2%</td>
									</tr>
									<tr>
										<td>Reviews: 2%</td>
										<td>Directory: 14%</td>
									</tr>
									<tr>
										<td>Priority: 4</td>
										<td></td>
									</tr>
								</tbody>
							</table>
							<br />
							<span class="grey">Toffs has 2 review types:</span>
							<table>
								<tbody>
									<tr>
										<td>Food: 10%</td>
										<td>Drink: 10%</td>
									</tr>
								</tbody>
							</table>
							<br />
					</td>
				</tr>
				<tr class="tr2">
					<td><a href="#">Evil Eye</a></td>
					<td><a href="#">14%</a></td>
				</tr>
				<tr class="tr1">
					<td><a href="#">Bleh</a></td>
					<td><a href="#">14%</a></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="blue_box">
	<h2>Organisation Reviews - Evil Eye</h2>
	<div id="ArticleBox">
		<table>
			<thead>
				<tr>
					<th>Review Type</th>
					<th>Average</th>
				</tr>
			</thead>
			<tbody>
				<tr class="tr1">
					<td><a href="#">Food</a></td>
					<td><a href="#">14%</a></td>
				</tr>
				<tr id="stevedave">
					<td colspan=0>
							<h5>Statistics</h5>
							<table>
								<tbody>
									<tr>
										<td>Updated: 35%</td>
										<td>Deals: 2%</td>
									</tr>
									<tr>
										<td>Photos: 2%</td>
										<td>Business Cards: 14%</td>
									</tr>
								</tbody>
							</table>
							<br />
							<!--
							<span class="grey">Toffs has 2 review types:</span>
							<table>
								<tbody>
									<tr>
										<td>Food: 10%</td>
										<td>Drink: 10%</td>
									</tr>
								</tbody>
							</table>
							<br />
							-->
					</td>
				</tr>
				<tr class="tr2">
					<td><a href="#">Drink</a></td>
					<td><a href="#">14%</a></td>
				</tr>
			</tbody>
		</table>
	</div>
<div>

<!--
	<div class="blue_box">
		<h2>xajaxTest...</h2>
		<div id="xajaxTest_load" class="ajax_loading hide">
			<img src="/images/prototype/prefs/loading.gif" alt="Loading" title="Loading" /> Saving new xajaxTest...
		</div>
		<fieldset id="xajaxTest_form" class="form">
			<label for="new_xajaxTest" class="full">Add New xajaxTest</label>
			<textarea name="new_xajaxTest" id="new_xajaxTest" class="full"></textarea>
			<br />
		 	<input type="button" name="add_xajaxTest" id="add_xajaxTest" value="Add xajaxTest" class="button" onclick="xajaxTest();" />
		</fieldset>
		<br />
		<div id="xajaxTest_container">
		</div>
	</div>
-->

<pre>
<?php

/*
echo '<br />';
echo print_r($content_types);
echo '<br />';
*/


?>
</pre>
