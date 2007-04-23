<?php
if (isset($maps)) {

switch($_SERVER['SERVER_NAME']) {
	case 'theyorker.gmghosting.com':
		$key = 'ABQIAAAA4LuflJA4VPgM8D-gyba8yBQpSg5-_eQ-9kxEpRcRNaP_SBL1ahQ985h-Do2Gm1Tle5pYiLO7kiWF8Q';
		//break; for some reason theyorker2.gmghosting.com comes up as theyorker.gmghosting.com
	case 'theyorker2.gmghosting.com':
		$key = 'ABQIAAAA4LuflJA4VPgM8D-gyba8yBTRyAb-KmMkdWctvtd_CKS_Gh2u2BQV2EX1b0qY4PM1eJgajR_yMSsENw';
		break;
	case 'ado.is-a-geek.net':
		$key = 'ABQIAAAA6vFF9HQVRyZ6pmMbEW2o8hT4dMPT2p45abcp05Afs400sGBlHhRGtu7daesOnj_9G28sgfkXgxTfxQ';
		break;
	case 'localhost':
		$key = 'ABQIAAAA6vFF9HQVRyZ6pmMbEW2o8hT2yXp_ZAY8_ufC3CFXhHIE1NvwkxS_eaUeRp8y_e74I4oBnTQAPy1jcg';
		break;
	case 'default':
		$key = 'unknown';
}

$editable = false;
foreach ($maps as $map) {
	$editable |= (count($map['newlocations']) > 0);
}

// The google maps API key will need to be changed whenever we change server
// There is a google account to do this:
//   username - theyorkermaps
//   password - same as the database
?>

	<!-- BEGIN map handling code -->
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo($key); ?>" type="text/javascript"></script>

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

<?php
if ($editable) {
?>

	function maps_editableLocation(id, point, description) {
		this.id = id;
		this.button = null;
		this.description = description;
		this.point = point;
		this.container = null;
		this.marker = null;
		this.latctl = null;
		this.lngctl = null;

		this.initialize = function(container) {
			this.container = container;

			this.latctl = document.createElement("input");
			this.latctl.setAttribute("type", "text");
			this.latctl.setAttribute("name", this.id + "_lat");
			this.latctl.style.display = "none";
			this.container.form.appendChild(this.latctl);

			this.lngctl = document.createElement("input");
			this.lngctl.setAttribute("type", "text");
			this.lngctl.setAttribute("name", this.id + "_lng");
			this.lngctl.style.display = "none";
			this.container.form.appendChild(this.lngctl);

			this.button = document.createElement("div");
			maps_setButtonStyle(this.button);
			this.button.appendChild(document.createTextNode("Add " + this.description));

			var ctl = this;
			GEvent.addDomListener(
				this.button,
				"click", 
				function() {
					ctl.point = ctl.container.map.getCenter();
					ctl.addMarker();
				}
			);

			container.control.appendChild(this.button);

			if (this.point != null) {
				this.addMarker();
			}
		}

		this.clickMarker = function() {
			this.marker.openInfoWindowHtml(this.description + "<br /><br />Drag to move");
		}

		this.dragStart = function() {
			this.container.map.closeInfoWindow();
		}

		this.dragEnd = function() {
			this.latctl.setAttribute("value", this.marker.getPoint().lat());
			this.lngctl.setAttribute("value", this.marker.getPoint().lng());
		}

		this.addMarker = function() {
			this.marker = new GMarker(this.point, {draggable: true});
			var ctl = this;
			GEvent.addListener(
				this.marker, 
				"click", 
				function() {
					ctl.clickMarker();
				}
			);
			GEvent.addListener(
				this.marker, 
				"dragstart", 
				function() {
					ctl.dragStart();
				}
			);
			GEvent.addListener(
				this.marker, 
				"dragend", 
				function() {
					ctl.dragEnd();
				}
			);

			this.latctl.setAttribute("value", this.point.lat());
			this.lngctl.setAttribute("value", this.point.lng());

			this.button.style.display = "none";

			this.container.map.addOverlay(this.marker);
			this.clickMarker();
		}
	}

	function maps_setButtonStyle(button) {
		button.style.backgroundColor = "white";
		button.style.borderStyle = "solid";
		button.style.borderColor = "black";
		button.style.borderWidth = "1px";
		button.style.padding = "2px";
		button.style.margin = "2px"
		button.style.textAlign = "center";
		button.style.font.size = "12px";
		button.style.cursor = "pointer";
	}

	function maps_editLocationControl(map, id, post, form) {
		this.control = null;
		this.map = map;
		this.locations = new Array();
		this.formname = form;
		this.form = null;
		this.id = id;
		this.post = post;

		this.initialize = function(map) {
			this.control = document.createElement("div");
			
			if (this.formname === 'null') {
				this.form = document.createElement("form");
				this.control.appendChild(this.form);
				this.form.setAttribute("id", this.id + "_form");
				this.form.setAttribute("name", this.id + "_form");
				this.form.setAttribute("action", this.post);
				this.form.setAttribute("method", "post");
				this.form.style.display = "none";

				var savectl = new maps_saveLocationControl(map, this.form);
				map.addControl(savectl);
			} else {
				this.form = document.getElementById(this.formname);
			}

			map.getContainer().appendChild(this.control);
			return this.control;
		}

		this.addLocation = function(id, description, lat, lng) {
			var point = null;
			if (lat != null) {
				point = new GLatLng(lat, lng);
			}
			var newlocation = new maps_editableLocation(id, point, description);
			newlocation.initialize(this);
			this.locations.push(newlocation);
		}

		this.getDefaultPosition = function() {
			return new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(5, 5));
		}
	}
	maps_editLocationControl.prototype = new GControl();

	function maps_saveLocationControl(map, form) {
		this.form = form;

		this.initialize = function(map) {
			var button = document.createElement("div");
			maps_setButtonStyle(button);
			button.appendChild(document.createTextNode("Save"));
			map.getContainer().appendChild(button);

			var ctl = this;
			GEvent.addDomListener(
				button, 
				"click", 
				function() {
					ctl.form.submit();
				}
			);

			return button;
		}

		this.getDefaultPosition = function() {
			return new GControlPosition(G_ANCHOR_BOTTOM_RIGHT, new GSize(5, 18));
		}
	}
	maps_saveLocationControl.prototype = new GControl();

<?php
}
?>
	var unitiles = {
		'0.0': 'none', '0.1': 'part', '0.2': 'part', '0.3': 'part', '0.4': 'part', '0.5': 'part', '0.6': 'part', '0.7': 'none',
		'1.0': 'none', '1.1': 'part', '1.6': 'part', '1.7': 'part',
		'2.0': 'part', '2.1': 'part', '2.7': 'part',
		'3.0': 'part', '3.7': 'part',
		'4.0': 'part', '4.7': 'part',
		'5.0': 'part', '5.1': 'part', '5.4': 'part', '5.5': 'part', '5.6': 'part', '5.7': 'part',
		'5.0': 'part', '5.1': 'part', '5.4': 'part', '5.5': 'part', '5.6': 'part', '5.7': 'part',
		'6.0': 'none', '6.1': 'part', '6.2': 'part', '6.3': 'part', '6.4': 'part', '6.5': 'none', '6.6': 'none', '6.7': 'none'};

	function maps_getMapTileUrl(tile, zoom) {
		if (zoom === 17 && tile.x >= 65150 && tile.y >= 42114 && tile.x <= 65156 && tile.y <= 42121) {
			var x = tile.x - 65150;
			var y = tile.y - 42114;
			if (unitiles[x + '.' + y] === undefined) {
				return 'http://ado.is-a-geek.net/xmldemo/gmap/data2/large/large-' + x + '.' + y + '.png';
			}
		}
		return G_NORMAL_MAP.getTileLayers()[0].getTileUrl(tile, zoom);
	}

	function maps_getOverlayTileUrl(tile, zoom) {
		if (zoom === 17) {
			if (tile.x >= 65150 && tile.y >= 42114 && tile.x <= 65156 && tile.y <= 42121) {
				var x = tile.x - 65150;
				var y = tile.y - 42114;
				if (unitiles[x + '.' + y] === 'part') {
					return 'http://ado.is-a-geek.net/xmldemo/gmap/data2/large/large-' + x + '.' + y + '.png';
				}
			}
		} else if (zoom === 16) {
			if (tile.x >= 32575 && tile.y >= 21057 && tile.x <= 32578 && tile.y <= 21060) {
				var x = tile.x - 32575;
				var y = tile.y - 21057;
				return 'http://ado.is-a-geek.net/xmldemo/gmap/data2/small/small-' + x + '.' + y + '.png';
			}
		} else if (zoom === 15) {
			if (tile.x >= 16287 && tile.y >= 10528 && tile.x <= 16289 && tile.y <= 10530) {
				var x = tile.x - 16287;
				var y = tile.y - 10528;
				return 'http://ado.is-a-geek.net/xmldemo/gmap/data2/xsmall/xsmall-' + x + '.' + y + '.png';
			}
		}
		return '';
	}

	function maps_onLoad() {
		var map;
		var bounds;
		var locationctl;

		var copyright;
		var copyrightCollection;
		var uniMapTiles;
		var uniOverlayTiles;
		var unimap;
		var unioverlay;

		copyright = new GCopyright(
			1, 
			new GLatLngBounds(
				new GLatLng(53.94, -1.06),
				new GLatLng(53.951, -1.04)
			),
			15,
			'York University');
		copyrightCollection = new GCopyrightCollection('University Maps &copy;');
		copyrightCollection.addCopyright(copyright);

		uniMapTiles = new GTileLayer(copyrightCollection, 0, 17);
		uniMapTiles.getTileUrl = maps_getMapTileUrl;
		uniMapTiles.getCopyright = function(bounds, zoom) {
			var cpy1 = copyrightCollection.getCopyrightNotice(bounds, zoom);
			var cpy2 = G_NORMAL_MAP.getTileLayers()[0].getCopyright(bounds, zoom);
			cpy2 = G_NORMAL_MAP.getTileLayers()[0].getCopyright(bounds, zoom);
			if (cpy1 === null)
				return cpy2;
			if (cpy2 === null)
				return cpy1;
			return cpy1 + '<br />' + cpy2;
		};
		uniMapTiles.isPng = function() { return true; };

		uniOverlayTiles = new GTileLayer(copyrightCollection, 17, 17);
		uniOverlayTiles.getTileUrl = maps_getOverlayTileUrl;
		uniOverlayTiles.isPng = function() { return true; };

		unimap = new GMapType([uniMapTiles], new GMercatorProjection(18), "University Map", {errorMessage:"No Data Available"});
		unioverlay = new GTileLayerOverlay(uniOverlayTiles);

<?php
foreach ($maps as $map) {
?>
		map = new GMap2(document.getElementById("<?php echo($map['element']);?>"));
		map.addMapType(unimap);
		map.addControl(new GSmallMapControl());
		//map.enableScrollWheelZoom();
<?php
	if (count($map['newlocations']) > 0) {
?>
		locationctl = new maps_editLocationControl(map, "<?php echo($map['element']);?>", "<?php echo($map['post']);?>", "<?php echo($map['form']); ?>");
		map.addControl(locationctl);
<?php	
	}

	if (count($map['locations']) == 0) {
		// Default to somewhere near york
		echo('		'
			.'map.setCenter(new GLatLng(53.955, -1.08), 15);'."\n"
		);
	} elseif (count($map['locations']) == 1) {
		echo('		'
			.'map.setCenter(new GLatLng('
				.$map['locations'][0]['lat'].', '
				.$map['locations'][0]['lng']
			.'), '
			.'15);'."\n"
		);
	} else {
		echo('		'
			.'bounds = new GLatLngBounds('
				.'new GLatLng('
					.$map['minlat'].', '
					.$map['minlng']
				.'), new GLatLng('
					.$map['maxlat'].', '
					.$map['maxlng']
				.')'
			.');'."\n"
		);
		echo('		'
			.'map.setCenter(bounds.getCenter(), map.getBoundsZoomLevel(bounds));'."\n"
		);
	}

	echo("\n");

	foreach ($map['locations'] as $location) {
		echo('		'
			.'maps_addLocation(map, '
			.$location['lat'].', '
			.$location['lng'].', "'
			.$location['description'].'");'
			."\n"
		);
	}

	echo("\n");

	foreach($map['newlocations'] as $id => $location) {
		echo('		'
			.'locationctl.addLocation('
				.'"'.$id.'", '
				.'"'.$location['description'].'", '
				.($location['lat'] == null ? 'null' : $location['lat']).', '
				.($location['lng'] == null ? 'null' : $location['lng'])
			.');'."\n"
		);
	}

	echo("\n");

	echo('		map.setMapType(unimap);'."\n");
	echo('		map.addOverlay(unioverlay);'."\n");
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
