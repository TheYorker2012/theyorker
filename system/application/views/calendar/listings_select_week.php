<?php /*
input:
	links = array [
				'prev_term'
				'this_term'
				'next_term'
			]
	term = array [
				'name' => term name e.g. Autumn term
			]
	weeks = array [
				arrays [
					'link' => url to link to
					'name' => e.g. Week 1
					'events' => e.g. 12
					'select' => TRUE/FALSE
					'start_date' => e.g. 'Jan 5th'
				]
				...
			]

jh559: I reckon a bit of AJAX (or AJA*) would go well here for updating the
	weeks when the next term link is clicked.
*/ ?>
<table width="150">

<?php foreach ($weeks as $week) { ?>
	<tr>
		<td class="<?php
		if ($week['heading'])
			echo 'calendarweekheading';
		else if ($week['select'])
			echo 'calendarweekselect';
		else
			echo 'calendarweek';
		?>" onclick = "location.href='<?php echo $week['link']?>';">
			<?php echo $week['name'].
			(empty($week['start_date'])?'':' ('.$week['start_date'].')'); ?>
		</td>
	</tr>
<?php } ?>

	<tr>
		<td align="center" height="40" valign="middle">
			<a href="<?php echo $links['prev_term']; ?>">&lt;&lt;</a>
			<a href="<?php echo $links['this_term']; ?>"><?php echo $term['name']; ?></a>
			<a href="<?php echo $links['next_term']; ?>">&gt;&gt;</a> </td>
	</tr>
	
</table>