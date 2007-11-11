<?php
/**
 *	@brief		The Yorker - News Ticker Facebook Application Code
 *	@author		Chris Travis	(cdt502 - ctravis@gmail.com)
 */

class Ticker extends Controller {

	/**
	 * @brief Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();
		// Load Facebook Ticker library
		$this->load->library('facebook_ticker');
		// Load Models
		$this->load->model('news_model');
	}

	// @brief	Canvas Page : List latest news in FBML
	function index()
	{
		if ($user = $this->facebook_ticker->Authenticate()) {
			$data['selected_tab'] = 'latest';
			$data['articles'] = $articles = $this->news_model->GetArchive('search', array(), 0, 15);
			$this->facebook_ticker->CanvasPage('facebook/ticker/article_list', $data);
		}
	}

	function myarticles ($sub_section = NULL, $action = NULL, $byline_id = NULL)
	{
		// Load yorker's user management
		$this->load->model('user_auth');
		$this->load->model('businesscards_model');

		// PHP Sessions don't work with Facebook!
		session_destroy();
		if (isset($_POST['fb_sig_session_key'])) {
			$_fb_sig_session_key = str_replace('-','0',$_POST['fb_sig_session_key']);
			session_id($_fb_sig_session_key);
		}
		session_start();

		if ($user = $this->facebook_ticker->Authenticate()) {

			if ((isset($_SESSION['ua_loggedin'])) && ($_SESSION['ua_loggedin'])) {
				if ((isset($_SESSION['ua_hasoffice'])) && ($_SESSION['ua_hasoffice'])) {
					// Get all facebook enabled bylines
					$data['facebook_bylines'] = $this->businesscards_model->GetUserBylinesForFacebook($_SESSION['ua_entityId']);
					// If no bylines linked to facebook or user requests - goto byline selection page
					if ((count($data['facebook_bylines']) == 0) || ($sub_section == 'bylines')) {
						// Get all user's bylines and prompt them to select which ones to (un)link with facebook
						$data['user_bylines'] = $this->businesscards_model->GetUserBylines($_SESSION['ua_entityId']);
						if (($action === NULL) || (!is_numeric($byline_id))) {
							// Process byline image
							$this->load->library('image');
							foreach ($data['user_bylines'] as &$byline) {
								if ($byline['business_card_image_id'] === NULL) {
									$byline['business_card_image_href'] = '';
								} else {
									$byline['business_card_image_href'] = 'http://www.theyorker.co.uk' . $this->image->getPhotoURL($byline['business_card_image_id'], 'userimage');
								}
							}
							$view = 'facebook/ticker/select_bylines';
						} else {
							// Check that operation is to be carried out on a byline owned by the user
							$user_owned_byline = false;
							foreach ($data['user_bylines'] as $byline) {
								if ($byline['business_card_id'] == $byline_id) {
									$user_owned_byline = true;
									break;
								}
							}
							if (!$user_owned_byline) {
								$_SESSION['fbticker_messages'][] = array('error', 'Unable to carry out the requested operation as this byline does not belong to you!');
							} else {
								switch ($action) {
									case 'link':
										if ($this->businesscards_model->BylineFacebookSetting($byline_id, 1)) {
											$_SESSION['fbticker_messages'][] = array('success', 'Byline successfully linked with Facebook.');
										} else {
											$_SESSION['fbticker_messages'][] = array('error', 'There was an error linking your byline to Facebook, please try again.');
										}
										break;
									case 'unlink':
										if ($this->businesscards_model->BylineFacebookSetting($byline_id, 0)) {
											$_SESSION['fbticker_messages'][] = array('success', 'Byline successfully un-linked with Facebook.');
										} else {
											$_SESSION['fbticker_messages'][] = array('error', 'There was an error un-linking your byline with Facebook, please try again.');
										}
										break;
									case 'action':
										$this->facebook_ticker->SetFBML($byline_id);
										break;
								}
							}
							$this->facebook_ticker->facebook->redirect('http://apps.facebook.com/theyorker/myarticles/bylines/');
						}
					} else {
						// Show current articles
						$filters = array();
						foreach ($data['facebook_bylines'] as $byline)
							$filters[] = array('reporter', $byline['business_card_id']);
						$data['articles'] = $this->news_model->GetArchive('search', $filters, 0, 30);

						// Check for extra operations requests
						if (($sub_section == 'article') && ($action == 'feedpost') && (is_numeric($byline_id))) {
							$this->_feedPost($byline_id);
							$this->facebook_ticker->facebook->redirect('http://apps.facebook.com/theyorker/myarticles/');
						}
						// Give various extra operations that can be carried out
						$data['extra_ops'] = true;
						$view = 'facebook/ticker/article_list';
					}
				} else {
					// Show 'not a writer / no office access error msg'
					$view = 'facebook/ticker/no_access';
				}
			} else {
				// Check for a login request
				if ((isset($_POST['yorker_username'])) && (isset($_POST['yorker_password']))) {
					try {
						// Attempt login
						$this->user_auth->login($_POST['yorker_username'], $_POST['yorker_password'], false);
						// Need to transfer the yorker's login info into the facebook session
						$session_data = session_encode();
						session_destroy();
						if (isset($_POST['fb_sig_session_key'])) {
							$_fb_sig_session_key = str_replace('-','0',$_POST['fb_sig_session_key']);
							session_id($_fb_sig_session_key);
						}
						session_start();
						session_decode($session_data);
						// Login was successful
						$this->facebook_ticker->facebook->redirect('http://apps.facebook.com/theyorker/myarticles/');
						$_SESSION['fbticker_messages'][] = array('success', 'You have been successfully logged in.');
					} catch (Exception $e) {
						// Login failed
						$_SESSION['fbticker_messages'][] = array('error', $e->getMessage());
					}
				}
				// Show Yorker login box
				$view = 'facebook/ticker/login';
			}

			$data['selected_tab'] = 'myarticles';
			$this->facebook_ticker->CanvasPage($view, $data);
		}
	}

	function invite ()
	{
		if ($user = $this->facebook_ticker->Authenticate()) {
			$data['selected_tab'] = 'invite';
			$data['app_users'] = $this->facebook_ticker->client->friends_getAppUsers();
			$this->facebook_ticker->CanvasPage('facebook/ticker/invite', $data);
		}
	}

	function _feedPost ($article_id)
	{
		if ($user = $this->facebook_ticker->Authenticate()) {
			$article = $this->news_model->GetSummaryArticle($article_id);

			$article_headline = $article['heading'];
			$article_link = 'http://www.theyorker.co.uk/news/' . $article['article_type'] . '/' . $article['id'] . '/';
			$article_blurb = $article['blurb'];
			$photo = 'http://www.theyorker.co.uk/photos/small/' . $article['photo_id'] . '/';

			$title = '{actor} has just written an article on <a href="' . $article_link . '">The Yorker</a>.';
			$body = '<b>' . $article_headline . '</b> <i>' . $article_blurb;
			$body = substr($body, 0, 193) . '...</i>';

			if ($this->facebook_ticker->client->feed_publishTemplatizedAction($user, $title, '', $body, '', '', $photo, $article_link)) {
				$_SESSION['fbticker_messages'][] = array('success', 'The requested article was posted on your feed.');
			} else {
				// Error posting article to facebook
				$_SESSION['fbticker_messages'][] = array('error', 'There was a problem posting the requested article to your feed, please try again.');
			}
		}
	}

	function welcome ()
	{
		if ($user = $this->facebook_ticker->Authenticate()) {
			$this->facebook_ticker->SetFBML();

			$_SESSION['fbticker_messages'][] = array('success', 'Thank you for adding The Yorker\'s News Ticker Application!');
			$this->facebook_ticker->facebook->redirect('http://apps.facebook.com/theyorker/');
		}
	}

}
?>