<?php

/**
 * @file controllers/admin/tools.php
 * @brief Misc admin tools.
 */

/// Misc admin tools (usually performing actions on the system).
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @todo use pages from db.
 */
class Tools extends controller
{
	/// Main tools page
	function index()
	{
		if (!CheckPermissions('admin')) return;
		/// @todo implement index page.
		$this->messages->AddMessage('todo: implement index page');
		$this->main_frame->load();
	}
	
	/// Handle the database tools
	function database($Tool = 'index', $Param1 = NULL)
	{
		if (!CheckPermissions('admin')) return;
		static $valid_tools = array(
			'reset',
			'triggers',
			'unescape',
		);
		if (in_array($Tool, $valid_tools)) {
			$function_name = '_database_'.$Tool;
			$this->$function_name($Param1);
		} else {
			show_404();
		}
	}
	
	function _database_reset($what)
	{
		static $valid_tools = array('comments');
		if (in_array($what, $valid_tools)) {
			$function_name = '_database_reset_'.$what;
			$this->$function_name();
			$this->main_frame->load();
		} else {
			show_404();
		}
	}
	
	function _database_reset_comments()
	{
		static $thread_fields = array(
			'articles' => array(
				'keys' => array('article_id'),
				'fields' => array(
					'article_private_comment_thread_id' => array(
						'allow_anonymous_comments' => FALSE,
					),
					'article_public_comment_thread_id' => array(
					),
				),
			),
			'review_contexts' => array(
				'keys' => array(
					'review_context_organisation_entity_id',
					'review_context_content_type_id',
				),
				'fields' => array(
					'review_context_comment_thread_id' => array(
						'allow_ratings' => TRUE,
					),
					'review_context_office_comment_thread_id' => array(
						'allow_anonymous_comments' => FALSE,
					),
				),
			),
			'photo_requests' => array(
				'keys' => array('photo_request_id'),
				'fields' => array(
					'photo_request_comment_thread_id' => array(
						'allow_anonymous_comments' => FALSE,
					),
				),
			),
		);
		
		
		// start transaction
		$this->db->trans_start();
		// delete all references to comment threads
		foreach ($thread_fields as $table => $info) {
			$setter = array();
			foreach ($info['fields'] as $field => $param) {
				$setter[$field] = NULL;
			}
			$this->db->update($table, $setter, '1');
		}
		// delete all comments
		$this->db->delete('comments', '1');
		// delete all comment ratings
		$this->db->delete('comment_ratings', '1');
		// delete all comment threads
		$this->db->delete('comment_threads', '1');
		// end transaction
		$this->db->trans_complete();
		
		$this->load->model('comments_model');
		foreach ($thread_fields as $table => $info) {
			foreach ($info['fields'] as $field => $param) {
				$this->comments_model->CreateThreads($param, $table, $info['keys'], $field);
			}
		}
		
		$this->messages->AddMessage('success', 'Comment threads have been successfully reset.');
		redirect('admin');
	}
	
	function _database_triggers($what)
	{
		static $flush_triggers = array(
			//'Calendar' => array('events_model', '');
			'Comments' => array('comments_model', 'CreateTriggers'),
		);
		foreach ($flush_triggers as $section => $trigger) {
			$this->load->model($trigger[0]);
			$this->$trigger[0]->$trigger[1]();
			$this->messages->AddMessage('information', $section.': Triggers have been recreated');
		}
		/// @todo provide a choice of which ones to flush.
		
		$this->main_frame->Load();
	}
	
