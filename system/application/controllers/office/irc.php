<?php

/**
 * @file controllers/office/irc.php
 * @brief Office IRC web client.
 * @author James Hogan (jh559)
 */

/// IRC controller
class Irc extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::Controller();
	}
	
	/// Main index page
	function index()
	{
		$this->office();
	}
	
	function free()
	{
		if (!CheckPermissions('public')) return;
		
		$RequiredReferer = 'http://trunk.yorker.albanarts.com/office/irc';
		$RequiredReferer = NULL;
		
		if ($RequiredReferer != NULL &&
			(!isset($_SERVER['HTTP_REFERER']) ||
			 $_SERVER['HTTP_REFERER'] != $RequiredReferer))
		{
			show_404();
		}
		
		if (!isset($_GET['username']) || !isset($_GET['fullname'])) {
			show_404();
		}
		
		$data = array(
			'Username' => $_GET['username'],
			'Fullname' => $_GET['fullname'],
		);
		$this->load->view('office/irc/embedded', $data);
	}
	
	/// Office IRC
	function office()
	{
		if (!CheckPermissions('office')) return;
		$this->_irc_channel();
	}
	
	/// Show an irc channel page
	function _irc_channel()
	{
		$this->pages_model->SetPageCode('office_irc');
		$this->main_frame->SetTitleParameters();
		$data = array(
			'Help' => $this->pages_model->GetPropertyWikitext("help_main"),
			'IrcHelp' => $this->pages_model->GetPropertyWikitext("irc_help"),
			'Embed' => true,
		);
		$this->main_frame->SetContentSimple('office/irc/irc', $data);
		$this->main_frame->Load();
	}
	
	/// Automatically add links for urls, email addresses, channels
	function _autolinkify($text)
	{
		$link_handlers = array(
			'(https?|ftp):\/\/(&amp;|[^\s,@&])+'   => '_autolinkify_url',
// 			'[^\s,@&:]+@[^\s,@&:]+'  => '_autolinkify_email', // Don't bother with email addresses for now, they're a bit trickier
			'#theyorker\w*' => '_autolinkify_chan',
		);
		
		foreach ($link_handlers as $regexp => $handler) {
			$text = preg_replace_callback("/$regexp/i", array(&$this, $handler), $text);
		}
		return $text;
	}
	function _autolinkify_url($matches)
	{
		return "<a href=\"$matches[0]\">$matches[0]</a>";
	}
	function _autolinkify_email($matches)
	{
		return "<a href=\"mailto:$matches[0]\">$matches[0]</a>";
	}
	function _autolinkify_chan($matches)
	{
		return "<a style=\"cursor:pointer;\" onclick=\"irc_join_channel('$matches[0]');\">$matches[0]</a>";
	}
	
	/// Handle ajax data from irc interface.
	/**
	 * @todo Handle when not logged in nicely
	 */
	function ajax($variation = NULL)
	{
		if ('embeddedlive' === $variation) {
			if (!CheckPermissions('public', false)) return;
			if (!isset($_GET['username']) || !isset($_GET['fullname'])) {
				show_404();
			}
			$username = $_GET['username'];
			$fullname = $_GET['fullname'];
		} else {
			if (!CheckPermissions('office', false)) return;
			$username = $this->user_auth->username;
			$fullname = $this->user_auth->firstname.' '.$this->user_auth->surname;
		}
		$nick = str_replace(' ', '', $fullname);
		
		$data = array(
			'Errors' => array(),
			'Messages' => array(),
		);
		if (isset($_GET['cmd'])) {
			$this->load->library('irc_client');
			if ($_GET['cmd'] == 'connect') {
				// Start a new client server, don't terminate until client server is finished.
				// The script will continue to run, timeout will be disabled.
				// However the connection to the browser will be closed
				
				// Only have one client server running at a time
				if (IrcClientManager::IsConnected()) {
					$this->irc_client->Attach();
					if ($this->irc_client->Attached()) {
						$this->irc_client->ForceDisconnect();
					}
				}
				// This is the main client server object
				$server = new IrcClientManager('irc.afsmg.co.uk');
				
				if ($server->IsConnected()) {
					// Use the user's username, and fullnames in irc names
					$server->Login($username, $nick, $fullname);
					$server->Join('#theyorkeroffice');
					
					// Output empty XML document and close the connection with
					// the browser
					ob_end_clean();
					header('content-type: text/xml');
					header("Connection: close");
					ignore_user_abort();
					ob_start();
					echo('<?xml version="1.0" encoding="UTF-8"?><irc></irc>'."\n");
					$size = ob_get_length();
					header('Content-Length: '.$size);
					ob_end_flush();
					flush();
					
					// Now start listening to the sockets.
					return $server->listen();
				}
				else {
					$data['Errors'][] = array(
						'_attr' => array(
							'code' => 1,
							'retry' => 1,
						),
						"Connection failed",
					);
				}
				
			} else {
				$this->irc_client->Attach();
				session_commit();
				if (!$this->irc_client->Attached()) {
					$data['Errors'][] = array(
						'_attr' => array(
							'code' => 1,
							'retry' => 0,
						),
						"Unable to process message, IPC failure.",
					);
				} else {
					
					$new_message = NULL;
					$get_messages = false;
					switch ($_GET['cmd']) {
						// This command lets the server know that the interface is still alive
						// it also waits a while for any new messages which it can return
						case 'ping':
							$this->irc_client->Ping();
							$get_messages = true;
							break;
							
						// A query has been written in one of the channels
						case 'msg':
							if (isset($_GET['channel']) && isset($_GET['msg'])) {
								$new_message = $this->irc_client->InterpretQuery($_GET['channel'], $_GET['msg']);
								if (false === $new_message) {
									$data['Errors'][] = array(
										'_attr' => array(
											'code' => 1,
											'retry' => 0,
										),
										"Unable to send message, IPC failure.",
									);
								}
							}
							break;
							
						// Disconnect and end any client servers.
						case 'disconnect':
							if ($this->irc_client->Attached()) {
								$this->irc_client->Disconnect();
							} else {
								$data['Errors'][] = array(
									'_attr' => array(
										'code' => 1,
										'retry' => 0,
									),
									"Unable to disconnect, IPC failure.",
								);
							}
							$continue = false;
							break;
					}
					
					if (is_array($new_message)) {
						$new_message['content'] = $this->_autolinkify(htmlentities($new_message['content'], ENT_QUOTES, 'utf-8'));
						$data['Messages'][] = $new_message;
					}
					if (/*$this->irc_client->Attached() &&*/ $get_messages) {
						// wait up to 20 seconds for messages before returning.
						$messages = $this->irc_client->WaitForMessages(20*1000);
						if (is_array($messages)) {
							foreach ($messages as $message) {
								$message['content'] = $this->_autolinkify(htmlentities($message['content'], ENT_QUOTES, 'utf-8'));
								$data['Messages'][] = $message;
							}
						}
						// Stop receiving messages
						$this->irc_client->Unping();
					}
				}
			}
		}
		// Write the xml of any messages.
		$this->load->view('office/irc/xml.php', $data);
	}
}


?>