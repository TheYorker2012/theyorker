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
	
	function occop($Operation, $EventId, $OccurrenceId)
	{
		if (!is_numeric($EventId)) {
			show_404();
		}
		if (FALSE!==$OccurrenceId && !is_numeric($OccurrenceId)) {
			show_404();
		}
		
		if (!CheckPermissions('vip')) return;
		
		$this->load->model('calendar/events_model');
		
		$valid = TRUE;
		switch ($Operation) {
			case 'trash':
				$model_function = 'OccurrenceDraftTrash';
				$success_message = 'Successfully trashed';
				$failure_message = 'Could not trash';
				break;
				
			case 'untrash':
				$model_function = 'OccurrenceTrashedRestore';
				$success_message = 'Successfully restored';
				$failure_message = 'Could not restore';
				break;
				
			case 'publish':
				$model_function = 'OccurrenceDraftPublish';
				$success_message = 'Successfully published';
				$failure_message = 'Could not publish';
				break;
				
			case 'cancel':
				$model_function = 'OccurrencePublishedCancel';
				$success_message = 'Successfully cancelled';
				$failure_message = 'Could not cancel';
				break;
				
			case 'uncancel':
				$model_function = 'OccurrenceCancelledRestore';
				$success_message = 'Successfully restored';
				$failure_message = 'Could not restore';
				break;
				
			case 'postpone':
				$model_function = 'OccurrencePostpone';
				$success_message = 'Successfully postponed';
				$failure_message = 'Could not postpone';
				break;
				
			case 'publishmove':
				$model_function = 'OccurrenceMovedraftPublish';
				$success_message = 'Successfully published movement';
				$failure_message = 'Could not publish movement';
				break;
				
			case 'restoremove':
				$model_function = 'OccurrenceMovedraftRestore';
				$success_message = 'Successfully restored movement';
				$failure_message = 'Could not restore movement';
				break;
				
			case 'cancelmove':
				$model_function = 'OccurrenceMovedraftCancel';
				$success_message = 'Successfully cancelled movement';
				$failure_message = 'Could not cancel movement';
				break;
				
			case 'delete':
				$model_function = 'OccurrenceDelete';
				$success_message = 'Successfully deleted';
				$failure_message = 'Could not delete';
				break;
				
			default:
				$this->messages->AddMessage('error','Unknown operation: '.$Operation);
				$valid = FALSE;
				break;
		};
		
		if ($valid) {
			$result = $this->events_model->$model_function($EventId, $OccurrenceId);
			
			if ($result) {
				$this->messages->AddMessage('success',$success_message);
			} else {
				$this->messages->AddMessage('error',$failure_message);
			}
		}
		redirect('viparea/calendar/events/'.$EventId);
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
		$this->load->model('calendar/recurrence_model');
		$this->load->helper('text');
		
		if (FALSE === $EventId) {
			// Quick thingy to add atandard english calendar rules to current
			// organisation. Note they aren't created with occurrences.
			if (FALSE) {
				$rules = $this->RuleCollectionStdEngland();
				foreach ($rules as $info) {
					$name = $info[0];
					$rule = $info[1];
					$rule_id = $this->recurrence_model->AddRule($rule);
					if (FALSE === $rule_id) {
						$this->messages->AddMessage('warning','RRule named '.$name.' could not be added');
					} else {
						$new_event = array(
							'name' => $name,
							'recurrence_rule_id' => $rule_id,
						);
						try {
							$result = $this->events_model->EventCreate($new_event);
							$this->messages->AddMessage('success','added '.$name);
						} catch (Exception $e) {
							$this->messages->AddMessage('error','while creating event '.$name.': '.$e->getMessage());
						}
					}
				}
			}
			
			$fields = array(
					'event_id' => 'events.event_id',
					'name' => 'events.event_name',
					'description' => 'events.event_description',
				);
			$results = $this->events_model->EventsGet($fields, FALSE, TRUE);
			
			$events = array();
			foreach ($results as $result) {
				if (!array_key_exists($result['event_id'], $events)) {
					$events[$result['event_id']] = $result;
				}
			}
			
			$op = '<OL>';
			foreach ($events as $event) {
				if (NULL != $event['event_recurrence_rule']) {
					$event['description'] = $event['event_recurrence_rule']->ToString() .
						' - ' . $event['description'];
				}
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
				$filter = new EventOccurrenceFilter();
				$filter->DisableSource('subscribed');
				$filter->SetRange(strtotime('-1year'),strtotime('+1year'));
				$filter->SetSpecialCondition($special_condition);
				
				$fields = array(
						'occurrence_id' => 'event_occurrences.event_occurrence_id',
						'description' => 'event_occurrences.event_occurrence_description',
						'start' => 'event_occurrences.event_occurrence_start_time',
						'end' => 'event_occurrences.event_occurrence_end_time',
						'status'=>$filter->ExpressionPublicState(),
					);
				
				$results = $filter->GenerateOccurrences($fields);
				
				$occurrences = array();
				foreach ($results as $result) {
					if (!array_key_exists($result['occurrence_id'], $occurrences)) {
						$occurrences[$result['occurrence_id']] = $result;
					}
				}
				
				$op = '<H4>Existing occurrences</H4>';
				$op .= '<OL>';
				foreach ($occurrences as $occurrence) {
					$operations = array();
					if ($occurrence['status'] === 'draft') {
						$operations[] = 'publish';
						$operations[] = 'trash';
					}
					if ($occurrence['status'] === 'movedraft') {
						$operations[] = 'publishmove';
						$operations[] = 'restoremove';
						$operations[] = 'cancelmove';
					}
					if ($occurrence['status'] === 'trashed') {
						$operations[] = 'untrash';
					}
					if ($occurrence['status'] === 'published') {
						$operations[] = 'cancel';
						$operations[] = 'postpone';
					}
					if ($occurrence['status'] === 'cancelled') {
						$operations[] = 'uncancel';
						$operations[] = 'postpone';
					}
					$links = array();
					foreach ($operations as $operation) {
						$links[] = '<A HREF="' . site_url('viparea/calendar/occop/'.$operation.'/' . $EventId . '/' . $occurrence['occurrence_id']) .
							'">'.$operation.'</A>';
					}
					$op .= '<LI>'.$occurrence['status'].' <A HREF="' . site_url('viparea/calendar/events/' . $EventId . '/' . $occurrence['occurrence_id']) . '">' .
						$occurrence['start'] . ' -> ' . $occurrence['end'] . '</A> '.
						$occurrence['description'].' ('.implode(', ',$links).') </LI>';
				}
				$op .= '</OL>';
				
				// Get event information
				$events = $this->events_model->EventsGet(array('events.*'), $EventId, TRUE);
				
				// If theres a recurrence rule, use it to generate the next two
				// years occurrences
				if (isset($events[0]['event_recurrence_rule'])) {
					$op .= '<H4>Generated occurrences in next 5 years</H4>';
					$op .= '<P><em>'.$events[0]['event_recurrence_rule']->ToString().'</em></P>';
					$occurrences_calculated = array_keys($events[0]['event_recurrence_rule']->FindTimes(
						time(), strtotime('+5years')));
					foreach ($occurrences_calculated as $key => $timestamp) {
						$occurrences_calculated[$key] = date(DATE_RFC822,$timestamp);
					}
					$op .=	'<pre>'.
								ascii_to_entities(var_export($occurrences_calculated,true)).
							'</pre>';
				}
				
				$op .= '<H4>Event information</H4>';
				$op .=	'<pre>'.
							ascii_to_entities(var_export($events,true)).
						'</pre>';
				
				$op .= '<H4>RSVP list</H4>';
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
				
				$result = $filter->GenerateOccurrences(array(
					'occurrence_id' => 'event_occurrences.event_occurrence_id',
					'description' => 'event_occurrences.event_occurrence_description',
					'start' => 'event_occurrences.event_occurrence_start_time',
					'end' => 'event_occurrences.event_occurrence_end_time',
					'active_id' => 'event_occurrences.event_occurrence_active_occurrence_id',
					'rescheduled_start' => 'active_occurrence.event_occurrence_start_time',
					'rescheduled_end' => 'active_occurrence.event_occurrence_end_time',
					'status'=>$filter->ExpressionPublicState(),
					'cancelled'=>$filter->ExpressionPublicCancelled(),
					'postponed'=>$filter->ExpressionPublicPostponed(),
					'rescheduled'=>$filter->ExpressionPublicRescheduled(),
					'ts' => 'event_occurrences.event_occurrence_timestamp',
					));
				
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