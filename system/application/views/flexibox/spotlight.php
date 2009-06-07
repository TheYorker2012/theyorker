<div class="FlexiBox Box23">
	<div id="DisplayBox">
		<div id="DisplayBoxBg">
			<?php echo(xml_escape($articles[0]['headline'])); ?>
		</div>
		<div id="DisplayBoxText">
			<a href="/news/<?php echo(xml_escape($articles[0]['id'])); ?>">
				<?php echo(xml_escape($articles[0]['headline'])); ?>
			</a>
		</div>
		<a href="/news/<?php echo(xml_escape($articles[0]['id'])); ?>">
			<img src="/photos/home/<?php echo(xml_escape($articles[0]['photo_id'])); ?>" alt="<?php echo(xml_escape($articles[0]['photo_title'])); ?>" />
		</a>
	</div>
</div>