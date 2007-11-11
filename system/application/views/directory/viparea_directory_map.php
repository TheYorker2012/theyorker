<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<div class="Entry">
		<?php echo($main_text); ?>
	</div>

	<h2>Jump to Location</h2>
	<div class="Entry">
		<h3>On Campus:</h3>
		<img src="/images/prototype/directory/central_hall.gif" title="Central Hall" alt="" class="Right" />
		<ul><li>
			<a onclick="javascript:maps['googlemaps'].setCenter(new GLatLng(53.94704447468437, -1.0529279708862305));">
			Central Hall
			</a>
		</li></ul>

		<h3>Off Campus:</h3>
		<p>Enter a place name or postcode:</p>
		<fieldset>
			<input type="text" id="MapSearch"/>
			<input type="submit" value="Search" onclick="maps_search(document.getElementById('MapSearch').value, 'googlemaps', document.getElementById('MapSearchResults'));" class="button" />
		</fieldset>
		<ul id="MapSearchResults">
		</ul>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
	<h2>location</h2>
		<div id='googlemaps' style='height: 300px'>
		</div>
	</div>
</div>
