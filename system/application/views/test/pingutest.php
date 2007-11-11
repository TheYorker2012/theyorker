<div id="RightColumn">
	<h2 class="first">Make Suggestion</h2>
	<div class="Entry">
		<a href="#">Wizard</a>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>current deals</h2>
		<h3>Triples for Singles</h3>
		<div class="Date">25th March 2007 - 1st April 2007</div>
		<p>An amazing offer on guinea pigs, 3 for 1!!! You've left the office door open! Spies are everywhere: if you're going to stay out for a while, please shut it and lock it.</p>
		<p><a href="#">[edit]</a> <a href="#">[expire]</a> <a href="#">[delete]</a></p>
		<h3>Half Price Hay</h3>
		<div class="Date">23th March 2007 - 30th March 2007</div>
		<p>Big bags of hay half price <strike>&pound;2.00</strike> now &pound;1.00.</p>
		<p><a href="#">[edit]</a> <a href="#">[expire]</a> <a href="#">[delete]</a></p>
	</div>
	<div class="BlueBox">
		<h2>future deals</h2>
		<h3>Half Price Guinea Pig Food</h3>
		<div class="Date">5th April 2007 - 15th April 2007</div>
		<p>Big bags of fooood half price <strike>&pound;2.00</strike> now &pound;1.00.</p>
		<p><a href="#">[edit]</a> <a href="#">[make current]</a> <a href="#">[delete]</a></p>
	</div>
	
	<div class="BlueBox">
		<h2>expired deals</h2>
		<h3>Triples for Singles</h3>
		<div class="Date">12th March 2007 - 20th March 2007</div>
		<p>An amazing offer on guinea pigs, 3 for 1!!!</p>
		<p><a href="#">[edit]</a> <a href="#">[delete]</a></p>
		<h3>Half Price Hay</h3>
		<div class="Date">5th March 2007 - 15th March 2007</div>
		<p>Big bags of hay half price <strike>&pound;2.00</strike> now &pound;1.00</p>
		<p><a href="#">[edit]</a> <a href="#">[delete]</a></p>
	</div>
	
	<div class="BlueBox">
		<h2>edit</h2>
		<form id="dealupdate" action="#" method="post" class="form">
			<fieldset>
				<label for="a_title">Title: </label>
				<input type="text" name="a_title" id="a_title" style="width: 220px;" value="Triples for Singles" />
				<label for="a_date_start">Date - Start: </label>
				<input type="text" name="a_date_start" id="a_date_start" style="width: 220px;" value="12/03/2007" />
				<label for="a_date_end">Date - End: </label>
				<input type="text" name="a_date_end" id="a_date_end" style="width: 220px;" value="20/03/2007" />
				<label for="a_description">Description: </label>
				<textarea name="a_description" id="a_description" cols="25" rows="5">An amazing offer on guinea pigs, 3 for 1!!!</textarea>
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