	/// Unescape various fields in the database
	function _database_unescape()
	{
		static $unescape_data = array(
			'quotes' => array(
				'keys' => array(
					'quote_id',
				),
				'cols' => array(
					'quote_text',
					'quote_author',
				),
			),
			'article_contents' => array(
				'keys' => array(
					'article_content_id',
				),
				'cols' => array(
					'article_content_heading',
					'article_content_subheading',
					'article_content_subtext',
					'article_content_blurb',
				),
			),
			'subscriptions' => array(
				'keys' => array(
					'subscription_organisation_entity_id',
					'subscription_user_entity_id',
				),
				'cols' => array(
					'subscription_user_position',
				),
			),
			'users' => array(
				'keys' => array(
					'user_entity_id',
				),
				'cols' => array(
					'user_contact_phone_number',
				),
			),
			'organisations' => array(
				'keys' => array(
					'organisation_entity_id',
				),
				'cols' => array(
					'organisation_name',
					'organisation_yorkipedia_entry',
				),
			),
			'organisation_contents' => array(
				'keys' => array(
					'organisation_content_id',
				),
				'cols' => array(
					'organisation_content_description',
					'organisation_content_postal_address',
					'organisation_content_postcode',
					'organisation_content_phone_external',
					'organisation_content_phone_internal',
					'organisation_content_fax_number',
					'organisation_content_email_address',
					'organisation_content_url',
					'organisation_content_opening_hours',
				),
			),
		);
		if (false !== $this->input->post('go_unescape')) {
			$make_changes = (false !== $this->input->post('confirm_changes'));
			echo('Starting unescaping of database fields<br />');
			
			foreach ($unescape_data as $table => $data) {
				list($keys, $cols) = array($data['keys'], $data['cols']);
				
				echo('Unescaping table `'.xml_escape($table).'`<br /><ul>');
				
				// Get the rows from the database first
				$this->db->select(array_merge($keys, $cols));
				$this->db->from($table);
				$query = $this->db->get();
				$result_data = array();
				foreach ($query->result_array() as $row) {
					$changes = array();
					$key_values = array();
					foreach ($keys as $key) {
						$key_values[$key] = $row[$key];
					}
					foreach ($cols as $col) {
						if (is_string($row[$col]) && $row[$col] != xml_unescape($row[$col])) {
							$need_updating = true;
							echo('<li>"'.xml_escape($row[$col]).'"<ul><li>"'.$row[$col].'"</li></ul></li>');
							$changes[$col] = xml_unescape($row[$col]);
						}
					}
					if (!empty($changes) && $make_changes) {
						$this->db->where($key_values);
						$this->db->update($table, $changes);
					}
				}
				
				echo('</ul>');
			}
			
			if ($make_changes) {
				echo('Unescaping completed (<a href="'.site_url('admin').'">Return to admin</a>)<br />');
			}
			else {
				echo('<form action="'.site_url($this->uri->uri_string()).'" method="post"><label><input type="checkbox" name="confirm_changes" />Confirm changes</label><br /><input type="submit" name="go_unescape" value="Unescape now" /></form>');
			}
		}
		else {
			$update_list = '<ul>';
			foreach ($unescape_data as $table => $data) {
				$update_list .= '<li>'.xml_escape('table `'.$table.'` (keys: `'.implode('`, `', $data['keys']).'`).');
				$update_list .= '<ul>';
				foreach ($data['cols'] as $col) {
					$update_list .= '<li>'.xml_escape('`'.$table.'`.`'.$col.'`').'</li>';
				}
				$update_list .= '</ul>';
				$update_list .= '</li>';
			}
			$update_list .= '</ul>';
			$this->messages->AddMessage('warning', '<p>To unescape the following fields in the database, make sure you are sure and know what you are doing first. The following fields will be unescaped:</p> '.$update_list.'<p>If you are sure click here:</p><form action="'.site_url($this->uri->uri_string()).'" method="post"><input type="submit" name="go_unescape" value="Next Stage" /></form>');
			$this->main_frame->Load();
		}
	}

	
	/// Handle the wiktiext tools
	function wikitext($Tool = 'test')
	{
		if (!CheckPermissions('admin')) return;
		static $valid_tools = array('flush','test');
		if (in_array($Tool, $valid_tools)) {
			$function_name = '_wikitext_'.$Tool;
			$this->$function_name();
			$this->main_frame->load();
		} else {
			show_404();
		}
	}
	
	/// Flush the wikitext caches of various bits of the database.
	protected function _wikitext_flush()
	{
		static $flush_sections = array(
			'Comments' => array('comments_model','FlushWikitextCache'),
		);
		foreach ($flush_sections as $section => $flusher) {
			$this->load->model($flusher[0]);
			$affected = $this->$flusher[0]->$flusher[1]();
			$this->messages->AddMessage('information', $section.': '.$affected.' records have been flushed');
		}
		/// @todo provide a choice of which ones to flush.
	}
	
