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

	function charts ()
	{
		if (!CheckPermissions('office')) return;
		if ((GetUserLevel() != 'editor') && (GetUserLevel() != 'admin')) {
			$this->main_frame->AddMessage('error', 'ACCESS DENIED');
			redirect('/office/liveblog');
		}

		$sql = 'SELECT id, role, (SELECT COUNT(*) FROM elections_candidates WHERE elections_candidates.position_id = elections_positions.id) AS candidate_count FROM elections_positions ORDER BY ordering ASC';
		$data = array();
		$data['positions'] = $this->db->query($sql)->result();

		$this->main_frame->SetContentSimple('office/liveblog/charts-elections-positions', $data);
		$this->main_frame->Load();
	}

	function charts2 ($position = NULL)
	{
		if (!CheckPermissions('office')) return;
		if ((GetUserLevel() != 'editor') && (GetUserLevel() != 'admin')) {
			$this->main_frame->AddMessage('error', 'ACCESS DENIED');
			redirect('/office/liveblog');
		}
		if (empty($position)) {
			show_404();
			return;
		}

		// Elections Live Blog Article
		$article_id = 2858;

		// Get Data
		$data = array();
		$boxes = array('a','d','g','h','j','l','v','w','nc','votes');
		$colleges = array('a','d','g','h','j','l','v','w','nc');
		$collnames = array('Alcuin','Derwent','Goodricke','Halifax','James','Langwith','Vanbrugh','Wentworth','No College');

		$sql = 'SELECT id, role, count FROM elections_positions WHERE id = ?';
		$data['position'] = $this->db->query($sql, array($position))->row();

		$sql = 'SELECT id, name FROM elections_candidates WHERE position_id = ? ORDER BY ordering ASC';
		$data['candidates'] = $this->db->query($sql, array($position))->result_array();

		foreach ($data['candidates'] as &$candidate) {
			$sql = 'SELECT * FROM elections_rounds WHERE candidate_id = ? ORDER BY round_id ASC';
			$query = $this->db->query($sql, array($candidate['id']));
			$candidate['rounds'] = array();
			foreach ($query->result_array() as $round) {
				$candidate['rounds'][$round['round_id']] = $round;
			}
		}

		$data['chart1'] = array();
		$data['chart2'] = array();
		if (empty($_POST['chart1-height'])) $_POST['chart1-height'] = 130;
		if (empty($_POST['chart2-height'])) $_POST['chart2-height'] = 200;

		// Draw a chart for each round
		for ($r = count($data['candidates'][0]['rounds']); $r > 0; $r--) {
			$chart_people = array();
			$chart_values = array();
			$chart_maxvalue = 0;

			foreach ($data['candidates'] as $c) {
				$chart_people[] = $c['name'];
				if (!empty($c['rounds'][$r]['votes'])) {
					$chart_values[] = $c['rounds'][$r]['votes'];
				} else {
					$temp = 0;
					foreach ($colleges as $college) {
						$temp += $c['rounds'][$r][$college];
					}
					$chart_values[] = $temp;
				}
				if ($chart_values[count($chart_values) - 1] > $chart_maxvalue) {
					$chart_maxvalue = $chart_values[count($chart_values) - 1];
				}
			}
			$chart_title = $data['position']->role;
			if (count($data['candidates'][0]['rounds']) > 1) {
				$chart_title .= ' - Round ' . $r;
			}
			$data['chart1'][] = $this->_drawChart($chart_title, $data['position']->count, $chart_people, $chart_values, $chart_maxvalue);
		}

		//$this->_drawChart2($chart_title, $collnames, $chart_values2, $chart_maxvalue);

		// Carry out operations as required
		if (!empty($_POST['set-data'])) {
			foreach ($data['candidates'] as $c) {
				foreach ($c['rounds'] as $r) {
					$params = array();
					foreach ($boxes as $b) {
						$input_name = 'c-' . $c['id'] . '-' . $r['round_id'] . '-' . $b;
						$params[] = (!empty($_POST[$input_name])) ? $_POST[$input_name] : '0';
					}
					$params[] = $r['round_id'];
					$params[] = $c['id'];
					$sql = 'UPDATE elections_rounds SET a=?, d=?, g=?, h=?, j=?, l=?, v=?, w=?, nc=?, votes=? WHERE round_id = ? AND candidate_id = ?';
					$query = $this->db->query($sql, $params);
				}
				$new_round = false;
				$new_round_number = count($c['rounds']) + 1;
				$params = array();
				foreach ($boxes as $b) {
					$input_name = 'c-' . $c['id'] . '-' . $new_round_number . '-' . $b;
					if (isset($_POST[$input_name]) && $_POST[$input_name] != '') {
						$params[] = $_POST[$input_name];
						$new_round = true;
					} else {
						$params[] = '0';
					}
				}
				$params[] = $new_round_number;
				$params[] = $c['id'];
				if ($new_round) {
					$sql = 'INSERT INTO elections_rounds SET a=?, d=?, g=?, h=?, j=?, l=?, v=?, w=?, nc=?, votes=?, round_id=?, candidate_id=?';
					$query = $this->db->query($sql, $params);
				}
			}
			redirect('/office/liveblog/charts2/' . $position);
		} elseif (!empty($_POST['insert-chart1'])) {
			$this->load->model('roses_model');

			$blog_entry = '-- CHART --';
			$blog_entry_cache = '';
			foreach ($data['chart1'] as $chart) {
				$blog_entry_cache .= '<div style="text-align:center"><img src="' . $chart . '" alt="Election Results Chart" /></div>';
			}

			$this->roses_model->addBlogEntry($article_id, $blog_entry, $blog_entry_cache, $this->user_auth->entityId);
			$this->_updateArticle($article_id, $this->user_auth->entityId);

			$this->main_frame->AddMessage('success', 'Chart inserted successfully.');
			redirect('/office/liveblog/charts2/' . $position);
		}


		$this->main_frame->SetContentSimple('office/liveblog/charts-elections-results', $data);
		$this->main_frame->Load();
	}

	function _drawChart($title = '', $position_count = 1, $people = array(), $values = array(), $max = 100)
	{
		$o = 'http://chart.apis.google.com/chart?chs=360x' . $_POST['chart1-height'];
		$o .= '&cht=bhs';
		$o .= '&chxt=y,x';
		$o .= '&chxs=0,000000,11,1,lt,000000|1,000000,11,0,t,000000';
		$o .= '&chxtc=1,0';
		$o .= '&chts=000000,14';
		$o .= '&chxp=1,50';
		$o .= '&chco=20c1f0';
		$o .= '&chm=N+*,000000,0,-1,11,1';

		$title = str_replace(' ', '+', $title);
		$quota = round(array_sum($values) / ($position_count + 1), 1);
		$candidates = implode('|', array_reverse($people));

		$o .= '&chtt=' . $title;
		$o .= '&chxl=0:|' . $candidates . '|1:|Vote+Quota+=+' . $quota . '|';
		$o .= '&chd=t:' . implode(',', $values);

		if ($quota > $max) $max = $quota;
		$max += 50;
		//$o .= '&chds=0,' . array_sum($values);
		$o .= '&chds=0,' . $max;
		$quota_line = round(100 * ($quota / $max), 1);
		$o .= '&chg=200,200,4,4,' . $quota_line . ',-1';


		$tmp = '';
		$bar_height = round(1 / count($values), 3);
		$new_values = array_reverse($values);
		$win_quota = $quota;
		$ordered_values = sort($values);
		if (!empty($ordered_values[$position_count - 1]) && $ordered_values[$position_count - 1] > $win_quota) {
			$win_quota = $ordered_values[$position_count - 1];
		}
		foreach ($new_values as $v) {
			$tmp .= ',' . ($v >= $win_quota ? 'dddddd' : 'ffffff') . ',' . $bar_height;
		}
		$o .= '&chf=c,ls,90' . $tmp;

		return $o;
	}

	// $values[$candidate][$college]
	function _drawChart2($title = '', $people = array(), $values = array(), $max = 100)
	{
		$o = 'http://chart.apis.google.com/chart?chs=360x' . $_POST['chart2-height'];
		$o .= '&cht=bhs';
		$o .= '&chxt=y,x';
		$o .= '&chxs=0,000000,11,1,lt,000000|1,000000,11,0,t,000000';
		$o .= '&chxtc=1,0';
		$o .= '&chts=000000,14';
		$o .= '&chxp=1,50';
		$o .= '&chco=ff6a00|20c1f0|999999|000000|cccccc';

		$labels = array();
		$p_count = 0;
		foreach ($people as $p) {
			$labels[] = 'N+*,000000,' . $p_count . ',-1,11,1';
			$p_count++;
		}
		$o .= '&chm=' . implode('|', $labels);

		$title = str_replace(' ', '+', $title);
		$candidates = implode('|', array_reverse($people));

		$o .= '&chtt=' . $title;
		$o .= '&chxl=0:|' . $candidates . '|1:|Votes per candidate|';
		foreach ($values as &$v) {
			$v = implode(',', $v);
		}
		$o .= '&chd=t:' . implode('|', $values);

		$o .= '&chds=0,' . $max;

		return $o;
	}

	function admin ($article_id = 1682, $entry_id = NULL)
	{
		if (!CheckPermissions('office')) return;
		if ((GetUserLevel() == 'editor') || (GetUserLevel() == 'admin')) {
			$this->load->model('roses_model');
			$this->load->model('photos_model');
			$this->load->library('image');
			$this->load->library('wikiparser');
			// Set winning team indicators
			$this->wikiparser->add_image_override(-1, '<img src="/images/version2/rose_lancashire.png" alt="Lancaster" />', 'Lancaster Win');
			$this->wikiparser->add_image_override(-2, '<img src="/images/version2/rose_yorkshire.png" alt="York Win" />', 'York Win');
			$this->wikiparser->add_image_override(-3, '<img src="/images/version2/rose_draw.png" alt="Draw" />', 'Draw');
			$photo_requests = $this->photos_model->GetPhotoRequestsForArticle($article_id);
			foreach ($photo_requests as $photo) {
				$this->wikiparser->add_image_override($photo['photo_number'], $this->image->getThumb($photo['photo_id'], $photo['photo_codename'], true), $photo['photo_caption']);
			}
			$data = array();

			if ($this->roses_model->isLiveBlog($article_id)) {
				if ((is_numeric($entry_id)) && (!empty($_POST['edit' . $entry_id]))) {
					$blog_entry = $_POST['entry' . $entry_id];
					$blog_entry_cache = $this->wikiparser->parse($blog_entry);
					$this->roses_model->updateBlogEntry($entry_id, $blog_entry, $blog_entry_cache, $this->user_auth->entityId);
					$this->_updateArticle($article_id, $this->user_auth->entityId);
					$this->main_frame->AddMessage('success', 'Blog entry was successfully edited.');
					redirect('/office/liveblog/admin/' . $article_id);
				}
				if ((is_numeric($entry_id)) && (!empty($_POST['delete' . $entry_id]))) {
					$this->roses_model->deleteBlogEntry($entry_id);
					$this->_updateArticle($article_id, $this->user_auth->entityId);
					$this->main_frame->AddMessage('success', 'Blog entry was successfully deleted!');
					redirect('/office/liveblog/admin/' . $article_id);
				}
				if ((is_numeric($article_id)) && (!empty($_POST['postnew']))) {
					$blog_entry = "'''" . date('H:i') . "''' " . $_POST['postcontent'];
					$twitter_update = $_POST['postcontent'];
					$blog_entry_cache = $this->wikiparser->parse($blog_entry);
					$this->roses_model->addBlogEntry($article_id, $blog_entry, $blog_entry_cache, $this->user_auth->entityId);
					$this->_updateArticle($article_id, $this->user_auth->entityId);
					if (!empty($_POST['posttwitter'])) {
						// Post to public Twitter feed
						$TwitterFeed = new TwitterXML($this->config->item('twitter_feed_userid'), $this->config->item('twitter_feed_passwd'));
						$TwitterFeed->updateStatus($twitter_update);
					}
					$this->main_frame->AddMessage('success', 'New Blog entry added.');
					redirect('/office/liveblog/admin/' . $article_id);
				}

				$data['article_id'] = $article_id;
				$data['content'] = $this->roses_model->getLiveBlog($article_id);

				// Set up the content
				$this->main_frame->SetContentSimple('office/liveblog/admin', $data);
				$this->main_frame->Load();
			} else {
				$this->main_frame->AddMessage('error', 'Requested article ID is not setup for Live Blogging.');
				redirect('/office/liveblog');
			}
		} else {
			$this->main_frame->AddMessage('error', 'ACCESS DENIED');
			redirect('/office/liveblog');
		}
	}

	function scores ()
	{
		if (!CheckPermissions('office')) return;

		$this->load->library('wikiparser');
		$this->load->model('roses_model');

		// Format: d ykr score <event_id> <lancaster_score> <york_score>
		// <*_score> is numerical if score known or w/d/l if not
		$valid = array(
			'w' => -1,
			'd' => -2,
			'l' => -3
		);

		$data = array();
		$data['allevents'] = $this->roses_model->getAllResults();
		$data['valid'] = array_flip($valid);

		if (!empty($_POST['updatescore'])) {

			// Roses 2009 article id's
			$article_id = $this->_whichArticle();
	
			$event_id = $_POST['updatescore'];
			$lscore = $_POST['lscore' . $_POST['updatescore']];
			$yscore = $_POST['yscore' . $_POST['updatescore']];
			$event = $this->roses_model->getResult($event_id);
	
			// Store result in database
			$score_lancs = (isset($valid[$lscore])) ? $valid[$lscore] : $lscore;
			$score_york = (isset($valid[$yscore])) ? $valid[$yscore] : $yscore;
			$this->roses_model->setResult($event->event_id, $score_lancs, $score_york, mktime());
	
			// Set winning team indicators
			$this->wikiparser->add_image_override(-1, '<img src="/images/version2/rose_lancashire.png" alt="Lancaster Win" />', 'Lancaster Win');
			$this->wikiparser->add_image_override(-2, '<img src="/images/version2/rose_yorkshire.png" alt="York Win" />', 'York Win');
			$this->wikiparser->add_image_override(-3, '<img src="/images/version2/rose_draw.png" alt="Draw" />', 'Draw');
	
			// Add posted time to blog entry
			if ($event->event_score_time === NULL) {
				$blog_entry = "'''" . date('H:i') . "''' ";
			} else {
				$blog_entry = "'''" . date('H:i', $event->event_score_time) . "''' ";
			}
			$twitter_update = '';
			// Add winning team indicator
			if ($score_lancs > $score_york) {
				$blog_entry .= "[[image:-1|inline|Lancaster Win]] '''Lancaster Win!'''";
				$twitter_update .= 'Lancaster Win!';
			} elseif ($score_lancs < $score_york) {
				$blog_entry .= "[[image:-2|inline|York Win]] '''York Win!'''";
				$twitter_update .= 'York Win!';
			} elseif ($score_lancs == $score_york) {
				$blog_entry .= "[[image:-3|inline|Draw]] '''Draw!'''";
				$twitter_update .= 'Draw!';
			}
			// Add sport and match name
			$blog_entry .= ' ' . $event->event_sport . ' ' . $event->event_name;
			$twitter_update .= ' ' . $event->event_sport . ' ' . $event->event_name;
			// Add score if available
			if (($score_lancs >= 0) || ($score_york >= 0)) {
				$blog_entry .= " '''" . $score_york . '-' . $score_lancs . "'''";
				$twitter_update .= ' ' . $score_york . '-' . $score_lancs;
			}
			$blog_entry_cache = $this->wikiparser->parse($blog_entry);
			// Either add new blog entry or update existing
			if ($event->event_blog_entry_id === NULL) {
				$new_entry = $this->roses_model->addBlogEntry($article_id, $blog_entry, $blog_entry_cache, $this->user_auth->entityId);
				$this->roses_model->noteScoreBlogEntry($event->event_id, $new_entry);
			} else {
				$this->roses_model->updateBlogEntry($event->event_blog_entry_id, $blog_entry, $blog_entry_cache, $this->user_auth->entityId);
			}
			// Update live blog article
			$update = $this->_updateArticle($article_id, $this->user_auth->entityId);
			if ($update !== TRUE) {
				$error = $update;
			}
			if (!empty($twitter_update)) {
				$score = $this->_getScore();
				// Post to public Twitter feed
				$TwitterFeed = new TwitterXML($this->config->item('twitter_feed_userid'), $this->config->item('twitter_feed_passwd'));
				$TwitterFeed->updateStatus('#Roses2009 YORK ' . $score['york'] . ' LANCASTER ' . $score['lancs'] . ' -' . $twitter_update);
			}
			//$this->_updateResults ($user->user_entity_id);
			redirect('/office/liveblog/scores');
		}

		$this->main_frame->SetContentSimple('office/liveblog/scores', $data);
		$this->main_frame->Load();
	}

	function update ()
	{
		$this->load->library('wikiparser');
		$this->load->model('roses_model');

		$TwitterCP = new TwitterXML($this->config->item('twitter_admin_userid'), $this->config->item('twitter_admin_passwd'));
		$msgs = $TwitterCP->getDirectMessages();
		if ($msgs === FALSE) {
			$error = 'Unable to retrieve direct messages: ' . $TwitterCP->getError();
		} else {
			$msgs = $msgs->direct_message;
			$msg_count = count($msgs);
			// @TODO: Cope with more than 20 new messages
			// Process msgs from oldest to newest
			for ($i = $msg_count - 1; $i >= 0; $i--) {
				// @TODO: Also check they have live blogging permission
				$user = $this->twitter_model->checkTwitterAccess($msgs[$i]->sender_id);
				if (!$user) {
					$TwitterCP->sendDirectMessage($msgs[$i]->sender_id, 'ERROR: Unauthorised User!');
					$error = 'Unauthorised User! ' . $msgs[$i]->sender_id;
				} else {
					echo('msgid: ' . $msgs[$i]->id . '<br />');
					echo('date: ' . $msgs[$i]->created_at . '<br />');
					echo('txt: ' . $msgs[$i]->text . '<br />');
					echo('sender: ' . $msgs[$i]->sender_id . '<hr />');
					$msg_date = strtotime($msgs[$i]->created_at);
					$cmds = explode(' ', $msgs[$i]->text);
					switch ($cmds[0]) {
						case 'score':
							// Format: d ykr score <event_id> <lancaster_score> <york_score>
							// <*_score> is numerical if score known or w/d/l if not
							$valid = array(
								'w' => -1,
								'd' => -2,
								'l' => -3
							);
							// Roses 2008 article id's
							$article_id = $this->_whichArticle();
							if ((count($cmds) == 4) && (is_numeric($cmds[1])) && ((is_numeric($cmds[2])) || (isset($valid[$cmds[2]]))) && ((is_numeric($cmds[3])) || (isset($valid[$cmds[3]])))) {
								$event = $this->roses_model->getResult($cmds[1]);
								if ($event !== NULL) {
									// Store result in database
									$score_lancs = (isset($valid[$cmds[2]])) ? $valid[$cmds[2]] : $cmds[2];
									$score_york = (isset($valid[$cmds[3]])) ? $valid[$cmds[3]] : $cmds[3];
									$this->roses_model->setResult($event->event_id, $score_lancs, $score_york, date('Y-m-d H:i:s'));
									// Set winning team indicators
									$this->wikiparser->add_image_override(-1, '<img src="/images/version2/rose_lancashire.png" alt="Lancaster" />', 'Lancaster Win');
									$this->wikiparser->add_image_override(-2, '<img src="/images/version2/rose_yorkshire.png" alt="York Win" />', 'York Win');
									$this->wikiparser->add_image_override(-3, '<img src="/images/version2/rose_draw.png" alt="Draw" />', 'Draw');
									// Add posted time to blog entry
									if ($event->event_score_time === NULL) {
										$blog_entry = "'''" . date('H:i') . "''' ";
									} else {
										$blog_entry = "'''" . date('H:i', $event->event_score_time) . "''' ";
									}
									$twitter_update = '';
									// Add winning team indicator
									if ($score_lancs > $score_york) {
										$blog_entry .= "[[image:-1|inline|Lancaster Win]] '''Lancaster Win!'''";
										$twitter_update .= 'Lancaster Win!';
									} elseif ($score_lancs < $score_york) {
										$blog_entry .= "[[image:-2|inline|York Win]] '''York Win!'''";
										$twitter_update .= 'York Win!';
									} elseif ($score_lancs == $score_york) {
										$blog_entry .= "[[image:-3|inline|Draw]] '''Draw!'''";
										$twitter_update .= 'Draw!';
									}
									// Add sport and match name
									$blog_entry .= ' ' . $event->event_sport . ' ' . $event->event_name;
									$twitter_update .= ' ' . $event->event_sport . ' ' . $event->event_name;
									// Add score if available
									if (($score_lancs >= 0) || ($score_york >= 0)) {
										$blog_entry .= " '''" . $score_lancs . '-' . $score_york . "'''";
										$twitter_update .= ' ' . $score_lancs . '-' . $score_york;
									}
									$blog_entry_cache = $this->wikiparser->parse($blog_entry);
									// Either add new blog entry or update existing
									if ($event->event_blog_entry_id === NULL) {
										$new_entry = $this->roses_model->addBlogEntry($article_id, $blog_entry, $blog_entry_cache, $user->user_entity_id);
										$this->roses_model->noteScoreBlogEntry($event->event_id, $new_entry);
									} else {
										$this->roses_model->updateBlogEntry($event->event_blog_entry_id, $blog_entry, $blog_entry_cache, $user->user_entity_id);
									}
									// Update live blog article
									$update = $this->_updateArticle($article_id, $user->user_entity_id);
									if ($update !== TRUE) {
										$error = $update;
									}
									$this->_updateResults ($user->user_entity_id);
								} else {
									$TwitterCP->sendDirectMessage($msgs[$i]->sender_id, 'ERROR: Unknown event ID!');
								}
							} else {
								$TwitterCP->sendDirectMessage($msgs[$i]->sender_id, 'ERROR: Incorrect message format: d ' . $this->config->item('twitter_admin_userid') . ' score <event_id> <lancs_score> <york_score>');
							}
							break;
						default:
							if (is_numeric($cmds[0])) {
								// Process live blog update
								$article_id = $cmds[0];
								unset($cmds[0]);
								if ($this->roses_model->isLiveBlog($article_id)) {
									$blog_entry = "'''" . date('H:i') . "''' " . implode(' ', $cmds);
									$twitter_update = implode(' ', $cmds);
									$blog_entry_cache = $this->wikiparser->parse($blog_entry);
									$this->roses_model->addBlogEntry($article_id, $blog_entry, $blog_entry_cache, $user->user_entity_id);
									// Update live blog article
									$update = $this->_updateArticle($article_id, $user->user_entity_id);
									if ($update !== TRUE) {
										$error = $update;
									}
									//$send_msg = $TwitterCP->sendDirectMessage($msgs[$i]->sender_id, 'Successfully updated article: \'' . $rev_data['headline'] . '\'');
								} else {
									$error = 'ERROR: Article ' . $article_id . ' isn\'t a live blog!';
								}
							} else {
								$TwitterCP->sendDirectMessage($msgs[$i]->sender_id, 'ERROR: Unknown Command!');
							}
					}
				}
				// Delete message after processing
				if ($TwitterCP->deleteDirectMessage($msgs[$i]->id) === FALSE) {
					$error = 'Unable to delete message: ' . $TwitterCP->getError();
				}
			}
		}
		if (isset($error)) {
			$this->load->helper('yorkermail_helper');
		//	yorkermail($this->config->item('webmaster_email_address'), '[TheYorker] - Twitter API Error Message', $error, $this->config->item('no_reply_email_address'));
		}
		if (isset($twitter_update)) {
			// Post to public Twitter feed
			$TwitterFeed = new TwitterXML($this->config->item('twitter_feed_userid'), $this->config->item('twitter_feed_passwd'));
			$TwitterFeed->updateStatus($twitter_update);
		}
	}

	function _whichArticle ()
	{
		$article_id = 1682;											// Test article id
		if (mktime() >= mktime(8,0,0,5,8,2009)) $article_id = 2965;	// Friday
		if (mktime() >= mktime(8,0,0,5,9,2009)) $article_id = 2971;	// Saturday
		if (mktime() >= mktime(8,0,0,5,10,2009)) $article_id = 2972;	// Sunday
		return $article_id;
	}

	function _updateArticle ($article_id, $user_id)
	{
		$this->load->model('roses_model');
		if ($this->roses_model->isLiveBlog($article_id)) {
			// Get all live blog content
			$content = $this->roses_model->getLiveBlog($article_id);
			// Find article to edit
			$this->load->model('article_model');
			$this->load->model('requests_model');
			$revision = $this->article_model->GetLatestRevision($article_id);
			$rev_data = $this->article_model->GetRevisionData($revision);
			//$revision = $this->article_model->CreateNewRevision($article_id, $user_id, $rev_data['headline'], $rev_data['subheadline'], $rev_data['subtext'], $rev_data['blurb'], $content['all']['wikitext'], $content['all']['cache']);
			$content['all']['wikitext'] = '!!! DO NOT EDIT THIS ARTICLE, THIS IS A LIVE BLOG, SPEAK TO webmaster@theyorker.co.uk INSTEAD !!!';
			$revision = $this->article_model->CreateNewRevision($article_id, $user_id, $rev_data['headline'], $rev_data['subheadline'], $rev_data['subtext'], $rev_data['blurb'], $content['all']['wikitext'], $content['all']['cache']);
			$publish_date = $this->roses_model->getPublishDate($article_id);
			$this->requests_model->PublishArticle($article_id,$revision,$publish_date);
		} else {
			return 'ERROR: Article ' . $article_id . ' isn\'t a live blog!';
		}
		return TRUE;
	}

	function _getScore ()
	{
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

		$data['york'] = $score_york;
		$data['lancs'] = $score_lancs;
		return $data;
	}

	function _updateResults ($user_id)
	{
		$total_york = 0;
		$total_lancs = 0;
		$article_id = 1693;
		$this->load->model('roses_model');
		$this->load->library('wikiparser');
		// Set winning team indicators
		$this->wikiparser->add_image_override(-1, '<img src="/images/version2/rose_lancashire.png" alt="Lancaster" />', 'Lancaster Win');
		$this->wikiparser->add_image_override(-2, '<img src="/images/version2/rose_yorkshire.png" alt="York Win" />', 'York Win');
		$this->wikiparser->add_image_override(-3, '<img src="/images/version2/rose_draw.png" alt="Draw" />', 'Draw');
		$result = $this->roses_model->getAllResults();
		$content = '';
		foreach ($result as $r) {
			$content .= date('D H:i', $r['event_time']) . ' ' . $r['event_sport'] . ' ' . $r['event_name'] . ' @ ' . $r['event_venue'];
			if ($r['event_score_time'] !== NULL) {
				$points_lancs = 0;
				$points_york = 0;
				if ($r['event_lancaster_score'] > $r['event_york_score']) {
					$content .= "{{br}}[[image:-1|inline|Lancaster Win]] '''Lancaster Win!''' ";
					if ($r['event_points'] > 0) $points_lancs = $r['event_points'];

				} elseif ($r['event_lancaster_score'] < $r['event_york_score']) {
					$content .= "{{br}}[[image:-2|inline|York Win]] '''York Win!''' ";
					if ($r['event_points'] > 0) $points_york = $r['event_points'];
				} elseif ($r['event_lancaster_score'] == $r['event_york_score']) {
					$content .= "{{br}}[[image:-3|inline|Draw]] '''Draw!''' ";
					if ($r['event_points'] > 0) {
						$points_lancs = $r['event_points'] / 2;
						$points_york = $r['event_points'] / 2;
					}
				}
				if (($r['event_lancaster_score'] >= 0) || ($r['event_york_score'] >= 0)) {
					$content .= 'Score: ' . $r['event_lancaster_score'] . '-' . $r['event_york_score'] . ' ';
				}
				if ($r['event_points'] > 0) {
					$content .= 'Points: ' . $points_lancs . '-' . $points_york;
					$total_york = $total_york + $points_york;
					$total_lancs = $total_lancs + $points_lancs;
				}
			}
			$content .= "\n\n";
		}
		$intro = 'Lancaster ' . $total_lancs . ' - ' . $total_york . ' York';
		$cache = $this->wikiparser->parse($content);
		// Find article to edit
		$this->load->model('article_model');
		$this->load->model('requests_model');
		$revision = $this->article_model->GetLatestRevision($article_id);
		$rev_data = $this->article_model->GetRevisionData($revision);
		$revision = $this->article_model->CreateNewRevision($article_id, $user_id, $rev_data['headline'], $rev_data['subheadline'], $intro, $rev_data['blurb'], $content, $cache);
		$publish_date = $this->roses_model->getPublishDate($article_id);
		$this->requests_model->PublishArticle($article_id,$revision,$publish_date);
	}

}
?>
