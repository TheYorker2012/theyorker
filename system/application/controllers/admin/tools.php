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
		static $valid_tools = array('reset','triggers');
		if (in_array($Tool, $valid_tools)) {
			$function_name = '_database_'.$Tool;
			$this->$function_name($Param1);
			$this->main_frame->load();
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
}

?>