	/// Wikitext tester.
	protected function _wikitext_test()
	{
		$this->load->helper('form');
		$this->load->library('wikiparser');
		
		
		$this->load->helper('wikitext_smiley');
		
		// No POST data? just set wikitext to default string
		$wikitext = $this->input->post('wikitext');
		if ($wikitext === FALSE) {
			$wikitext  = '==This is the yorker wikitext parser==' . "\n";
			$wikitext .= 'The [[admin/tools/wikitext/flush|flusher]] will allow you to flush the wikitext caches across the website.' . "\n";
			$wikitext .= '*This is an unordered list' . "\n";
			$wikitext .= '*#With an ordered list within' . "\n";
			$wikitext .= '*#And another item' . "\n";
			$wikitext .= "\n";
			$wikitext .= '#This is an ordered list' . "\n";
			$wikitext .= '#*With an unordered list within' . "\n";
			$wikitext .= '#*And another item' . "\n";
			$wikitext .= implode('',array_keys(_get_smiley_array())) . "\n";
		} else if (get_magic_quotes_gpc()) {
			$wikitext = stripslashes($wikitext);
		}
		
		$parsed_wikitext = $wikitext;
		/// @todo wiktiext tester enable/disable different passes + display mid sections
		$parsed_wikitext = wikitext_parse_smileys($parsed_wikitext);
		$parsed_wikitext = $this->wikiparser->parse($parsed_wikitext."\n",'wiki test');
		
		$data = array(
			'parsed_wikitext' => $parsed_wikitext,
			'wikitext' => $wikitext,
		);
		
		// Set up the public frame
		$this->main_frame->SetTitle('Wikitext Preview');
		$this->main_frame->SetContentSimple('admin/tools/wikitext', $data);
	}
	
	/// Perform tests on the branch.
	function test($Tool = 'index')
	{
		if (!CheckPermissions('admin')) return;
		
		static $valid_tools = array('index', 'static');
		if (in_array($Tool, $valid_tools)) {
			$function_name = '_test_'.$Tool;
			$args = func_get_args();
			array_shift($args);
			call_user_func_array(array(&$this, $function_name), $args);
		} else {
			show_404();
		}
	}
	
	/// Test index.
	function _test_index()
	{
		$data = array();
		$this->pages_model->SetPageCode('admin_tools_test_index');
		$this->main_frame->SetContentSimple('admin/tools/test/index', $data);
		$this->main_frame->load();
	}
	
	/// Static analysis tests.
	function _test_static($mode = null)
	{
		$analyser_program = '../tools/static_analysis/analyser.pl';
		$root_directory   = '..';
		if (null !== $mode) {
			if ('text' == $mode || 'ajax' == $mode) {
				$text = '';
				$tests = $_GET['tests'];
				if (false !== $tests) {
					if (preg_match('/^\w+(,\w+)*$/', $tests)) {
						$tests = explode(',', $tests);
						$command = $analyser_program.' '.$root_directory;
						foreach ($tests as $test) {
							$command .= ' -t '.$test;
						}
						$text = `$command 2>&1`;
					}
				}
				if ('text' == $mode) {
					$data = array(
						'Text' => '',
					);
					$main_view = new FramesView('general/text', $data);
				}
				else {
					$data = array(
						'RootTag' => array(
							'_tag' => 'result',
						),
					);
					foreach (explode("\n", $text) as $line) {
						$data['RootTag'][] = array(
							'_tag' => 'line',
							$line,
						);
					}
					$main_view = new FramesView('general/xml', $data);
				}
				$main_view->Load();
			}
			else {
				show_404();
			}
		}
		else {
			$data = array(
				'Tests' => array(),
				'TestSets' => array(),
			);
			
			$lines = `$analyser_program -m`;
			$lines = explode("\n", $lines);
			foreach ($lines as $line) {
				if (preg_match('/^\t(.*)\t(.*)$/', $line, $matches)) {
					$data['Tests'][$matches[1]] = $matches[2];
				}
			}
			
			$this->pages_model->SetPageCode('admin_tools_test_static');
			$this->main_frame->IncludeJs('javascript/admin/static_analysis.js');
			$this->main_frame->IncludeJs('javascript/simple_ajax.js');
			$this->main_frame->SetContentSimple('admin/tools/test/static', $data);
			$this->main_frame->load();
		}
	}
}

?>