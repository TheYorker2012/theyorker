<?php

class Liveblog extends Controller
{
	function __construct()
	{
		parent::Controller();
		$this->load->library('Twitter');
		$this->load->model('twitter_model');
	}

	function index ()
	{
		if (!CheckPermissions('office')) return;
		// @TODO: Check live blogging permission!

		$this->pages_model->SetPageCode('office_liveblogging');
		$data = array();
		$data['twitter_id'] = $this->twitter_model->getTwitterId($this->user_auth->entityId);
		$data['heading'] = $this->pages_model->GetPropertyText('heading');
		$data['install_info'] = $this->pages_model->GetPropertyWikitext('install_info');
		$data['uninstall_info'] = $this->pages_model->GetPropertyWikitext('uninstall_info');

		// Set up the content
		$this->main_frame->SetContentSimple('office/liveblog/index', $data);
		$this->main_frame->Load();
	}

	function install ()
	{
		if (!CheckPermissions('office')) return;
		// @TODO: Check live blogging permission!
		if (!empty($_POST['twitter_user']) && !empty($_POST['twitter_pass'])) {
			// Check can login
			$Twitter = new TwitterXML($_POST['twitter_user'], $_POST['twitter_pass']);
			if ($Twitter->verifyCredentials()) {
				// Get user's Twitter ID
				$tuser_info = $Twitter->getUserInfo($_POST['twitter_user']);
				$tuser_id = $tuser_info->id;
				// Store user's Twitter ID
				$this->twitter_model->setTwitterId($this->user_auth->entityId, $tuser_id);
				// User follows Yorker's Admin Account
				$Twitter->addFriend($this->config->item('twitter_admin_userid'));
				// Yorker Admin Account follows User
				$TwitterCP = new TwitterXML($this->config->item('twitter_admin_userid'), $this->config->item('twitter_admin_passwd'));
				$TwitterCP->addFriend($tuser_id);
				$this->main_frame->AddMessage('success', 'Your Twitter account has been successfully linked.');
			} else {
				$this->main_frame->AddMessage('error', 'The Twitter login credentials you supplied were incorrect.');
			}
		} else {
			$this->main_frame->AddMessage('error', 'Please provide your Twitter login details.');
		}
		redirect('/office/liveblog');
	}

	function uninstall ()
	{
		if (!CheckPermissions('office')) return;
		// Check user has a Twitter account
		$tuser_id = $this->twitter_model->getTwitterId($this->user_auth->entityId);
		if ($tuser_id != '') {
			// Remove user's stored Twitter ID
			$this->twitter_model->setTwitterId($this->user_auth->entityId, '');
			// Remove 'follow' from Yorker Admin Account so that Twitter blocks messages
			$TwitterCP = new TwitterXML($this->config->item('twitter_admin_userid'), $this->config->item('twitter_admin_passwd'));
			$TwitterCP->removeFriend($tuser_id);
		} else {
			$this->main_frame->AddMessage('error', 'There are no Twitter details associated with your user account.');
		}
		redirect('/office/liveblog');
	}

	function update ()
	{
		$TwitterCP = new TwitterXML($this->config->item('twitter_admin_userid'), $this->config->item('twitter_admin_passwd'));
		$msgs = $TwitterCP->getDirectMessages();
		$msgs = $msgs->direct_message;
		$msg_count = count($msgs);
		// @TODO: Cope with more than 20 new messages
		// Process msgs from oldest to newest
		for ($i = $msg_count - 1; $i >= 0; $i--) {
			// @TODO: Also check they have live blogging permission
			$user = $this->twitter_model->checkTwitterAccess($msgs[$i]->sender_id);
			if (!$user) {
				$send_msg = $TwitterCP->sendDirectMessage($msgs[$i]->sender_id, 'Unauthorised User!');
print_r($send_msg);
			} else {
				echo('msgid: ' . $msgs[$i]->id . '<br />');
				echo('date: ' . $msgs[$i]->created_at . '<br />');
				echo('txt: ' . $msgs[$i]->text . '<br />');
				echo('sender: ' . $msgs[$i]->sender_id . '<hr />');
				$msg_date = strtotime($msgs[$i]->created_at);
				$cmds = explode(' ', $msgs[$i]->text);
				switch ($cmds[0]) {
					default:
						if (is_numeric($cmds[0])) {
							// Process live blog update
							$article_id = $cmds[0];
							unset($cmds[0]);
							$text = '\'\'\'' . date('D @ H:i', $msg_date) . '\'\'\' ' . implode(' ', $cmds) . "\n\n";
							// Find article to edit
							$this->load->model('article_model');
							$this->load->model('requests_model');
							$this->load->library('wikiparser');
							$wiki_cache = $this->wikiparser->parse($text);
							$revision = $this->article_model->GetLatestRevision($article_id);
							$rev_data = $this->article_model->GetRevisionData($revision);
							$revision = $this->article_model->CreateNewRevision($article_id, $user->user_entity_id, $rev_data['headline'], $rev_data['subheadline'], $rev_data['subtext'], $rev_data['blurb'], $text . $rev_data['text'], $wiki_cache . $rev_data['cache']);
							$publish_date = date('Y-m-d H:i:s', mktime());
							$this->requests_model->PublishArticle($article_id,$revision,$publish_date);
							$send_msg = $TwitterCP->sendDirectMessage($msgs[$i]->sender_id, 'Successfully updated article: \'' . $rev_data['headline'] . '\'');
print_r($send_msg);
						} else {
							$send_msg = $TwitterCP->sendDirectMessage($msgs[$i]->sender_id, 'Unknown Command!');
print_r($send_msg);
						}
				}
			}
			// Delete message after processing
			$TwitterCP->deleteDirectMessage($msgs[$i]->id);
		}
	}
}
?>