<?php

/**
 * @file views/calendar/ical.php
 * @brief iCalendar file format view
 * @author James Hogan (jh559@cs.york.ac.uk)
 */

function PrintContentLine($ContentLine)
{
	$len = strlen($ContentLine);
	while ($len > 75) {
		echo(substr($ContentLine, 0, 75)."\r\n");
		$ContentLine = ' '.substr($ContentLine, 75);
		$len -= 74;
	}
	echo($ContentLine."\r\n");
}

function PrintContentLines()
{
	foreach (func_get_args() as $line) {
		PrintContentLine($line);
	}
}

PrintContentLines(
	'BEGIN:VCALENDAR',
	'PRODID:-//Google Inc//Google Calendar 70.9054//EN',
	'VERSION:2.0',
	'CALSCALE:GREGORIAN',
	'METHOD:PUBLISH',
	'X-WR-CALNAME:Christian Holidays',
	'X-WR-TIMEZONE:Etc/GMT',
	'X-WR-RELCALID:""',
	'BEGIN:VTIMEZONE',
	'TZID:America/Los_Angeles',
	'X-LIC-LOCATION:America/Los_Angeles',
	'BEGIN:STANDARD',
	'TZOFFSETFROM:-0700',
	'TZOFFSETTO:-0800',
	'TZNAME:PST',
	'DTSTART:19701025T020000',
	'RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU',
	'END:STANDARD',
	'BEGIN:DAYLIGHT',
	'TZOFFSETFROM:-0800',
	'TZOFFSETTO:-0700',
	'TZNAME:PDT',
	'DTSTART:19700405T020000',
	'RRULE:FREQ=YEARLY;BYMONTH=4;BYDAY=1SU',
	'END:DAYLIGHT',
	'END:VTIMEZONE',
	'BEGIN:VEVENT',
	'DTSTART;VALUE=DATE:20051031',
	'DTEND;VALUE=DATE:20051101',
	'RRULE:FREQ=YEARLY;INTERVAL=1',
	'DTSTAMP:20070413T152609Z',
	'ORGANIZER;CN=Christian', 'Holidays:MAILTO:christian__en_gb@holiday.calendar.google.com',
	'UID:76ljhgfebvut399qngg2lv0u5k_cdk74qbjehkm2ri0d1nmoqb4c5sisor1dhimsp31e8n6errfctm6abj3dtmg@google.com',
	'CLASS:PUBLIC',
	'CREATED:20060915T033458Z',
	'DESCRIPTION:something really really very rather long which will wrap hopefully several times, ill make it go onto multiple lines just to check that they all line up\, itll be amazing dude, hehe im running out of crap to say\, oh well that ought to do the trick',
	'LAST-MODIFIED:20060917T015706Z',
	'LOCATION:',
	'SEQUENCE:0',
	'STATUS:CONFIRMED',
	'SUMMARY:Reformation Day',
	'TRANSP:OPAQUE',
	'END:VEVENT',
	'END:VCALENDAR'
);

?>