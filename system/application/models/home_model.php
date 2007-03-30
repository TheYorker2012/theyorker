<?php
/*
 * Model for use of dynamic data on homepage
 * 
 *
 * \author Alex Fargus (agf501)
 *
 *
 *
 */
class Home_Model extends Model {
	/*
	 * Constructor, calls default model constructor
	 */
	function Home_Model() {
		parent::Model();
	}
	/*
	 * Function to obtain weather forecast from Yahoo RSS feed.
	 * If forecast is over 4 housr old, it is updated. Latest forecast is always returned.
	 */
	function GetWeather() {
		//Return all forecasts that are less than 4 hours old. (Should be either 1 or 0).
		$sql = 'SELECT weather_cache_timestamp, weather_cache_html
			FROM weather_cache
			WHERE weather_cache_timestamp > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 4 HOUR) ';
		$query = $this->db->query($sql);
		//If 0 rows returned then get up to date weather
		if ($query->num_rows() == 0) {
			//Get the rss feed
			$weather_data = 'http://xml.weather.yahoo.com/forecastrss?p=UKXX0162&u=c';
			$response = file_get_contents($weather_data);
			$weather = new simplexmlelement($response);
			$weather->registerXPathNamespace('data','http://xml.weather.yahoo.com/ns/rss/1.0');
			$weather_forecast = $weather->xpath('//channel/item/data:forecast');
			//Generate the html to be displayed
			$html = '<ul>';
			$html = $html.'<li>'.$weather_forecast[0]->attributes()->day.' -  '.$weather_forecast[0]->attributes()->text.'. High: ';
			$html = $html.$weather_forecast[0]->attributes()->high.', Low: '.$weather_forecast[0]->attributes()->low.'</li>';
			$html = $html.'<li>'.$weather_forecast[1]->attributes()->day.' -  '.$weather_forecast[1]->attributes()->text.'. High: ';
			$html = $html.$weather_forecast[1]->attributes()->high.', Low: '.$weather_forecast[1]->attributes()->low.'</li>';
			$html = $html.'</ul>';
			$html = $html.'<p class="Discreet">Data provided by Yahoo</p>';
			//Delete the old weather forecast
			$sql = 'DELETE FROM weather_cache';
			$query = $this->db->query($sql);
			//Add the new weather forecast
			$sql = 'INSERT INTO weather_cache(weather_cache_html) VALUES (?)';
			$query = $this->db->query($sql,array($html));
			//Return the new html
			return $html;
		} else {
		//Otherwise return cached forecast from table
			$row = $query->row();
			//Return cached html
			return $row->weather_cache_html;
		}
	}
	/*
	 * Function to obtain a random banner image for today.
	 * Returns the image location.
	 */
	function GetBannerImage() {
		$this->load->helper('images');
		$sql = 'SELECT 	image_id
			FROM	images
			WHERE	image_image_type_id = 9
			AND	DATE(image_last_displayed_timestamp) = CURRENT_DATE()';
		$query = $this->db->query($sql);
		if($query->num_rows() == 0){
			$sql = 'SELECT image_id
				FROM images 
				WHERE image_image_type_id = 9
				ORDER BY image_last_displayed_timestamp
				LIMIT 0,1';
			$query = $this->db->query($sql);
			$sql = 'UPDATE images
				SET image_last_displayed_timestamp = CURRENT_TIMESTAMP()
				WHERE image_id = ?';
			$update = $this->db->query($sql,array($query->row()->image_id));
		} 
		$id = $query->row()->image_id;
		return imageLocTag($id,'banner');
	}
}
?>
