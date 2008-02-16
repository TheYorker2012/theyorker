<div id="RightColumn">
	<h2 class="first">Quick Links</h2>
	<div class="Entry">
		<a href="/">Back To Home</a>
	</div>
</div>

<div id="MainColumn">
	<div id="HomeBanner">
		<?php 
		$this->homepage_boxes->print_homepage_banner($banner);
		?>
	</div>
	<div class="BlueBox">
		<h2><?php echo(xml_escape($sections['no_charity']['title'])); ?></h2>
		<div class="Entry">
			<?php echo($sections['no_charity']['text']); ?>
		</div>
	</div>
</div>
