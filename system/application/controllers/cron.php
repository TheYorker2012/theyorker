<?php

/**
 *	Cron Tasks
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Cron extends Controller
{

	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		show_404();
	}
	
	function flickr_update ()
	{
		$this->load->library('Flickr');
		$this->load->model('flickr_model');

		$flickr = new FlickrAPI;

		$last_update = $this->flickr_model->cronGetLast();;
		$page_number = 1;
		$changes = $flickr->getChangesSince($last_update, $page_number);
		if (!empty($changes['photos']['total']) && $changes['photos']['total'] > 0) {
			while ($page_number <= $changes['photos']['pages']) {
				foreach ($changes['photos']['photo'] as $change) {
					$details = $flickr->getPhotoInfo($change['id']);
					if (!empty($details['photo'])) {
						$photo = $details['photo'];
						$this->flickr_model->addPhoto(
							$photo['id'],
							$photo['secret'],
							$photo['server'],
							$photo['farm'],
							$photo['license'],
							$photo['owner']['nsid'],
							$photo['owner']['realname'],
							$photo['owner']['username'],
							$photo['title']['_content'],
							$photo['description']['_content'],
							$photo['dates']['posted'],
							$photo['dates']['taken'],
							$photo['dates']['lastupdate']
						);
						if (!empty($photo['tags']['tag'])) {
							foreach ($photo['tags']['tag'] as $tag) {
								$this->flickr_model->tag(
									$photo['id'],
									$tag['id'],
									$tag['raw'],
									$tag['_content']
								);
							}
						}
					}
				}
				$page_number++;
				if ($page_number <= $changes['photos']['pages']) {
					$changes = $flickr->getChangesSince($last_update, $page_number);
				}
			}
		}
		$this->flickr_model->cronSetLast();
	}
	
}

?>