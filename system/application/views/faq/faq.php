<?php
/*<div id='newsnav'>
	<ul id='newsnavlist'>
		<li><a href='/faq/' id='current'><img src='/images/prototype/news/uk.png' alt='FAQ' title='FAQ' /> FAQ</a></li>
	</ul>
</div>
*/
?>
<div class="padding">
	<h2>Frequently Asked Questions</h2>
	<ul>
	<?php
	foreach ($faq as $key => $faq_entry) {
		echo '<li><a href="#faqn'.$key.'">'.$faq_entry['question'].'</a></li>';
	}
	?>
	</ul>
	<?php
	foreach ($faq as $key => $faq_entry) {
		echo '<H3><a id="faqn'.$key.'" class="nolinkstyle">'.$faq_entry['question'].'</a></h3>';
		echo $faq_entry['answer'];
	}
	?>
</div>