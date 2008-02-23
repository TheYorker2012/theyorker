<?php
/**
 * @brief XML output from inline page edits.
 * @file views/admin/pages_inlineedit.php
 * @author James Hogan (jh559)
 * @param $Fail bool Permission failure.
 * @param $Saved bool Whether it was saved.
 * @param $Preview string XHTML preview.
 */
header('content-type: text/xml');
?><<?php ?>?xml version="1.0" encoding="UTF-8"?><?php
?><inline_page_edit permission="<?php echo($Fail ? '0' : '1'); ?>" saved="<?php echo($Saved ? '1' : '0'); ?>"><?php
if ($Preview !== NULL) {
	?><preview><?php
	echo(xml_escape($Preview));
	?></preview><?php
}
?></inline_page_edit>