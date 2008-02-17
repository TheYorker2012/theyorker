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

	function images()
	{
		if (!CheckPermissions('office')) return;

		$data['images'][] = array(
			'url'		=>	'CompSci.jpg',
			'style'		=>	'0',
			'position'	=>	'right'
		);
		$data['images'][] = array(
			'url'		=>	'CompSci.jpg',
			'style'		=>	'1',
			'position'	=>	'right'
		);
		$data['images'][] = array(
			'url'		=>	'CompSci.jpg',
			'style'		=>	'0',
			'position'	=>	'bottom'
		);
		$data['images'][] = array(
			'url'		=>	'CompSci.jpg',
			'style'		=>	'1',
			'position'	=>	'bottom'
		);
		$data['images'][] = array(
			'url'		=>	'70.jpg',
			'style'		=>	'0',
			'position'	=>	'right'
		);
		$data['images'][] = array(
			'url'		=>	'70.jpg',
			'style'		=>	'1',
			'position'	=>	'right'
		);
		$data['images'][] = array(
			'url'		=>	'70.jpg',
			'style'		=>	'0',
			'position'	=>	'bottom'
		);
		$data['images'][] = array(
			'url'		=>	'70.jpg',
			'style'		=>	'1',
			'position'	=>	'bottom'
		);

		// Set up the public frame
		$this->main_frame->SetTitle('Travis\' Ideas Page :)');
		$this->main_frame->SetContentSimple('test/travis-images', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	function image_test($img_path = 'CompSci.jpg', $type = 0, $position = 'right', $caption = 'Chris Travis &copy; 2007')
	{
		$img_path = realpath('.') . '/images/prototype/news//' . $img_path;
		$img = @imagecreatefromjpeg($img_path);
		if ($img) {
			$img_size = getimagesize($img_path);

			if ($type == 0) {
				$black = imagecolorallocate($img, 0, 0, 0);
				$water_mark_bg = imagecolorallocatealpha($img, 255, 255, 255, 65);
			} else {
				$black = imagecolorallocate($img, 255, 255, 255);
				$water_mark_bg = imagecolorallocatealpha($img, 0, 0, 0, 65);
			}

			putenv('GDFONTPATH=' . realpath('.').'/images');
			$font = 'arial';

			if ($position == 'bottom') {
				imagefilledrectangle($img, 0, $img_size[1] - 12, $img_size[0], $img_size[1], $water_mark_bg);
				imagettftext($img, 8, 0, 5, $img_size[1] - 2, $black, $font, html_entity_decode($caption));
			} else {
				imagefilledrectangle($img, $img_size[0] - 12, 0, $img_size[0], $img_size[1], $water_mark_bg);
				imagettftext($img, 8, 90, $img_size[0] - 2, $img_size[1] - 5, $black, $font, html_entity_decode($caption));
			}

		} else {
			$img = imagecreatetruecolor(150, 30);	/* Create a black image */
			$bgc = imagecolorallocate($img, 255, 255, 255);
			$tc  = imagecolorallocate($img, 0, 0, 0);
	        imagefilledrectangle($img, 0, 0, 150, 30, $bgc);
			/* Output an errmsg */
			imagestring($img, 1, 5, 5, "Error loading $img_path", $tc);
		}
		header('Content-type: image/png');
		imagepng($img);
		imagedestroy($img);
	}

	function HttpRequest( $url, $method = 'GET', $data = NULL, $additional_headers = NULL, $followRedirects = true )
	{
	    # in compliance with the RFC 2616 post data will not redirected
	    $method = strtoupper($method);
	    $url_parsed = @parse_url($url);
	    if (!@$url_parsed['scheme']) $url_parsed = @parse_url('http://'.$url);
	    extract($url_parsed);
	    if(!is_array($data))
	    {
	        $data = NULL;
	    }
	    else
	    {
	        $ampersand = '';
	        $temp = NULL;
	        foreach($data as $k => $v)
	        {
	            $temp .= $ampersand.urlencode($k).'='.urlencode($v);
	            $ampersand = '&';
	        }
	        $data = $temp;
	    }
	    if(!@$port) $port = 80;
	    if(!@$path) $path = '/';
	    if(($method == 'GET') and ($data)) $query = (@$query)?'&'.$data:'?'.$data;
	    if(@$query) $path .= '?'.$query;
	    $out = "$method $path HTTP/1.0\r\n";
	    $out .= "Host: $host\r\n";
	    if($method == 'POST')
	    {
	        $out .= "Content-type: application/x-www-form-urlencoded\r\n";
	        $out .= "Content-length: " . @strlen($data) . "\r\n";
	    }
	    $out .= (@$additional_headers)?$additional_headers:'';
	    $out .= "Connection: Close\r\n\r\n";
	    if($method == 'POST') $out .= $data."\r\n";
	    if(!$fp = @fsockopen($host, $port, $es, $en, 5)){
	       return false;
	   }
	   fwrite($fp, $out);
		$foundBody = false;
		$header = '';
		$body = '';
	    while (!feof($fp)) {
	        $s = fgets($fp, 128);
	        echo $s;
	        if ( $s == "\r\n" ) {
	            $foundBody = true;
	            continue;
	        }
	        if ( $foundBody ) {
	            $body .= $s;
	        } else {
	            
	            //echo $s;
	            
	            if(($method != 'POST') and ($followRedirects) and (preg_match('/^Location:(.*)/i', $s, $matches) != false) )
	            {
	                fclose($fp);
	                return HttpRequest( trim($matches[1]) );
	            }
	            $header .= $s;
	            if(preg_match('@HTTP[/]1[.][01x][\s]{1,}([1-5][01][0-9])[\s].*$@', $s, $matches))
	            {
	                $status = trim($matches[1]);
	            }
	        }
	    }
	    fclose($fp);
	    return array('head' => trim($header), 'body' => trim($body), 'status' => $status);
	}

	function feedback()
	{
		if (!CheckPermissions('office')) return;

		$data['test'] = '';
		
		if ($this->input->post('c_add') == 'Add Feedback') {
			/*
			var_dump(http_post_data('http://theyorker2.gmghosting.com/trac/newticket',
									urlencode(
										'__FORM_TOKEN=d24d3199042da4f56f979'.
										'&type=feedback'.
										'&action=create'.
										'&status=new'.
										'&priority=none'.
										'&owner='.
										'&summary='.$this->input->post('c_title').
										'&description='.$this->input->post('c_desc').
										'&milestone=1.0 Beta2'.
										'&component=General'.
										'&version=Beta'
									)
									,
									array('httpauth'	=>	'cdt502:29fish'),
									$info
                     				)
					);
			var_dump($info);
			*/
			$post_data = array(
				'__FORM_TOKEN'	=>	'd24d3199042da4f56f979',
				'type'			=>	'feedback',
				'action'		=>	'create',
				'status'		=>	'new',
				'priority'		=>	'none',
				'owner'			=>	'',
				'summary'		=>	$this->input->post('c_title'),
				'description'	=>	$this->input->post('c_desc'),
				'milestone'		=>	'1.0 Beta2',
				'component'		=>	'General',
				'version'		=>	'Beta'
			);
			var_dump($this->HttpRequest('http://theyorker2.gmghosting.com/trac/newticket', 'GET', NULL, 'Authorization: BASIC Y2R0NTAyOjI5ZmlzaA=='));

		}

		// Set up the public frame
		$this->main_frame->SetTitle('Feedback => Trac Importer');
		$this->main_frame->SetContentSimple('test/travis-feedback', $data);
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

	function select()
	{
		if (!CheckPermissions('office')) return;

		$data['test'] = '';

		// Set up the public frame
		$this->main_frame->SetTitle('Assignee Selector');
		$this->main_frame->SetContentSimple('test/travis-selector', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	function contenttypes()
	{
		if (!CheckPermissions('office')) return;

		$sql = 'SELECT content_type_id AS id, content_type_name AS name
				FROM content_types
				WHERE content_type_has_children = 1
				ORDER BY content_type_name ASC';
		$query = $this->db->query($sql);
		$data['parents'] = array();
		foreach ($query->result() as $row) {
			$data['parents'][] = $row;
		}

		// Set up the public frame
		$this->main_frame->SetTitle('Content Types Adder');
		$this->main_frame->SetContentSimple('test/travis-contenttypes', $data);
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	function addtype()
	{
		if (!CheckPermissions('office')) return;
		if ($this->input->post('c_add') !== FALSE) {
			$sql = 'INSERT INTO organisations
				SET organisation_organisation_type_id = ?,
					organisation_parent_organisation_entity_id = ?,
					organisation_name = ?,
					organisation_directory_entry_name = ?,
					organisation_show_in_directory = ?,
					organisation_reviewed = ?,
					organisation_pr_suggestion = ?';
			$query = $this->db->query($sql,array(5,453,$this->input->post('c_name'),$this->input->post('c_dname'),0,1,0));
			
			$org_id = $this->db->insert_id();
			$sql = 'INSERT INTO entities
				SET entity_id = ?, entity_username = ?';
			$query = $this->db->query($sql,array($org_id,$this->input->post('c_dname')));

			$sql = 'INSERT INTO content_types
				SET content_type_codename = ?,
					content_type_related_organisation_entity_id = ?,
					content_type_parent_content_type_id = ?,
					content_type_name = ?,
					content_type_archive = ?,
					content_type_blurb = ?,
					content_type_has_reviews = ?,
					content_type_has_children = ?,
					content_type_section = ?';
			$query = $this->db->query($sql,array($this->input->post('c_codename'),
															$org_id,
															$this->input->post('c_parent'),
															$this->input->post('c_name'),
															1,
															$this->input->post('c_blurb'),
															0,
															$this->input->post('c_children'),
															$this->input->post('c_section')
															)
												);
			$this->main_frame->AddMessage('success','New content type added');
			redirect('/test/travis/contenttypes');
		}

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