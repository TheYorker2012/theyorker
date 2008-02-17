<?php
/**
 * @file views/calendar/simple_recur_xml.php
 * @author James Hogan (jh559)
 * @param $Errors  Error messages.
 * @param $Start   Start time.
 * @param $End     End time.
 * @param $Results Results structure.
 */
header('content-type: text/xml');
?><<?php ?>?xml version="1.0" encoding="UTF-8"?><?php
?><recur_validation valid="1"><?php
	foreach ($Errors as $error) {
		echo('<error field="'.$error['field'].'">'.xml_escape($error['text']).'</error>'."");
	}
	$start_Ymd = date('Ymd', $Start);
	foreach ($Results as $date => $recurrences) {
		foreach ($recurrences as $time => $duration) {
			$classes = 'exi sel';
			if ($date == $start_Ymd) {
				$classes .= ' sta';
			}
			echo("<occ date=\"$date\" time=\"$time\" dur=\"$duration\" class=\"$classes\" />");
		}
	}
?></recur_validation>