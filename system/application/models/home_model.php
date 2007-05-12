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
		if ($query->num_rows() == 0 || True) { //TODO: Remove true to enable caching
			//Get the rss feed
			$weather_data = 'http://xml.weather.yahoo.com/forecastrss?p=UKXX0162&u=c';
			$response = file_get_contents($weather_data);
			$weather = new simplexmlelement($response);
			$weather->registerXPathNamespace('data','http://xml.weather.yahoo.com/ns/rss/1.0');
			$weather_forecast = $weather->xpath('//channel/item/data:forecast');
			//Generate the html to be displayed
			$html = '<table border="0" width="90%">';
			$html .= '	<tr><td align="center">';
			$html .= '		<div class="Date">'.date('l jS',strtotime($weather_forecast[0]->attributes()->date)).'</div>';
			$html .= '	</td>';
			$html .= '	<td align="center">';
			$html .= '		<div class="Date">'.date('l jS',strtotime($weather_forecast[1]->attributes()->date)).'</div>';
			$html .= '	</td></tr>';
			$html .= '	<tr><td align="center">';
			$html .= '		<img src="http://us.i1.yimg.com/us.yimg.com/i/us/we/52/'.$weather_forecast[0]->attributes()->code.'.gif" title="'.$weather_forecast[0]->attributes()->text.'" alt="'.$weather_forecast[0]->attributes()->text.'" />';
			$html .= '	</td>';
			$html .= '	<td align="center">';
			$html .= '		<img src="http://us.i1.yimg.com/us.yimg.com/i/us/we/52/'.$weather_forecast[1]->attributes()->code.'.gif" title="'.$weather_forecast[1]->attributes()->text.'" alt="'.$weather_forecast[1]->attributes()->text.'" />';
			$html .= '	</td></tr>';
			$html .= '	<tr><td align="center">';
			$html .= '		'.$weather_forecast[0]->attributes()->low.'&#176;C - '.$weather_forecast[0]->attributes()->high.'&#176;C';
			$html .= '	</td>';
			$html .= '	<td align="center">';
			$html .= '		'.$weather_forecast[1]->attributes()->low.'&#176;C - '.$weather_forecast[1]->attributes()->high.'&#176;C';
			$html .= '	</td></tr>';
			$html .= '</table>';
			//$html .= '<p class="Discreet">Data provided by Yahoo</p>';
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
		$sql = 'SELECT 	image_id, image_title
			FROM	images
			WHERE	image_image_type_id = 9
			AND	DATE(image_last_displayed_timestamp) = CURRENT_DATE()';
		$query = $this->db->query($sql);
		if($query->num_rows() == 0){
			$sql = 'SELECT image_id, image_title
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
		$title = $query->row()->image_title;
		return imageLocTag($id,'banner',false,$title);
	}

	/*
	 * Function to obtain a random quote for today.
	 * Returns the quote_text and quote_author in an array.
	 */
	function GetQuote() {
		$this->load->helper('images');
		$sql = 'SELECT quote_text, quote_author
			FROM quotes
			WHERE DATE(quote_last_displayed_timestamp) = CURRENT_DATE()';
		$query = $this->db->query($sql);
		if($query->num_rows() == 0){
			$sql = 'SELECT quote_id, quote_text, quote_author
				FROM quotes
				WHERE 1
				ORDER BY quote_last_displayed_timestamp
				LIMIT 0,1';
			$query = $this->db->query($sql);
			$sql = 'UPDATE quotes
				SET quote_last_displayed_timestamp = CURRENT_TIMESTAMP()
				WHERE quote_id = ?';
			$update = $this->db->query($sql,array($query->row()->quote_id));
		}
		return $query->row();
	}


}
?>
