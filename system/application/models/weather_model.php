<?php

class Weather_Model extends Model {
	function Weather_Model() {
		parent::Model();
	}
	function GetWeather() {
		$sql = 'SELECT weather_cache_timestamp, weather_cache_html
			FROM weather_cache
			WHERE weather_cache_timestamp > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 4 HOUR) ';
		$query = $this->db->query($sql);
		if ($query->num_rows() == 0 || true){
			$weather_data = 'http://xml.weather.yahoo.com/forecastrss?p=UKXX0162&u=c';
			$response = file_get_contents($weather_data);
			$weather = new simplexmlelement($response);
			$weather->registerXPathNamespace('data','http://xml.weather.yahoo.com/ns/rss/1.0');
			$weather_forecast = $weather->xpath('//channel/item/data:forecast');

			$html = '<ul>';
			$html = $html.'<li>'.$weather_forecast[0]->attributes()->day.' -  '.$weather_forecast[0]->attributes()->text.'. High: ';
			$html = $html.$weather_forecast[0]->attributes()->high.', Low: '.$weather_forecast[0]->attributes()->low.'</li>';
			$html = $html.'<li>'.$weather_forecast[1]->attributes()->day.' -  '.$weather_forecast[1]->attributes()->text.'. High: ';
			$html = $html.$weather_forecast[1]->attributes()->high.', Low: '.$weather_forecast[1]->attributes()->low.'</li>';
			$html = $html.'</ul>';

			$sql = 'DELETE FROM weather_cache';
			$query = $this->db->query($sql);
			$sql = 'INSERT INTO weather_cache(weather_cache_html) VALUES (?)';
			$query = $this->db->query($sql,array($html));
			return $html;
		} else {
			$row = $query->row();
			return $row->weather_cache_html;
		}
	}
}
?>
