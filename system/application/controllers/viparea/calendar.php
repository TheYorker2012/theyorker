<?php

class Calendar extends controller
{
	function __construct()
	{
		parent::controller();
	}
	
	function index()
	{
		if (!CheckPermissions('vip')) return;
		
		$this->pages_model->SetPageCode('viparea_calendar');
		
		$this->main_frame->SetContentSimple('viparea/calendar');
		
		// Load the main frame
		$this->main_frame->Load();
	}
	
	function events($EventId = FALSE, $OccurrenceId=FALSE)
	{
		if (FALSE!==$EventId && !is_numeric($EventId)) {
			show_404();
		}
		if (FALSE!==$OccurrenceId && !is_numeric($OccurrenceId)) {
			show_404();
		}
		
		if (!CheckPermissions('vip')) return;
		
		$this->load->model('calendar/events_model');
		$this->load->helper('text');
		
		if (FALSE === $EventId) {
			$fields = array(
					'event_id' => 'events.event_id',
					'name' => 'events.event_name',
					'description' => 'events.event_description',
				);
			
			$filter = new EventOccurrenceFilter();
			$filter->DisableSource('subscribed');
			$filter->SetRange(strtotime('-1year'),strtotime('+1year'));
			
			$results = $filter->GenerateOccurrences($fields);
			
			$events = array();
			foreach ($results as $result) {
				if (!array_key_exists($result['event_id'], $events)) {
					$events[$result['event_id']] = $result;
				}
			}
			
			$op = '<OL>';
			foreach ($events as $event) {
				$op .= '<LI><A HREF="'.site_url('viparea/calendar/events/'.$event['event_id']).'">'.
					$event['name'].'</A> - '.$event['description'].'</LI>';
			}
			$op .= '</OL>';
			
			$this->main_frame->SetTitle('Events');
			$this->main_frame->SetContent(
				new SimpleView($op)
			);
			
		} else {
			
			$special_condition = 'events.event_id='.$EventId;
			if (FALSE === $OccurrenceId) {
				$fields = array(
						'occurrence_id' => 'event_occurrences.event_occurrence_id',
						'description' => 'event_occurrences.event_occurrence_description',
						'start' => 'event_occurrences.event_occurrence_start_time',
						'end' => 'event_occurrences.event_occurrence_end_time',
					);
				
				$filter = new EventOccurrenceFilter();
				$filter->DisableSource('subscribed');
				$filter->SetRange(strtotime('-1year'),strtotime('+1year'));
				$filter->SetSpecialCondition($special_condition);
				
				$results = $filter->GenerateOccurrences($fields);
				
				$occurrences = array();
				foreach ($results as $result) {
					if (!array_key_exists($result['occurrence_id'], $occurrences)) {
						$occurrences[$result['occurrence_id']] = $result;
					}
				}
				
				$op = '<OL>';
				foreach ($occurrences as $occurrence) {
					$op .= '<LI><A HREF="'.site_url('viparea/calendar/events/'.$EventId.'/'.$occurrence['occurrence_id']).'">'.$occurrence['start'].' -> '.$occurrence['end'].'</A> '.
						$occurrence['description'].' </LI>';
				}
				$op .= '</OL>';
				
				$rsvps = $this->events_model->GetEventRsvp($EventId);
				
				$op .=	'<pre>'.
							ascii_to_entities(var_export($rsvps,true)).
						'</pre>';
				
				$this->main_frame->SetTitle('Events');
				$this->main_frame->SetContent(
					new SimpleView($op)
				);
			} else {
				$special_condition .= ' AND event_occurrences.event_occurrence_id='.$OccurrenceId;
			
				$filter = new EventOccurrenceFilter();
				$filter->DisableSource('subscribed');
				$filter->SetRange(strtotime('-1year'),strtotime('+1year'));
				$filter->SetSpecialCondition($special_condition);
				
				$result = $filter->GenerateOccurrences(array('*'));
				
				$rsvps = $this->events_model->GetOccurrenceRsvp($OccurrenceId);
				
				$op =	'<pre>'.
							ascii_to_entities(var_export($result,true)).
						'</pre><pre>'.
							ascii_to_entities(var_export($rsvps,true)).
						'</pre>';
				$this->main_frame->SetContent(
					new SimpleView($op)
				);
			}
		}
		
		// Load the main frame
		$this->main_frame->Load();
	}
	
	function view($DateRange='')
	{
		if (!CheckPermissions('vip')) return;
		
		$this->load->library('date_uri');
		$this->load->library('academic_calendar');
		
		$this->pages_model->SetPageCode('calendar_personal');
		if (!empty($DateRange)) {
			// $DateRange Not empty
			
			// Read the date, only allowing a single date (no range data)
			$uri_result = $this->date_uri->ReadUri($DateRange, FALSE);
			if ($uri_result['valid']) {
				// $DateRange Valid
				$start_time = $uri_result['start'];
				$start_time = $start_time->BackToMonday();
				$format = $uri_result['format']; // Use the format in all links
				$days = 7; // force 7 days until view can handle different values.
				
				/*$this->_ShowCalendar(
						$start_time, $days,
						'/calendar/week/', $format
					);*/
				//return;
				
			} else {
				// $DateRange Invalid
				$this->messages->AddMessage('error','Unrecognised date: "'.$DateRange.'"');
			}
		}
		
		// Default to this week
		$format = 'ac';
		$base_time = new Academic_time(time());
		
		$monday = $base_time->BackToMonday();
		
		
		// Load the public frame view
		$this->main_frame->Load();
	}
	
	function createevent()
	{
		if (!CheckPermissions('vip')) return;
		
		$this->pages_model->SetPageCode('viparea_calendar_createevent');
		
		$this->load->model('calendar/events_model');
		$this->load->helper('text');
		try {
			throw new Exception('Disabled hardcoded event adder');
			$event_data = array(
				'name' => 'Developer Meeting',
				'description' => 'Ready for the alpha launch?',
				'occurrences' => array(
					array(
						'description' => 'the only occurrence',
						'location' => 'tba',
						'start' => mktime(14,30,0,2,14,2007),
						'end' => mktime(17,0,0,2,14,2007),
						'all_day' => FALSE,
						'ends_late' => FALSE,
					),
				),
			);
			$result = $this->events_model->EventCreate($event_data);
		} catch (Exception $msg) {
			$this->messages->AddMessage('error',$msg->getMessage());
			$result = FALSE;
		}
		
		$this->main_frame->SetContent(
			new SimpleView(str_replace("\n",'<br />'."\n",ascii_to_entities(var_export($result,true))))
		);
		
		// Load the main frame
		$this->main_frame->Load();
	}
}

?>