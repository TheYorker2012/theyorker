	<script type='text/javascript' src='/javascript/prototype.js'></script>
	<script type='text/javascript' src='/javascript/scriptaculous.js'></script>
	<script type='text/javascript'>
	function showEntry (id) {
		if (document.getElementById('feedback' + id).style.display == 'none') {
			Effect.BlindDown('feedback' + id);
			document.getElementById('image' + id).src = '/images/prototype/prefs/toc_expanded.gif';
		} else {
			Effect.BlindUp('feedback' + id);
			document.getElementById('image' + id).src = '/images/prototype/prefs/toc_collapsed.gif';
		}
		return false;
	}
	</script>

	<?php foreach ($entries as $entry) { ?>
	<div id='container<?php echo $entry['id']; ?>' class='feedback'>
		<div class='top'>
			<span style='float:right; text-align: right;'>
				<?php echo $entry['time']; ?>
				<?php if ($entry['email'] != '') { ?>
					<br /><a href='mailto:<?php echo $entry['email']; ?>'><span class='orange'><?php echo $entry['email']; ?></span></a>
				<?php } ?>
			</span>
			<span class='page'>
				<a href='/admin/feedback' onclick="return showEntry('<?php echo $entry['id']; ?>');">
				<img src='/images/prototype/prefs/toc_collapsed.gif' id='image<?php echo $entry['id']; ?>' alt='View' title='View' />
				<span class='black'><?php echo $entry['page']; ?></span>
				</a>
			</span>
			<br />- <?php echo $entry['author']; ?>
		</div>
		<div id='feedback<?php echo $entry['id']; ?>' class='main'>
			<?php echo nl2br($entry['comment']); ?>
		</div>
	</div>
	<script type='text/javascript'>document.getElementById('feedback<?php echo $entry['id']; ?>').style.display = 'none';</script>
	<?php } ?>