<?php

/**
 *	@brief	The Yorker - News Ticker Facebook Application Code
 *	@author	Chris Travis	(cdt502 - ctravis@gmail.com)
 */

// @TODO	Only accept links coming from Facebook!

class Ticker extends Controller {

	private $fb_config;
	private $facebook;
	private $client;

	/**
	 * @brief Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();
		// Load news model
		$this->load->model('news_model');
		// Load Facebook Config
		$this->load->config('facebook');
		$this->load->helper('facebook');
		// Setup access
		$this->fb_config = $this->config->Item('facebook');
		$this->facebook = new FacebookPlatform($this->fb_config['ticker']['api_key'], $this->fb_config['ticker']['secret']);
		$this->client = &$this->facebook->api_client;

		$fbuid = $this->facebook->get_loggedin_user();
		if ($fbuid) {
			try {
				if ($this->client->users_isAppAdded()) {
					// The user has added our app
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

		//print_r('Facebook Session Key: ' . $this->client->session_key . '<br />'."\n");
		//print_r('Require Add: ' . $this->facebook->require_add() . '<br />'."\n");
	}

	// @brief	Setup Dashboard title, tabs, top links etc
	function _dashboardHeader ($selected_tab = 'latest')
	{
		return '<fb:fbml version="1.1">
					<fb:dashboard>
					</fb:dashboard>
					<fb:tabs>
						<fb:tab-item href="http://apps.facebook.com/theyorker/" title="Latest News"' . (($selected_tab == 'latest') ? ' selected="true"' : '') . ' />
						<fb:tab-item href="http://apps.facebook.com/theyorker/myarticles/" title="My Articles"' . (($selected_tab == 'myarticles') ? ' selected="true"' : '') . ' />
						<fb:tab-item href="http://apps.facebook.com/theyorker/invite/" title="Invite Friends"' . (($selected_tab == 'invite') ? ' selected="true"' : '') . ' />
					</fb:tabs>
					<div style="width:90%; margin: 10px auto;">';
	}

	// @brief	Finish up dashboard content template
	function _dashboardFooter ()
	{
		return '</div></fb:fbml>';
	}

	// @brief	Canvas Page : List latest news in FBML
	function index()
	{
		if ($user = $this->facebook->require_login()) {
			$this->facebook->require_frame();
			$articles = $this->news_model->GetArchive('search', array(), 0, 15);
			$content = $this->_dashboardHeader();
			foreach ($articles as $a) {
	            $reporters = array();
				foreach ($a['reporters'] as $r)
					$reporters[] = $r['name'];
				$reporters = implode(', ', $reporters);
				$content .= '<div style="clear:both; border-bottom:1px solid #bbb; padding-bottom: 5px; margin-bottom: 10px;">
								<a href="http://www.theyorker.co.uk/news/' . $a['type_codename'] . '/' . $a['id'] . '">
									<img src="http://www.theyorker.co.uk/photos/small/' . $a['photo_id'] . '" alt="' . $a['photo_title'] . '" style="float:left; margin-bottom:5px" />
								</a>
								<div style="margin-left:75px">
									<span style="float:right">
										<fb:share-button class="url" href="http://www.theyorker.co.uk/news/' . $a['type_codename'] . '/' . $a['id'] . '" />
									</span>
									<a href="http://www.theyorker.co.uk/news/' . $a['type_codename'] . '/' . $a['id'] . '"><b>' . $a['heading'] . '</b></a>
									<br />' . $a['blurb'] . '<br />
									<i>by ' . $reporters . '</i>
								</div>
								<div style="clear:both"></div>
							</div>';
			}
			$content .= $this->_dashboardFooter();
			echo($content);
		}
	}

	function myarticles ()
	{
		// Load yorker's user management
		$this->load->model('user_auth');

		// PHP Sessions don't work with Facebook!
		session_destroy();
		if (isset($_POST["fb_sig_session_key"])) {
			$_fb_sig_session_key = str_replace("-","0",$_POST["fb_sig_session_key"]);
			session_id($_fb_sig_session_key);
		}
		session_start();

		if ($user = $this->facebook->require_login()) {
			$this->facebook->require_frame();
			$content = $this->_dashboardHeader('myarticles');

			if ($this->user_auth->isLoggedIn == 1) {
				echo('LoggedIn');
				if ($this->user_auth->officeLogin == 1) {
					echo('OfficeAccess');
				} else {
					echo('NoOfficeAccess');
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
						if (isset($_POST["fb_sig_session_key"])) {
							$_fb_sig_session_key = str_replace("-","0",$_POST["fb_sig_session_key"]);
							session_id($_fb_sig_session_key);
						}
						session_start();
						session_decode($session_data);
						// Login was successful
print_r('Session ID: ' . session_id());
//						$this->facebook->redirect('http://apps.facebook.com/theyorker/myarticles/');
					} catch (Exception $e) {
						// Login failed
						$content .= '<div style="color:red">' . $e->getMessage() . '</div>';
					}
				}

				// Show Yorker login box
				$content .= '
					<div>
						To access this feature you must be a reporter for the Yorker.<br />
						If you are a reporter, please login with your Yorker account details below.
						<br />&nbsp;
					</div>
					<form action="http://apps.facebook.com/theyorker/myarticles/" method="post">
						<fieldset style="border:0">
							<label for="yorker_username" style="display:block;clear:both;float:left;width:30%;text-align:right;margin:0.4em;">Username:</label>
							<input type="text" name="yorker_username" id="yorker_username" value="" style="float:left;margin:0.2em;" />
							<br />
							<label for="yorker_password" style="display:block;clear:both;float:left;width:30%;text-align:right;margin:0.4em;">Password:</label>
							<input type="password" name="yorker_password" id="yorker_password" value="" style="float:left;margin:0.2em;" />
							<br />
							<input type="submit" name="yorker_login" id="yorker_login" value="Login" style="float:right;margin:0.5em;" />
						</fieldset>
					</form>
				';
				echo('NotLoggedIn');
			}

			$content .= $this->_dashboardFooter();
			echo($content);
echo(($this->user_auth->isLoggedIn) ? 'true' : 'false');
echo(($this->user_auth->isLoggedIn == 1) ? '1' : 'not 1');
echo(($this->user_auth->isLoggedIn == '1') ? 'str 1' : 'not str 1');
echo(($this->user_auth->isLoggedIn === TRUE) ? 'TRUE' : 'FALSE');
echo('Logged in: ' . $this->user_auth->isLoggedIn . ' - ' . $_SESSION['ua_loggedin']);
echo('Office Access?: ' . $this->user_auth->officeLogin . ' - ' . $_SESSION['ua_hasoffice']);
print_r($_SESSION);
print_r($_POST);
		}
	}

	function invite ()
	{
		if ($user = $this->facebook->require_login()) {
			$this->facebook->require_frame();
			$content = $this->_dashboardHeader('invite');
			$content .= '<fb:request-form type="The Yorker" action="http://apps.facebook.com/theyorker/invite/" method="post" invite="true" content="The Yorker provides online independent student news. Why not check out the website and find out what\'s hot in York? <fb:req-choice url=\'http://www.facebook.com/add.php?api_key=' . $this->fb_config['ticker']['api_key'] . '\' label=\'Read the latest news!\' />">
							<fb:multi-friend-selector actiontext="Select the friends you wish to invite to add The Yorker application." bypass="cancel" />
						</fb:request-form>';
			$content .= $this->_dashboardFooter();
			echo($content);
		}
	}

	function feedpost ()
	{
		if ($user = $this->facebook->require_login()) {
			$this->facebook->require_frame();
			$articles = $this->news_model->GetArchive('search', array(), 0, 1);

			$article_headline = $articles[0]['heading'];
			$article_link = 'http://www.theyorker.co.uk/news/' . $articles[0]['type_codename'] . '/' . $articles[0]['id'] . '/';
			$article_blurb = $articles[0]['blurb'];
			$photo = 'http://www.theyorker.co.uk/photos/small/' . $articles[0]['photo_id'] . '/';
	
			$title = '{actor} has just written an article on <a href="' . $article_link . '">The Yorker</a>.';
			$body = '<b>' . $article_headline . '</b><i>' . $article_blurb;
			$body = substr($body, 0, 193) . '...</i>';
	
			if (!$this->client->feed_publishTemplatizedAction($user, $title, '', $body, '', '', $photo, $article_link)) {
				// Error posting article to facebook
				// @TODO: Find out what the error is and tell user
			}
			echo('Feed Post Submitted');
		}
	}

	function setticker ()
	{
		if ($user = $this->facebook->require_login()) {
			$this->facebook->require_frame();

			$content = '<fb:fbml version="1.1">
						<fb:wide>
							<fb:subtitle seeallurl="http://apps.facebook.com/theyorker/">
								The latest online independent student news...
							</fb:subtitle>
							<a href="http://www.theyorker.co.uk">
								<img src="http://www.theyorker.co.uk/images/prototype/homepage/facebook_yorker_wide.jpg" />
							</a>
							<fb:ref handle="global_news_large" />
						</fb:wide>
						<fb:narrow>
							<a href="http://www.theyorker.co.uk">
								<img src="http://www.theyorker.co.uk/images/prototype/homepage/facebook_yorker_wide.jpg" />
							</a>
							<fb:ref handle="global_news_small" />
						</fb:narrow>
						<fb:profile-action url="http://www.theyorker.co.uk/news/archive/reporter/55/">
							View my articles
						</fb:profile-action>
						</fb:fbml>';
	
			print_r($this->client->profile_setFBML($content));
		}
	}

	function ticker_update ()
	{
		$articles = $this->news_model->GetArchive('search', array(), 0, 3);

		$content = '';
		foreach ($articles as $a) {
            $reporters = array();
			foreach ($a['reporters'] as $r)
				$reporters[] = $r['name'];
			$reporters = implode(', ', $reporters);

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
								<i>by ' . $reporters . '</i>
							</div>
						</div>';
		}

		$this->facebook->set_user($this->fb_config['ticker']['user_id'], $this->fb_config['ticker']['session_key'], $expires=null);
		print_r($this->client->fbml_setRefHandle('global_news_large', $content));
		print_r($this->client->fbml_setRefHandle('global_news_small', $content));
	}
}
?>