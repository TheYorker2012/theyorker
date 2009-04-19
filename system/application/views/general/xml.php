<?php
/**
 * @file views/general/xml.php
 * @brief Generic XML view.
 * @author James Hogan (jh559)
 *
 * @param $RootTag The root tag information.
 * @param $HeaderContentType null/string
 */
if (!isset($HeaderContentType) || null == $HeaderContentType) {
	$HeaderContentType = 'text/xml';
}
header("content-type: $HeaderContentType");
$this->load->helper('xml');

?><<?php ?>?xml version="1.0" encoding="UTF-8"?><?php
?><?php write_xml($RootTag, $RootTag['_tag']); ?>
