<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file libraries/Calendar_source_my_calendar.php
 * @brief Calendar source for Yorker events.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @pre loaded(library calendar_backend)
 * @pre loaded(library academic_calendar)
 *
 * Event source class for obtaining yorker events.
 *
 * @version 28-03-2007 James Hogan (jh559)
 *	- Created.
 */

/// Calendar source for yorker events.
class CalendarSourceMyCalendar extends CalendarSources
{
	/// Default constructor.
	function __construct($SourceId = -1)
	{
		parent::__construct();
		
		$this->SetSourceId($SourceId);
		
		$CI = & get_instance();
		$CI->load->model('calendar/events_model');
		
		$CI->db->from('event_sources');
		$CI->db->join('event_source_entities', 'event_source_entity_event_source_id = event_source_id', 'left');
		$CI->db->where('event_source_entity_entity_id = '.
			$CI->db->escape($CI->events_model->GetActiveEntityId()).
			' OR event_source_permanent = TRUE');
		$CI->db->select('event_source_id AS id,'.
						'event_source_protocol AS protocol,'.
						'event_source_url AS url');
		$query = $CI->db->get();
		$sources = $query->result_array();
		
		foreach ($sources as $source) {
			switch ($source['protocol']) {
				case 'yorkerdb':
					$CI->load->library('calendar_source_yorker');
					$this->AddSource(new CalendarSourceYorker($source['id']));
					break;
					
				case 'facebook':
					$CI->load->library('calendar_source_facebook');
					$this->AddSource(new CalendarSourceFacebook($source['id']));
					break;
					
				case 'ical':
					if (NULL !== $source['url']) {
						$data = file_get_contents(urlencode($source['url']));
						if (FALSE !== $data) {
							$CI->load->library('calendar_source_icalendar');
							$this->AddSource(new CalendarSourceICalendar($data, $source['id']));
						}
					}
					break;
			}
		}
	}
}

/// Dummy class
class Calendar_source_my_calendar
{
	/// Default constructor.
	function __construct()
	{
		$CI = & get_instance();
		$CI->load->model('calendar/recurrence_model');
	}
}

?>