<?php /*
This is very much just a bodge to get something working
(it looks awful and uses a table)
*/ ?>
<TABLE borderwidth="0" width="100%"><TR><TD bgcolor="
	<?php
		if ($class === 'error')
			echo '#FF0000';
		elseif ($class === 'warning')
			echo '#FF8000';
		elseif ($class === 'information')
			echo '#808080';
		else
			echo '#808080';
	?>
">
<?php echo $type; ?><br/>
<?php echo $text; ?>
</TD></TR></TABLE>