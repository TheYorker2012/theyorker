<?php 
// Maps (Google and Campus) class by Andrew Oakley (ado500)

class Map {
	private $map = array(
		'element' => '',
		'description' => '',
		'post' => false,
		'zoom' => 13,
		'minlat' => 90,
		'maxlat' => -90,
		'minlng' => 180,
		'maxlng' => -180,
		'locations' => array(),
		'routes' => array(),
		'newlocations' => array()
	);

	private $curroute;

	public function __construct($description, $element) {
		$this->map['description'] = $description;
		$this->map['element'] = $element;
		$this->curroute = &$this->map['locations'];
	}

	// Adds a pin to the map, description is the text in the
	// bubble thing.
	public function AddLocation($description, $lat, $lng) {
		$this->curroute[] = array(
			'description' => $description,
			'lat' => $lat,
			'lng' => $lng
		);
		if ($lat < $this->map['minlat'])
			$this->map['minlat'] = $lat;
		elseif ($lat > $this->map['maxlat'])
			$this->map['maxlat'] = $lat;

		if ($lng < $this->map['minlng'])
			$this->map['minlng'] = $lng;
		elseif ($lng > $this->map['maxlng'])
			$this->map['maxlng'] = $lng;
	}

	// Points added between StartPath() and EndPath() are joined.  
	// NB: this does NOT work at the moment
	public function StartPath() {
		$this->curroute = array();
	}

	// Points added between StartPath() and EndPath() are joined.  
	// NB: this does NOT work at the moment
	public function EndPath() {
		$this->map['routes'][] = $this->curroute;
		$this->curroute = &$this->map['locations'];
	}
	
	// Sets the zoom level (would default to a sensible value or
	// something that fits all pins if there are multiple pins).  
	public function SetZoom($level) {
		$this->map['zoom'] = $level;
	}
	
	// Sets the url to post form data to when we use the map for
	// input.  
	public function SetPostLocation($url) {
		$this->map['post'] = $url;
	}
	
	// Requests that the user places pins on the map.  If the lat
	// and lng are not set, the pin will not initially be placed
	// on the map.  A save button will be displayed which will
	// post the id, lat and lng of placed pins.  
	public function WantLocation($description, $defaultlat = null, $defaultlng = null) {
		$this->map['newlocations'][] = array(
			'description' => $description,
			'lat' => $defaultlat,
			'lng' => $defaultlng
		);
	}

	public function GetData() {
		return $this->map;
	}
}

class Maps {
	private $maps = array();

	public function CreateMap($description, $element) {
		$map = new Map($description, $element);
		$this->maps[] = &$map;
		return $map;
	}

	public function SendMapData() {
		$CI = &get_instance();
		$haseditable = false;
		$mapdata = array();
		foreach ($this->maps as $map) {
			$newdata = $map->GetData();
			$mapdata[] = $newdata;
			if (count($newdata['newlocations'] != 0)) {
				$haseditable = true;
			}
		}
		$CI->main_frame->SetData('maps', $mapdata);
	}
}

?>
