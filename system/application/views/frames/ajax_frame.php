<?php
/**
 * @author James Hogan <james_hogan@theyorker.co.uk>
 * @brief XML AJAX output class
 * @param xmlContent
 * @param errors[['code'=>,'class'=>,'text'=>,'html'=>,'retry'=>Bool]] Error messages.
 */
header('content-type: text/xml');
$this->load->helper('xml');
?><<?php ?>?xml version="1.0" encoding="UTF-8"?>
<theyorker><?php echo("\n");
	?><errors><?php echo("\n");
	foreach ($errors as $error) {
		echo('<error');
		if (isset($error['class'])) {
			echo(' class="'.xml_escape($error['class']).'"');
		}
		if (isset($error['code'])) {
			echo(' code="'.xml_escape($error['code']).'"');
		}
		if (isset($error['retry'])) {
			echo(' retry="'.($error['retry']?'1':'0').'"');
		}
		echo('>');
		if (isset($error['text'])) {
			echo(	'<text>'.xml_escape($error['text']).'</text>');
		}
		if (isset($error['html'])) {
			echo(	'<html>'.xml_escape($error['html']).'</html>');
		}
		echo('</error>'."\n");
	}
	?></errors><?php echo("\n");
    write_xml($xmlContent, $xmlContent['_tag']);
?></theyorker>
