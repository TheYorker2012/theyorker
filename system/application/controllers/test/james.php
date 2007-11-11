<?php

/// James' Test Controller
class James extends controller
{
	/// Generate random sequences of alphanumeric characters.
	function random()
	{
		if (!CheckPermissions('admin')) return;
		
		$bulk = '
		<FORM CLASS="form" METHOD="POST" ACTION="/test/james/random">
		<FIELDSET>
		<label for="length">Length:</label><input value="8" name="length" /><br />
		<label for="quantity">Quantity:</label><input value="8" name="quantity" /><br />
		<input type="submit" CLASS="button" name="submitter" value="Generate"><br />
		</FIELDSET>
		</FORM>
		';
		
		$length = $this->input->post('length');
		$quantity = $this->input->post('quantity');
		if (is_numeric($length) && is_numeric($quantity)) {
			$length = (int)$length;
			$quantity = (int)$quantity;
			if ($quantity > 100) {
				$quantity = 100;
			}
			$this->load->helper('string');
			$bulk = '';
			for ($i = 0; $i < $quantity; ++$i) {
				$gen = random_string('alnum', $length);
				$bulk .= '<p><b>'.$gen.'</b></p>';
			}
		}
		
		$this->main_frame->SetContent(new SimpleView($bulk));
		$this->main_frame->SetTitle('Random generator');
		$this->main_frame->Load();
	}
	
	function test()
	{
		if (!CheckPermissions('admin')) return;
		
		// Load libraries
		$this->load->library('academic_calendar');
		$this->load->library('calendar_backend');
		$this->load->library('calendar_frontend');
		
		$this->load->library('calendar_source_yorker');
		$this->load->library('calendar_view_days');
		
		// Set up data sources
		$data = new CalendarData();
		$sources = array();
		$sources[0] = new CalendarSourceYorker();
		$sources[0]->SetRange(strtotime('-2month'), strtotime('1month'));
		
		// Accumulate data from sources in $data
		foreach ($sources as $source) {
			try {
				$source->FetchEvents($data);
			} catch (Exception $e) {
				$this->messages->AddMessage('error', 'calendar data source failed: '.$e->getMessage());
			}
		}
		
		// Display data
		
		$days = new CalendarViewDays();
		$days->SetCalendarData($data);
		
		$this->main_frame->SetContent($days);
		
		// Load view
		$this->main_frame->Load();
	}
	
	function wikify()
	{
		$wikitext = $this->input->post('CommentAddContent');
		if ($wikitext !== FALSE) {
			$this->load->model('comments_model');
			$xhtml = $this->comments_model->ParseCommentWikitext($wikitext);
			$this->load->view('test/echo', array('content' => $xhtml));
		}
	}
	
	function addthreads($place)
	{
		if (!CheckPermissions('admin')) return;
		$this->load->model('comments_model');
		if ($place == 'articles') {
			$result = $this->comments_model->CreateThreads(
				array(
					'allow_ratings' => FALSE,
					'allow_comments' => TRUE,
					'allow_anonymous_comments' => TRUE,
				),
				'articles',
				'article_id',
				'article_public_comment_thread_id'
			);
			$this->messages->AddDumpMessage('public result', $result);
			$result = $this->comments_model->CreateThreads(
				array(
					'allow_ratings' => TRUE,
					'allow_comments' => TRUE,
					'allow_anonymous_comments' => FALSE,
				),
				'articles',
				'article_id',
				'article_private_comment_thread_id'
			);
			$this->messages->AddDumpMessage('private result', $result);
			
		} elseif ($place == 'review_contexts') {
			$result = $this->comments_model->CreateThreads(
				array(
					'allow_ratings' => TRUE,
					'allow_comments' => TRUE,
					'allow_anonymous_comments' => TRUE,
				),
				'review_contexts',
				array('review_context_organisation_entity_id', 'review_context_content_type_id'),
				'review_context_comment_thread_id'
			);
			$this->messages->AddDumpMessage('review_contexts', $result);
		}
		$this->main_frame->Load();
	}
}

?>