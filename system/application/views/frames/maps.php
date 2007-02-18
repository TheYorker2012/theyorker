<?php
if (isset($maps)) {
// The google maps API key will need to be changed whenever we change server
// There is a google account to do this:
//   username - theyorkermaps
//   password - same as the database
?>

	<!-- BEGIN map handling code -->
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA6vFF9HQVRyZ6pmMbEW2o8hT4dMPT2p45abcp05Afs400sGBlHhRGtu7daesOnj_9G28sgfkXgxTfxQ" type="text/javascript"></script>

	<script type="text/javascript">
	//<![CDATA[

	function maps_addLocation(map, lat, lng, description) {
		var point = new GLatLng(lat, lng);
		var marker = new GMarker(point);
		GEvent.addListener(
			marker, 
			"click",
			function() {
				marker.openInfoWindowHtml(description);
			}
		);
		map.addOverlay(marker);
	}

	function maps_editLocationControl(map, description) {
		this.description = description;
		this.button = null;
		this.map = map;

		this.initialize = function(map) {
			var container = document.createElement("div");
			this.button = document.createElement("div");

			this.setButtonStyle(this.button);
			container.appendChild(this.button);
			this.button.appendChild(document.createTextNode("Add " + this.description));

			this.description = this.description + "<br /><br />Drag to move";
			
			var control = this;
			GEvent.addDomListener(
				this.button, 
				"click",
				function() {
					var point = map.getCenter();
					control.createMarker(point);
				}
			);
			
			map.getContainer().appendChild(container);
			return container;
		}

		this.createMarker = function(point) {
			var marker = new GMarker(point, {draggable: true});
			var control = this;
			GEvent.addListener(
				marker, 
				"click",
				function() {
					marker.openInfoWindowHtml(control.description);
				}
			);
			GEvent.addListener(
				marker, 
				"dragstart", 
				function() {
					map.closeInfoWindow();
				}
			);
			GEvent.addListener(
				marker, 
				"dragend", 
				function() {
				}
			);
			map.addOverlay(marker);
			marker.openInfoWindowHtml(control.description);
			this.button.style.display = 'none';
		}

		this.setButtonStyle = function(button) {
			button.style.backgroundColor = "white";
			button.style.borderStyle = "solid";
			button.style.borderColor = "black";
			button.style.borderWidth = "1px";
			button.style.padding = "2px";
			button.style.font.size = "12px";
			button.style.cursor = "pointer";
		}
	}
	maps_editLocationControl.prototype = new GControl();

	/* Hack alert! */
	var maps_editLocationTop = 5;
	maps_editLocationControl.prototype.getDefaultPosition = function() {
		var ret = new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(5, maps_editLocationTop));
		maps_editLocationTop = maps_editLocationTop + 27;
		return ret;
	}


	function maps_onLoad() {
<?php
foreach ($maps as $map) {
?>
		var map = new GMap2(document.getElementById("<?php echo($map['element']);?>"));
		map.addControl(new GSmallMapControl());
<?php	
	if (count($map['locations']) == 0) {
		// Default to somewhere near york
		echo('		map.setCenter(new GLatLng(53.955, -1.08), 15);');
	} elseif (count($map['locations']) == 1) {
		echo('		map.setCenter(new GLatLng(');
		echo($map['locations'][0]['lat']);
		echo(', ');
		echo($map['locations'][0]['lng']);
		echo('), 15);'."\n");
	} else {
		echo('		var bounds = new GLatLngBounds(new GLatLng(');
		echo($map['minlat']);
		echo(', ');
		echo($map['minlng']);
		echo('), new GLatLng(');
		echo($map['maxlat']);
		echo(', ');
		echo($map['maxlng']);
		echo('));'."\n");
		echo('		map.setCenter(bounds.getCenter(), map.getBoundsZoomLevel(bounds));'."\n");
	}

	foreach ($map['locations'] as $location) {
		echo('		'
			.'maps_addLocation(map, '
			.$location['lat'].', '
			.$location['lng'].', "'
			.$location['description'].'");'
			."\n"
		);
	}

	echo('		var ctrl;'."\n");
	foreach($map['newlocations'] as $location) {
		echo('		ctrl = new maps_editLocationControl(map, "');
		echo($location['description']);
		echo('");'."\n");
		if ($location['lat'] != null) {
			echo('		ctrl.setLocation(');
			echo($location['lat']);
			echo(', ');
			echo($location['lng']);
			echo(');'."\n");
		}
		echo('		map.addControl(ctrl);'."\n");
	}
}

?>
	}

	onLoadFunctions.push(maps_onLoad);

	//]]>
	</script>
	<!-- END map handling code -->

<?php
}
?>
