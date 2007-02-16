	<script type='text/javascript' src='/javascript/prototype.js'></script>
	<script type='text/javascript' src='/javascript/scriptaculous.js?load=effects'></script>
	<script type='text/javascript'>
	<?php if ($editable) { ?>
	function deleteEntry (id) {
		document.getElementById('feedback' + id).innerHTML = "<div class='ajax_loading'><img src='/images/prototype/prefs/loading.gif' alt='Deleting' title='Deleting' /> Deleting Entry</div>";
		xajax_deleteEntry(id);
		return false;
	}
	<?php } ?>
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

	<div class='blue_box' style='width: auto;'>
		<h2><?php if (!$editable) { echo 'Deleted '; } ?>Feedback Entries</h2>
		<a href='/admin/feedback'><span id='new_entries'><?php echo $new_entries; ?></span> new feedback entries</a><br />
		<a href='/admin/feedback/deleted'><span id='deleted_entries'><?php echo $deleted_entries; ?></span> deleted feedback entries</a>
	</div>

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
			<div style='text-align: center;'><br />
				<?php if ($editable) { ?>
				<a href='/admin/feedback' onclick="return deleteEntry('<?php echo $entry['id']; ?>');"><span class='blue' style='float:right'>[ Delete Entry ]</span></a>
				<?php } ?>
				<span class='form'><button class='button' style='float:none;' onclick="return showEntry('<?php echo $entry['id']; ?>');">Close</button></span>
			</div>
		</div>
	</div>
	<script type='text/javascript'>document.getElementById('feedback<?php echo $entry['id']; ?>').style.display = 'none';</script>
	<?php } ?>