<div style="float: right; width: 100%; text-align: center; font-size: 12px; background-color: 
		<?php
			if ($class === 'error')
				echo '#FF0000';
			elseif ($class === 'warning')
				echo '#FF8000';
			elseif ($class === 'information')
				echo '#FFFF00';
			elseif ($class === 'success')
				echo '#00FF00';
			else
				echo '#808080';
		?>
	;">
	<?php echo $text ?>
</div>