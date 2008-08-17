<div id="RightColumn">
	<h2 class="first">Quick Links</h2>
	<div class="Entry">
		<ul>
			<li><a href="/office/charity/editarticle/<?php echo($charity['id']); ?>">Article</a></li>
			<li><a href="/office/charity/editreports/<?php echo($charity['id']); ?>">Progress Reports</a></li>
		</ul>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>charity info</h2>
		<p>
			<?php
			echo('			<b>Name: </b>'.xml_escape($charity['name']).'<br />'."\n");
			echo('			<b>Goal Total: </b>'.xml_escape($charity['target']).'<br />'."\n");
			echo('			<a href="/office/charity/modify/'.$charity['id'].'">[Modify]</a>'."\n");
			?>
		</p>
	</div>
</div>
