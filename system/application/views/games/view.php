<div class='BlueBox'>
	<h2><?php echo(xml_escape($game['title'])); ?></h2>
	<?php $this->load->view('games/embed',$game); ?>
	<div style="text-align:center">
		<a href="/games/">
			Back to Game Zone
		</a>
	</div>
</div>
