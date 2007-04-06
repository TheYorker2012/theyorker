<?php
// Parsed wikitext
echo $parsed_wikitext.'<br/>';
?>

<HR>
<H3>Edit Wikitext:</H3>
<?php
// Wikitext input box
echo form_open('/admin/tools/wikitext');

$textarea_data = array(
		'name'        => 'wikitext',
		'id'          => 'wikitext',
		'value'       => $wikitext,
		'rows'        => '10',
		'cols'        => '80',
		'style'       => 'width:100%',
	);
echo form_textarea($textarea_data) . '<br/>';

echo form_submit('submit', 'Preview') . '<br/>';

echo form_close('') . '<br/>';
?>

<HR>
<H3>HTML Output:</H3>
<?php
// HTML output box
$textarea_data = array(
		'name'        => 'html',
		'id'          => 'html',
		'value'       => $parsed_wikitext,
		'rows'        => '10',
		'cols'        => '80',
		'style'       => 'width:100%',
	);
echo form_textarea($textarea_data) . '<br/>';

?>