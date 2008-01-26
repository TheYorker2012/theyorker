<?php

/// Office calendar controller
class Calendar extends controller
{
	protected $mActions;
	
	function __construct()
	{
		parent::controller();
	}
	
	function _remap()
	{
		if (!CheckPermissions('vip+pr')) return;
		$this->load->model('subcontrollers/calendar_subcontroller');
		$this->calendar_subcontroller->_SetDefault('view');
		$this->calendar_subcontroller->_SetPermission('vip+pr');
		$this->calendar_subcontroller->_AddPermission('create', 'edit');
		$this->calendar_subcontroller->SetIndexPageCode('viparea_calendar_index');
		$this->calendar_subcontroller->SetRangePageCode('viparea_calendar');
		
		$sources = & $this->calendar_subcontroller->GetSources();
		$sources->DisableGroup('subscribed');
		$sources->EnableGroup('owned');
		$sources->EnableGroup('private');
		$sources->EnableGroup('active');
		$sources->DisableGroup('inactive');
		$sources->EnableGroup('show');
		
		$this->calendar_subcontroller->_map(func_get_args());
	}
	
	function action()
	{
		if (!CheckPermissions('vip+pr')) return;
		
		// do the magic, use calendar_actions as a controller
		$this->load->model('calendar/organiser_actions');
		$args = func_get_args();
		$func = array_shift($args);
		if ('_' !== substr($func,0,1) && method_exists($this->organiser_actions, $func)) {
			call_user_func_array(array(&$this->organiser_actions, $func), $args);
		} else {
			show_404();
		}
	}
	
	function publish($EventId = NULL)
	{
		if (!CheckPermissions('vip+pr')) return;
		
		if (is_numeric($EventId)) {
			$EventId = (int)$EventId;
			
			// Get the specific event
			$this->load->library('calendar_backend');
			$this->load->library('calendar_source_yorker');
			$source_yorker = new CalendarSourceYorker(0);
			$calendar_data = new CalendarData();
			$source_yorker->FetchEvent($calendar_data, $EventId);
			$events = $calendar_data->GetEvents();
			if (array_key_exists(0, $events)) {
				$event = $events[0];
				if ($this->input->post('evpub_cancel')) {
					// REDIRECT
					$this->messages->AddMessage('information','Event publication was cancelled.');
					$this->load->helper('uri_tail');
					RedirectUriTail(3);
					
				} elseif ($this->input->post('evpub_confirm')) {
					// PUBLISH
					$result = $this->events_model->OccurrenceDraftPublish($EventId, FALSE);
					if ($result > 0) {
						$this->messages->AddMessage('success',$result.' occurrences were altered');
					} else {
						print_r($this->db->last_query());
						exit;
						$this->messages->AddMessage('error','No occurrences were altered');
					}
					$this->load->helper('uri_tail');
					RedirectUriTail(3);
					
				} else {
					$data = array(
						'Event' => $event,
					);
					
					$this->main_frame->SetContentSimple('calendar/publish', $data);
					
					$this->main_frame->Load();
				}
			} else {
				$this->messages->AddMessage('error', 'The event coud not be found');
				$this->load->helper('uri_tail');
				RedirectUriTail(3);
			}
		} else {
			show_404();
		}
	}
	
	function occop($Operation, $EventId, $OccurrenceId)
	{
		if (!is_numeric($EventId)) {
			show_404();
		}
		if (FALSE!==$OccurrenceId && !is_numeric($OccurrenceId)) {
			show_404();
		}
		
		if (!CheckPermissions('vip+pr')) return;
		
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
		redirect(vip_url('calendar/events/'.$EventId));
	}
	
	function events($EventId = FALSE, $OccurrenceId=FALSE)
	{
		show_404();
		if (FALSE!==$EventId && !is_numeric($EventId)) {
			show_404();
		}
		if (FALSE!==$OccurrenceId && !is_numeric($OccurrenceId)) {
			show_404();
		}
		
		if (!CheckPermissions('vip+pr')) return;
		
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
					/// @todo Update for new recurrence system
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
			if (FALSE !== $results) {
				foreach ($results as $result) {
					if (!array_key_exists($result['event_id'], $events)) {
						$events[$result['event_id']] = $result;
					}
				}
			}
			
			$op = '<OL>';
			foreach ($events as $event) {
				if (array_key_exists('event_recurrence_rule',$event)
					&& NULL != $event['event_recurrence_rule']) {
					$event['description'] = $event['event_recurrence_rule']->ToString() .
						' - ' . $event['description'];
				}
				$op .= '<LI><A HREF="'.vip_url('calendar/events/'.$event['event_id']).'">'.
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
				$filter->SetSpecialCondition($special_condition);
				
				$fields = array(
						'occurrence_id' => 'event_occurrences.event_occurrence_id',
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
						$links[] = '<A HREF="' . vip_url('calendar/occop/'.$operation.'/' . $EventId . '/' . $occurrence['occurrence_id']) .
							'">'.$operation.'</A>';
					}
					$op .= '<LI>'.$occurrence['status'].' <A HREF="' . vip_url('calendar/events/' . $EventId . '/' . $occurrence['occurrence_id']) . '">' .
						$occurrence['start'] . ' -> ' . $occurrence['end'] . '</A> '.
						' ('.implode(', ',$links).') </LI>';
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
					'start' => 'event_occurrences.event_occurrence_start_time',
					'end' => 'event_occurrences.event_occurrence_end_time',
					'active_id' => 'event_occurrences.event_occurrence_active_occurrence_id',
					'rescheduled_start' => 'active_occurrence.event_occurrence_start_time',
					'rescheduled_end' => 'active_occurrence.event_occurrence_end_time',
					'status'=>$filter->ExpressionPublicState(),
					'cancelled'=>$filter->ExpressionPublicCancelled(),
					'postponed'=>$filter->ExpressionPublicPostponed(),
					'rescheduled'=>$filter->ExpressionPublicRescheduled(),
					'ts' => 'event_occurrences.event_occurrence_last_modified',
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
}

?>