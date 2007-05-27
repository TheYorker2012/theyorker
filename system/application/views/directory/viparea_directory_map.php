<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>

<h4>Jump to Location</h4>
<b>On Campus:</b><br />
<a href='javascript:maps["googlemaps"].setCenter(new GLatLng(53.94704447468437, -1.0529279708862305));'>
<div style="float: left">
<ul>
	<li>Central Hall</li>
</ul>
</div>
<div style="float: right">
<img  src="/images/prototype/directory/central_hall.gif" title="Central Hall" alt="" />
</div>
</a>

<p><b>Off Campus:</b></p>
<p>Enter a place name or postcode:</p>
<fieldset>
<input style="width: 60%" type="text" id="MapSearch"/>
<input style="width: 30%; float: right" type="submit" value="Search" onclick="maps_search(document.getElementById('MapSearch').value, 'googlemaps', document.getElementById('MapSearchResults'));"/>
</fieldset>
<ul id="MapSearchResults">
</ul>
</div>

<form id='orgdetails' name='orgdetails' action='/viparea/directory/<?php echo $organisation['shortname']; ?>/updatemap' method='POST' class='form'>
<div class='blue_box'>
	<h2>location</h2>
		<div id='googlemaps' style='height: 300px'>
		</div>
</div>
<a href='/viparea/'>Back to the vip area.</a>
