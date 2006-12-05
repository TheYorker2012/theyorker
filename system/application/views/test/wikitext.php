<HTML>
 <HEAD>
  <TITLE>Wikitext preview</TITLE>
 </HEAD>
 <BODY>
<?php

echo $parsed_wikitext.'<br/>';

echo form_open('/test/wikitext');

$textarea_data = array(
		'name'        => 'wikitext',
		'id'          => 'wikitext',
		'value'       => $wikitext,
		'rows'        => '10',
		'cols'        => '80',
		'style'       => 'width:80%',
	);
echo form_textarea($textarea_data) . '<br/>';

echo form_submit('submit', 'Preview') . '<br/>';

echo form_close('') . '<br/>';

?>

 </BODY>
</HTML>
