<div class="BlueBox">
	<h2>frequently asked questions</h2>
	<ul>
<?php
foreach ($faq as $key => $faq_entry) {
	echo('		<li><a href="#faqn'.$key.'">'.$faq_entry['question'].'</a></li>'."\n");
}
?>
	</ul>
<?php
foreach ($faq as $key => $faq_entry) {
	echo('	<h3 id="faqn'.$key.'">'.$faq_entry['question'].'</h3>'."\n");
	echo('	'.$faq_entry['answer']."\n");
}
?>
</div>
