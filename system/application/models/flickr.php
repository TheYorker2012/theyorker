<?php
define("API_Flickr", "755b930bf9c122c46ad44d07a15809b6");
/**
 * This model fetches photos and data from Flickr, using the Flickr API
 *
 * @author Mark Goodall <mark.goodall@gmail.com>
 * @depends CURL lib
 */
class Flickr extends Model {

	function Flickr()
	{
		// Call the Model constructor
		parent::Model();
	}
	
	/// Get minimal photo details for a group pool.
	/**
	 * @param $groupID string The group's id (eg: Yorker's group is 383786@N23)
	 * @param $page int The page number of results to return (first = 1)
	 * @param $perPage int How many are on each page
	 * @return array total(int) and photos(array)
	 */
	function getGroupPool($groupID, $page=1, $perPage = 50) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'http://api.flickr.com/services/rest/?method=flickr.groups.pools.getPhotos&api_key='.API_Flickr.'&group_id='.urlencode($groupID).'&per_page='.$perPage.'&page='.$page);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$xml = curl_exec($curl);
		curl_close($curl);
		if (!is_string($xml) || !strlen($xml)) {
			return false;
		} else {
			$xml = new SimpleXMLElement($xml);
			if ($xml['stat'] != 'ok') return false;
			$results['total'] = (int) $xml->photos['total'];
			$results['photos'] = array();
			foreach ($xml->photos->photo as $photo)
				$results['photos'][] = array('id'    => (int) $photo['id'],
				                             'title' => (string) $photo['title'],
				                             'owner' => (string) $photo['ownername'],
				                             'timestamp' => (int) $photo['dateadded']);
			return $results;
		}
	}

	/// Get interesting photo details.
	/**
	 * @param $photoID int The photo's unique id.
	 * @return array interesting data.
	 */
	function getPhotoDetails($photoID) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'http://api.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key='.API_Flickr.'&photo_id='.$photoID);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$xml = curl_exec($curl);
		if (!is_string($xml) || !strlen($xml)) {
			return false;
		} else {
			$xml = new SimpleXMLElement($xml);
			if ($xml['stat'] != 'ok') return false;
			$result = array('id' => (int) $xml->photo['id'],
			                'secret' => (string) $xml->photo['secret'],
			                'server' => (int) $xml->photo['server'],
			                'farm' => (int) $xml->photo['farm'],
			                'license' => (int) $xml->photo['license'],
			                'timestamp' => (int) $xml->photo['dateuploaded'],
			                'photographer' => (string) $xml->photo->owner['realname'],
			                'title' => (string) $xml->photo->title);
			foreach ($xml->photo->tags->tag as $tag) $result['tags'][] = (string) $tag;
		}
		curl_setopt($curl, CURLOPT_URL, 'http://api.flickr.com/services/rest/?method=flickr.photos.getSizes&api_key='.API_Flickr.'&photo_id='.$photoID);
		$xml = curl_exec($curl);
		curl_close($curl);
		if (!is_string($xml) || !strlen($xml)) {
			return false;
		} else {
			$xml = new SimpleXMLElement($xml);
			if ($xml['stat'] != 'ok') return false;
			$result['width'] = 0;
			foreach ($xml->sizes->size as $size) {
				if ($result['width'] < $size['width']) {
					$result['width'] = (int) $size['width'];
					$result['height'] = (int) $size['height'];
					$result['source'] = (string) $size['source'];
				}
			}
			return $result;
		}
	}

	/// Get interesting photo details for a pool's data
	/**
	 * @param $data array the 'photos' subarray from getGroupPool
	 */
	function populatePool(&$data) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		foreach ($data['photos'] as &$photo) {
			curl_setopt($curl, CURLOPT_URL, 'http://api.flickr.com/services/rest/?method=flickr.photos.getInfo&api_key='.API_Flickr.'&photo_id='.$photoID);
			$xml = curl_exec($curl);
			if (!is_string($xml) || !strlen($xml)) {
				continue;
			} else {
				$xml = new SimpleXMLElement($xml);
				if ($xml['stat'] != 'ok') continue;
				$photo = array('secret' => (string) $xml->photo['secret'],
				                'server' => (int) $xml->photo['server'],
				                'farm' => (int) $xml->photo['farm'],
				                'license' => (int) $xml->photo['license'],
				                'photographer' => (string) $xml->photo->owner['realname']);
				foreach ($xml->photo->tags->tag as $tag) $photo['tags'][] = (string) $tag;
			}
			curl_setopt($curl, CURLOPT_URL, 'http://api.flickr.com/services/rest/?method=flickr.photos.getSizes&api_key='.API_Flickr.'&photo_id='.$photoID);
			$xml = curl_exec($curl);
			if (!is_string($xml) || !strlen($xml)) {
				continue;
			} else {
				$xml = new SimpleXMLElement($xml);
				if ($xml['stat'] != 'ok') continue;
				$result['width'] = 0;
				foreach ($xml->sizes->size as $size) {
					if ($photo['width'] < $size['width']) {
						$photo['width'] = (int) $size['width'];
						$photo['height'] = (int) $size['height'];
						$photo['source'] = (string) $size['source'];
					}
				}
			}
		}
		curl_close($curl);
	}

}
?>