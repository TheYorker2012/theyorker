<?php

/**
 *	Roses Tab
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Roses extends Controller
{

	function __construct()
	{
		parent::Controller();
		$this->load->model('news_model');
		$this->load->model('home_model');
		$this->load->model('home_hack_model2', 'home_hack_model');
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;

		$data = array();
		$data['liveblog'] = $this->home_hack_model->getArticlesByTags(array('Roses 2010', 'liveblog'), 1);
		$data['others'] = $this->home_hack_model->getArticlesByTags(array('Roses 2010'), 15);

		$sql = 'SELECT article_liveblog_wikitext AS text FROM article_liveblog WHERE article_liveblog_article_id = ? AND article_liveblog_deleted = 0 ORDER BY article_liveblog_posted_time DESC LIMIT 0, 5';
		$query = $this->db->query($sql, array($data['liveblog'][0]['id']));
		$cache = '';
		foreach ($query->result_array() as $row) {
			$cache .= $row['text'] . "\n\n";
		}
		$this->load->library('image');
		$this->load->model('photos_model');
		$this->load->library('wikiparser');
		$this->wikiparser->add_image_override(-1, '<img src="/images/version2/rose_lancashire.png" alt="Lancaster" />', 'Lancaster Win');
		$this->wikiparser->add_image_override(-2, '<img src="/images/version2/rose_yorkshire.png" alt="York Win" />', 'York Win');
		$this->wikiparser->add_image_override(-3, '<img src="/images/version2/rose_draw.png" alt="Draw" />', 'Draw');
		$photo_requests = $this->photos_model->GetPhotoRequestsForArticle($data['liveblog'][0]['id']);
		foreach ($photo_requests as $photo) {
			$this->wikiparser->add_image_override($photo['photo_number'], $this->image->getThumb($photo['photo_id'], 'small', true), $photo['photo_caption']);
		}
		$cache = $this->wikiparser->parse($cache);
		$data['latest'] = array(array('cache' => $cache));

		$sql = 'SELECT * FROM roses_scores ORDER BY event_time ASC';
		$data['events'] = $this->db->query($sql)->result_array();

		$score_york = 0;
		$score_lancs = 0;

		foreach ($data['events'] as $events) {
			if (empty($events['event_score_time'])) continue;
			if ($events['event_york_score'] > $events['event_lancaster_score']) {
				$score_york += $events['event_points'];
			} else if ($events['event_york_score'] < $events['event_lancaster_score']) {
				$score_lancs += $events['event_points'];
			} else {
				$score_york += $events['event_points'] / 2;
				$score_lancs += $events['event_points'] / 2;
			}
		}

		$data['score_york'] = $score_york;
		$data['score_lancs'] = $score_lancs;

		// Flickr

		$sql = 'SELECT * FROM cron_updates WHERE service_name = "roses_flickr_photos" && last < ?';
		$query = $this->db->query($sql, array(mktime() - (60*10)));
		if ($query->num_rows() > 0) {
			$params = array(
				'api_key'		=>	'117e4fb7e8f54e5425b7dc4e28dec883',
				'method'		=>	'flickr.photosets.getPhotos',
				'format'		=>	'php_serial',
				'photoset_id'	=>	'72157623961104110',
				'extras'		=>	'license,owner,date_upload',
				'per_page'		=>	'500',
				'page'			=>	'1'
			);
			$encoded_params = array();
			foreach ($params as $key => $value) {
				$encoded_params[] = urlencode($key) . '=' . urlencode($value);
			}
			$url = 'http://api.flickr.com/services/rest/?' . implode('&', $encoded_params);
			$response = file_get_contents($url);
			$response = unserialize($response);
			$sql = 'REPLACE INTO flickr_photos SET photo = ?, link = ?, title = ?, added = ?';
			if (!empty($response['photoset']['photo'])) {
				foreach ($response['photoset']['photo'] as $photo) {
					$photo['owner'] = 'theyorker';
					$query = $this->db->query($sql, array(
						'http://farm' . $photo['farm'] . '.static.flickr.com/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'] . '_s.jpg',
						'http://www.flickr.com/photos/' . $photo['owner'] . '/' . $photo['id'],
						$photo['title'],
						$photo['dateupload']
					));
				}
			}
			$sql = 'UPDATE cron_updates SET last = ? WHERE service_name = "roses_flickr_photos"';
			$query = $this->db->query($sql, array(mktime()));
		}

        $sql = 'SELECT * FROM flickr_photos ORDER BY added DESC LIMIT 0, 16';
        $data['photos'] = $this->db->query($sql)->result_array();
		// Flickr

		$this->pages_model->SetPageCode('homepage_roses');
		$this->main_frame->SetData('menu_tab', 'roses');
		$this->main_frame->IncludeCss('stylesheets/home.css');
		$this->main_frame->SetContentSimple('homepages/roses', $data);
		$this->main_frame->Load();
	}
}
?>
