<?php
/**
 * @file views/admin/tools/test/index.php
 * @brief Release tests index page.
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
?>
<div id="RightColumn">
	<div>
		<h2 class="first">what&#039;s this?</h2>
		<p>Release tests should be run before updating the live site</p>
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>release tests</h2>
		<ul>
			<li><a href="<?php echo(site_url('admin/tools/test/static')); ?>">Static Analyser</a></li>
		</ul>
	</div>
</div>
