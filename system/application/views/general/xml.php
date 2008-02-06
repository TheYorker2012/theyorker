<?php
/**
 * @file views/general/xml.php
 * @brief Generic XML view.
 * @author James Hogan (jh559)
 *
 * @param $RootTag The root tag information.
 */
header('content-type: text/xml');

function write_xml($subtags, $label = NULL)
{
	if (NULL !== $label) {
		$attributes = '';
		if (is_array($subtags) && isset($subtags['_attr']) && is_array($subtags['_attr'])) {
			foreach ($subtags['_attr'] as $attribute => $value) {
				$attributes .= " $attribute=\"".xml_escape($value).'"';
			}
		}
		echo('<'.$label.$attributes.'>');
	}
	if (is_array($subtags)) {
		foreach ($subtags as $tag => $content) {
			if (substr($tag, 0, 1) == '_') {
				continue;
			}
			if (is_numeric($tag)) {
				if (is_array($content) && isset($content['_tag'])) {
					$tag = $content['_tag'];
				} else {
					$tag = NULL;
				}
			}
			write_xml($content, $tag);
		}
	} else {
		echo(xml_escape($subtags));
	}
	if (NULL !== $label) {
		echo('</'.$label.'>');
	}
}

?><<?php ?>?xml version="1.0" encoding="UTF-8"?><?php
?><?php write_xml($RootTag, $RootTag['_tag']); ?>