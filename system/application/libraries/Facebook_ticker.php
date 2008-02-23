<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *	@file		libraries/Facebook_ticker.php
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 *	@brief		Library of functions for use with Facebook news ticker
 */

/// Main library class
class Facebook_ticker
{
	private $CI;
	public $fb_config;
	public $facebook;
	public $client;
	private $image_list;

	/**
	 * @brief Default Constructor
	 */
	function __construct()
	{
		// Get Code Igniter Instance
		$this->CI = &get_instance();
		// Load news model
		$this->CI->load->model('news_model');
		// Load Facebook Config
		$this->CI->load->config('facebook');
		$this->CI->load->helper('facebook');
		// Setup access
		$this->fb_config = $this->CI->config->Item('facebook');
		$this->facebook = new FacebookPlatform($this->fb_config['ticker']['api_key'], $this->fb_config['ticker']['secret']);
		$this->client = &$this->facebook->api_client;
	}

	function Authenticate ()
	{
		$fbuid = $this->facebook->require_login();
		if ($fbuid) {
			try {
				if ($this->client->users_isAppAdded()) {
					// The user has added our app all is OK
					return true;
				} else {
					// The user has not added our app
					$this->facebook->require_add();
				}
			} catch (Exception $ex) {
				// This will clear cookies for your app and redirect them to a login prompt
				$this->facebook->set_user(null, null);
				$this->facebook->redirect($this->facebook->require_login());
				exit;
			}
		} else {
			// The user has never used our app
			$this->facebook->require_add();
		}
		return false;
	}

	function CanvasPage ($sub_view, $data = array())
	{
		$this->facebook->require_frame();
		$data['view'] = $sub_view;
		$this->CI->load->view('facebook/ticker/frame_canvas', $data);
	}

	function SetFBML ($byline_id = NULL)
	{
		$content = '<fb:fbml version="1.1">
					<fb:wide>
						<fb:subtitle seeallurl="http://apps.facebook.com/theyorker/">
							The latest online independent student news...
						</fb:subtitle>
						<a href="http://www.theyorker.co.uk">
							<img src="http://www.theyorker.co.uk/images/prototype/homepage/facebook_yorker_wide.jpg" alt="The Yorker" />
						</a>
						<fb:ref handle="global_news_large" />
					</fb:wide>
					<fb:narrow>
						<a href="http://www.theyorker.co.uk">
							<img src="http://www.theyorker.co.uk/images/prototype/homepage/facebook_yorker_wide.jpg" alt="The Yorker" />
						</a>
						<fb:ref handle="global_news_small" />
					</fb:narrow>';
		if ($byline_id !== NULL) {
			$content .= '<fb:profile-action url="http://www.theyorker.co.uk/news/archive/reporter/'.$byline_id.'/">View my articles</fb:profile-action>';
		}
		$content .= '</fb:fbml>';

		$this->client->profile_setFBML($content);
	}

	function TickerHTML ()
	{
		$article_cache = array();
		$this->image_list = array();
		$this->CI->load->model('facebookticker_model');
		$content_types = $this->CI->facebookticker_model->GetContentTypeArticleCount();
		foreach ($content_types as $type) {
			$filters = array(
				array('section', $type['content_type_id'])
			);
			$article_cache[$type['content_type_id']] = array_reverse($this->CI->news_model->GetArchive('search', $filters, 0, $type['article_count']));
		}

		$articles = array();
		$article_order = $this->CI->facebookticker_model->GetTickerSettings();
		foreach ($article_order as $order) {
			if ($order['facebook_article_content_type_id'] !== NULL) {
				$articles[] = array_pop($article_cache[$order['facebook_article_content_type_id']]);
			} elseif ($order['facebook_article_article_id'] !== NULL) {
				// Not implemented yet - don't do anything
			}
		}

		//$this->CI->load->library('academic_calendar');

		$content = '';
		foreach ($articles as $a) {
			$this->image_list[] = 'http://www.theyorker.co.uk/photos/small/' . $a['photo_id'];
            //$reporters = array();
			//foreach ($a['reporters'] as $r)
			//	$reporters[] = $r['name'];
			//$reporters = implode(', ', $reporters);
			//$published_date = $this->CI->academic_calendar->Timestamp($a['date']);
			//$article_date = $published_date->Format('l') . ', Week ' . $published_date->AcademicWeek() . ' ' . $published_date->Format('Y');

			$content .= '<div style="clear:both">
							<a href="http://www.theyorker.co.uk/news/' . $a['type_codename'] . '/' . $a['id'] . '">
								<img src="http://www.theyorker.co.uk/photos/small/' . $a['photo_id'] . '" alt="' . $a['photo_title'] . '" style="float:left; margin-bottom:3px" />
							</a>
							<div style="margin-left:75px">
								<span style="float:right">
									<fb:share-button class="url" href="http://www.theyorker.co.uk/news/' . $a['type_codename'] . '/' . $a['id'] . '" />
								</span>
								<a href="http://www.theyorker.co.uk/news/' . $a['type_codename'] . '/' . $a['id'] . '"><b>' . $a['heading'] . '</b></a>
								<br />
								<a href="http://www.theyorker.co.uk/news/' . $a['type_codename'] . '/">' . $a['type_name'] . '</a>
							</div>
						</div>';
		}
		$this->image_list[] = 'http://www.theyorker.co.uk/images/prototype/homepage/arrow.png';
		$content .= '	<div style="clear:both;text-align:right;">
							<a href="' . $this->facebook->get_add_url() . '">
								<img src="http://www.theyorker.co.uk/images/prototype/homepage/arrow.png" alt="Add this news to my profile!" />
								Add this news to my profile!
							</a>
						</div>';
		return $content;

	}

	function RefreshImageCache ()
	{
		if (count($this->image_list) == 0)
			$this->TickerHTML();
		foreach ($this->image_list as $image)
			$this->client->fbml_refreshImgSrc($image);
	}

	function TickerUpdate ()
	{
		$content = $this->TickerHTML();
		$this->facebook->set_user($this->fb_config['ticker']['user_id'], $this->fb_config['ticker']['session_key'], NULL);
		if ($this->client->fbml_setRefHandle('global_news_large', $content)) {
			if ($this->client->fbml_setRefHandle('global_news_small', $content)) {
				$this->RefreshImageCache();
				return true;
			}
		}
		return false;
	}

}

?>