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
	
	function recur()
	{
		if (!CheckPermissions('admin')) return;
		
		$this->load->library('academic_calendar');
		$this->load->library('calendar_backend');
		$this->load->model('calendar/recurrence_model');
		$this->load->library('calendar_source_icalendar');
		
		$ical = new CalendarSourceICalendar(
'BEGIN:VCALENDAR
PRODID:-//K Desktop Environment//NONSGML KOrganizer 3.5.6//EN
VERSION:2.0
BEGIN:VEVENT
DTSTAMP:20070330T073440Z
ORGANIZER;CN=James Hogan:MAILTO:james@albanarts.com
CREATED:20070327T120943Z
UID:KOrganizer-175000820.541
SEQUENCE:9
LAST-MODIFIED:20070330T073424Z
SUMMARY:Freshers week
CLASS:PUBLIC
PRIORITY:5
RRULE:FREQ=WEEKLY;COUNT=4;INTERVAL=2;BYDAY=WE,FR
EXDATE;VALUE=DATE:20070330
DTSTART:20070328T110000Z
DTEND:20070328T131500Z
TRANSP:OPAQUE
END:VEVENT

END:VCALENDAR
');
		$this->messages->AddDumpMessage('messages', $ical->ClearMessages());
		
		$recur = $ical->ReadRecur(
			'FREQ=MONTHLY;INTERVAL=2;BYDAY=WE,-1FR'
		);
		if (NULL === $recur) {
			$this->messages->AddDumpMessage('messages', $ical->ClearMessages());
		} else {
			echo('<pre>'); print_r($recur); echo('</pre>');
			$now = time();
			var_dump($recur->GetOccurrences($now,$now,strtotime('+5year')));
		}
		
		$this->main_frame->Load();
	}
	
	function eventsearch($search = '')
	{
		if (!CheckPermissions('public')) return;
		
		if (array_key_exists('search', $_GET)) {
			redirect('test/james/eventsearch/'.urlencode($_GET['search']));
		}
		
		// Load the libraries
		$this->load->library('calendar_backend');
		$this->load->library('calendar_source_my_calendar');
		$this->load->library('facebook');
		
		$data = array(
			'target' => $this->uri->uri_string(),
			'search' => $search,
		);
		
		if (!empty($search)) {
			
			$source = new CalendarSourceMyCalendar();
			// Use the search phrase
			$source->SetSearchPhrase($search);
			// Set the groups to get events from
			/*
				groups:
				'owned'      => TRUE,
				'subscribed' => TRUE,
				'all'        => FALSE,
				
				'private'    => TRUE,
				'active'     => TRUE,
				'inactive'   => TRUE,
				
				'hide'       => FALSE,
				'show'       => TRUE,
				'rsvp'       => TRUE,
				
				'event'      => TRUE,
				'todo'       => FALSE,
			*/
			$source->EnableGroup('all');
			$source->DisableGroup('subscribed'); // (subset of all)
			$source->DisableGroup('owned');      // (subset of all)
			$source->EnableGroup('hide'); // Show events hidden by user
			$source->EnableGroup('todo'); // Enable todo items
			
			// Set the date range if applicable
			//$source->SetRange(strtotime('-1month'), NULL))
			//$source->SetTodoRange(strtotime('-1month'), NULL))
			
			// Get the actual events from the sources
			$calendar_data = new CalendarData();
			$source->FetchEvents($calendar_data);
			
			// Do whatever with the data
			// $calendar_data->GetEvents() are the events
			// theres a couple of others for occurrences + organisations
			$data['results'] = $calendar_data->GetEvents();
		}
		
		$this->main_frame->SetContentSimple('test/james-eventsearch',$data);
		$this->main_frame->Load();
	}
}

?>