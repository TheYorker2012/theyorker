<div class="BlueBox">
	<h2>frequently asked questions</h2>
	<ul>
<?php
foreach ($faq as $key => $faq_entry) {
	echo('		<li><a href="#faqn'.$key.'">'.xml_escape($faq_entry['question']).'</a></li>'."\n");
}
?>
	</ul>
<?php
foreach ($faq as $key => $faq_entry) {
	echo('	<h3 id="faqn'.$key.'">'.xml_escape($faq_entry['question']).'</h3>'."\n");
	/// @todo FIXME should be called answer_xml
	echo('	'.$faq_entry['answer']."\n");
}
?>
</div>
