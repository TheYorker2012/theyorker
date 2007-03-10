<?php

/**
 *	@brief My testing page :)
 *	@author Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Travis extends Controller {

	/**
	 *	@brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
	}

	/**
	 * @brief Testing testing 1...2...3 ;)
	 */
	function index()
	{
		if (!CheckPermissions('office')) return;

		$data['test'] = '';

		// Set up the public frame
		$this->main_frame->SetTitle('Travis\' Ideas Page :)');
		$this->main_frame->SetContentSimple('test/travis-home', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	function campaign()
	{
		if (!CheckPermissions('office')) return;

		$data['test'] = '';

		// Set up the public frame
		$this->main_frame->SetTitle('Travis\' Ideas Page :)');
		$this->main_frame->SetContentSimple('test/travis-campaign', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	function imap()
	{
		if (!CheckPermissions('office')) return;

		$data['test'] = '';

		//Setup XAJAX
		$this->load->library('xajax');
        $this->xajax->registerFunction(array('_checkEmail', &$this, '_checkEmail'));
        $this->xajax->processRequests();
		$this->main_frame->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));

		// Set up the public frame
		$this->main_frame->SetTitle('Travis\' Ideas Page :)');
		$this->main_frame->SetContentSimple('test/travis-imap', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	function _checkEmail($user,$pass)
	{
		$xajax_response = new xajaxResponse();
		if (($user == '') || ($pass == '')) {
			$xajax_response->addScriptCall('msgError','Please enter your username and password');
		} else {
			$data = array();
			$email_count = 0;
			$buffer_old = '';

			$cnx = @fsockopen($user.'.imap.york.ac.uk',143);
			if (!$cnx) {
				$xajax_response->addScriptCall('msgError','Username does not exist!');
			} else {
				while (!feof($cnx)) {
					$buffer = trim(fgets($cnx, 4096));
					if ($buffer_old != $buffer) {
						// Following line used for debugging
						//print($buffer . '<br />');
						$message = explode(' ',$buffer);

						if ($message[0] == '*') {
							if ($message[1] == 'OK') {
								if ($message[2] == '[CAPABILITY') {
									// Successful server connection, so login
									fwrite($cnx,'a01 login '.$user.' '.$pass."\r\n");
									$data = array();
								} elseif ($message[2] == '[UNSEEN') {
									$data[] = substr($message[3],0,-1);
								}
							} elseif ($message[1] == 'SEARCH') {
								for ($i = 2; $i <= (count($message) - 1); $i++) {
									$data[] = $message[$i];
								}
							} elseif ($message[2] == 'EXISTS') {
								$email_count = $message[1];
							} elseif ($message[2] == 'FETCH') {
								$email = array();
//print_r($message);
								if (strpos($message[4],'\Seen') === FALSE) {
									$email['unread'] = 1;
								} else {
									$email['unread'] = 0;

								}
								// Get e-mail envelope
								for ($i = 0; $i <= 5; $i++) {
									unset($message[$i]);
								}
								$message = implode(' ',$message);
								// Get rid of leading ("
								$message = substr($message,2);
								$date = strpos($message,'"');
								$email['date'] = explode(' ',date('d-M H:i',strtotime(substr($message,0,$date))));
								if ($email['date'][0] == date('d-M')) {
									$email['date'] = $email['date'][1];
								} else {
									$email['date'] = $email['date'][0];
								}
								// Remove date and {space} at start of subject
								$message = substr($message,($date+2));
								if ($message[0] == '"') {
									$subject = strpos($message,'"',1);
									$email['subject'] = substr($message,1,($subject-1));
									$message = substr($message,($subject+2));
								} elseif ($message[0] == '{') {
									$subject = strpos($message,'}',1);
									$sub_count = substr($message,1,($subject-1));
									$message = substr($message,($subject+1));
									$email['subject'] = substr($message,0,$sub_count);
									$message = substr($message,($sub_count+1));
								}
								$email['subject'] = str_replace(' ','&nbsp;',$email['subject']);
								$message = explode('))',$message,5);
								$message[0] = substr(trim($message[0]),2);
								$index = 0;
								$sender = array();
								while ($message[0] != '') {
									if ($message[0][0] == '"') {
										$pos = strpos($message[0],'"',1);
										$sender[$index] = substr($message[0],1,($pos-1));
										$message[0] = substr($message[0],($pos+2));
									} else {
										$sender[$index] = '';
										$message[0] = substr($message[0],4);
									}
									$index++;
								}
//print_r($sender);
								$email['sender'] = $sender[0] . ' (' . $sender[2] . '@' . $sender[3] . ')';
								$data[] = $email;
							}
						}

						if ($message[0] == 'a01') {
							if ($message[1] == 'OK') {
								// Successfully logged in, so open inbox
								fwrite($cnx,'a02 examine inbox'."\r\n");
								$data = array();
							} else {
								$xajax_response->addScriptCall('msgError','Incorrect password!');
								fwrite($cnx,'a04 logout'."\r\n");
							}
						}

						if ($message[0] == 'a02') {
							if ($message[1] == 'OK') {
								// Successfully got inbox details
								if (count($data) == 1) {
									// There is at least 1 new email, check how many
									fwrite($cnx,'a03 search unseen'."\r\n");
									$data = array();
								} else {
									// No unread e-mails, get last 5 emails
									$email_count = $email_count - 4;
									$query = array();
									for ($i = 1; $i <= 5; $i++) {
										if ($email_count > 0) {
											$query[] = $email_count;
										}
										$email_count++;
									}
									fwrite($cnx,'a05 fetch ' . implode(',',$query) . ' (FLAGS ENVELOPE)'."\r\n");
									$data = array();
								}
							} else {
								$xajax_response->addScriptCall('msgError','Unable to find inbox');
								fwrite($cnx,'a04 logout'."\r\n");
							}
						}

						if ($message[0] == 'a03') {
							if ($message[1] == 'OK') {
								// Successfully got new email count, get last 5 emails
								$xajax_response->addScriptCall('checkedEmails',count($data));
								$email_count = $email_count - 4;
								$query = array();
								for ($i = 1; $i <= 5; $i++) {
									if ($email_count > 0) {
										$query[] = $email_count;
									}
									$email_count++;
								}
								fwrite($cnx,'a05 fetch ' . implode(',',$query) . ' (FLAGS ENVELOPE)'."\r\n");
								$data = array();
							} else {
								$xajax_response->addScriptCall('msgError','Unable to find any unread emails');
								fwrite($cnx,'a04 logout'."\r\n");
							}
						}

						if ($message[0] == 'a05') {
							if ($message[1] == 'OK') {
								// Got last 5 emails, so logout
								foreach ($data as $email) {
									$xajax_response->addScript("york_inbox[york_inbox_count] = new Array('" . $email['unread'] . "','" . $email['date'] . "','" . $email['subject'] . "','" . $email['sender'] . "'); york_inbox_count++;");
								}
								$xajax_response->addScriptCall('inboxContents');
								$xajax_response->addScriptCall('inboxChecked',date('H:i'));
								fwrite($cnx,'a04 logout'."\r\n");
								$data = array();
							} else {
								$xajax_response->addScriptCall('msgError','Unable to retrieve inbox contents');
								fwrite($cnx,'a04 logout'."\r\n");
							}
						}

						if ($message[0] == 'a04') {
							if ($message[1] == 'OK') {
								// Logged out, do nothing and let connection close
							} else {
								$xajax_response->addScriptCall('msgError','Logout error');
								fwrite($cnx,'a04 logout'."\r\n");
							}
						}
					}
					$buffer_old = $buffer;
				}
				$cnx = fclose($cnx);
			}
		}
		return $xajax_response;
	}

	function calendar()
	{
		if (!CheckPermissions('office')) return;

		$data['height_hour'] = 40;
		$data['width_page'] = 650;
		$data['width_time_col'] = 41;
		$data['width_day_col'] = 87;

		$data['startdate'] = mktime(0,0,0,2,25,2007);

		// Assuming ordered by date/time ASC
		$data['events'][] = array(
			'day' => 'sat',
			'title' => 'Meeting martians!',
			'start_date' => mktime(14,30,0,2,28,2007),
			'end_date' => mktime(17,00,0,2,28,2007),
			'location' => 'Mars'
		);

		// Set up the public frame
		$this->main_frame->SetTitle('Travis\' Ideas Page :)');
		$this->main_frame->SetContentSimple('test/travis', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}

?>