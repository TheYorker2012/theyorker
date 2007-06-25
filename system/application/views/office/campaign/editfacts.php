<div class="RightToolbar">
	<h4>Quick Links</h4>
	<div class="Entry">
		a link
	</div>
</div>

<div class="MainToolbar">
	<div class="BlueBox">
		<h2>edit campaign's facts</h2>
		<textarea name="ted"><?php echo($article['fact_boxes'][0]['wikitext']); ?></textarea>
	</div>
<div>

<?php
echo('<pre><div class=BlueBox>');
print_r($data);
echo('</div></pre>');
?>