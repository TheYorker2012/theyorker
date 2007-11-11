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
					'old' => TRUE/FALSE (in the past?)
					'select' => TRUE/FALSE (part of current selection?)
					'start_date' => e.g. 'Jan 5th'
				]
				...
			]
*/ ?>
<table width="150">

	<tr>
		<td align="center" height="40" valign="middle">
			<a href="<?php echo $links['prev_term']; ?>">&lt;&lt;</a>
			<?php
				if (!empty($links['this_term']))
					echo '<a href="'.$links['this_term'].'">'.$term['name'].'</a>';
				else
					echo $term['name'];
			?>
			<a href="<?php echo $links['next_term']; ?>">&gt;&gt;</a>
		</td>
	</tr>

<?php foreach ($weeks as $week) { ?>
	<tr>
		<td class="<?php
		if ($week['heading']) {
			echo 'calendarweekheading';
		} else {
			echo 'calendarweek';
			if ($week['select'])
				echo 'select';
			if ($week['old'])
				echo 'old';
		}
		?>" onclick = "location.href='<?php echo $week['link']?>';">
			<?php echo $week['name'].
			(empty($week['start_date'])?'':' ('.$week['start_date'].')'); ?>
		</td>
	</tr>
<?php } ?>
	
</table>